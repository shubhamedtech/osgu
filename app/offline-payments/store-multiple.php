<?php
if (isset($_POST['amount']) && isset($_POST['transaction_id']) && isset($_POST['ids'])) {
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';
  session_start();

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG", "pdf", "PDF");
  $file_folder = '../../uploads/offline-payments/';

  $ids = mysqli_real_escape_string($conn, $_POST['ids']);
  $ids = explode("|", $ids);
  $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
  $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
  $transaction_id = strtoupper(strtolower(uniqid()));
  $gateway_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);
  $amount = mysqli_real_escape_string($conn, $_POST['amount']);
  $transaction_date = mysqli_real_escape_string($conn, $_POST['transaction_date']);
  $transaction_date = date("Y-m-d", strtotime($transaction_date));
  $student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';

  $check = $conn->query("SELECT ID FROM Payments WHERE Transaction_ID = '$gateway_id' AND Type = 1 AND Payment_Mode != 'Cash'");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Transaction ID already exists!']);
    exit();
  }

  $file = NULL;
  if ($payment_type != 'Cash') {
    if (isset($_FILES["file"]['tmp_name']) && $_FILES["file"]['tmp_name'] != '') {
      $file = mysqli_real_escape_string($conn, $_FILES["file"]['name']);
      $tmp_name = $_FILES["file"]["tmp_name"];
      $file_extension = pathinfo($file, PATHINFO_EXTENSION);
      $file = uniqid() . "." . $file_extension;
      if (in_array($file_extension, $allowed_file_extensions)) {
        if (!move_uploaded_file($tmp_name, $file_folder . $file)) {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload file!']);
          exit();
        } else {
          $file = str_replace('../..', '', $file_folder) . $file;
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'File should be Image or PDF!']);
        exit();
      }
    } else {
      echo json_encode(['status' => 400, 'message' => 'File is required!']);
      exit();
    }
  }

  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];
    $balance = balanceAmount($conn, $id, $duration);
    $add = $conn->query("INSERT INTO Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, $duration, " . $_SESSION['university_id'] . ", '$transaction_id', $balance)");
  }

  if ($add) {
    $add = $conn->query("INSERT INTO Payments (Type, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID) VALUES (1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $_SESSION['ID'] . ", '$file', " . $_SESSION['university_id'] . ")");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Payment added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
