<?php
if (isset($_POST['amount'], $_POST['transaction_id'], $_POST['ids'])) {
  require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
  session_start();

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG", "pdf", "PDF");
  $file_folder = '../../../uploads/offline-payments/';

  $ids = isset($_POST['ids']) ? array_filter(explode(",", $_POST['ids'])) : array();
  $examSession = $_SESSION['active_rr_session_id'];
    
  if (empty($ids) || empty($examSession)) {
    $conn->close();
    exit(json_encode(['status' => 400, 'message' => 'Please select student!']));
  }

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
          $file = str_replace('../../..', '', $file_folder) . $file;
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

  $added_for_column = "";
  $added_for_value = "";
  $added_by = $_SESSION['ID'];

  $add = $conn->query("INSERT INTO Payments (Type, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, `File`, University_ID, `Source`) VALUES (1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $added_by . ", '$file', " . $_SESSION['university_id'] . ", 'Re-Reg')");
  if ($add) {
    // Add Student for Re-Reg
    $paymentId = $conn->insert_id;
    $totalFee = array();
    include 'calculate-fee.php';
    foreach($totalFee as $studentId => $amount){
      $studentDuration = $conn->query("SELECT Duration FROM Students WHERE ID = $studentId");
      $studentDuration = $studentDuration->fetch_assoc();
      $rrSem = $studentDuration['Duration']+1;
      $conn->query("INSERT INTO Re_Registrations (`Student_ID`, `Duration`, `Exam_Session_ID`, `University_ID`, `Amount`, `Payment_ID`, `Added_By`, `Payment_From`) VALUES ($studentId, $rrSem, $examSession, ".$_SESSION['university_id'].", $amount, $paymentId, $added_by, 'Offline')");
    }

    echo json_encode(['status' => 200, 'message' => 'Payment added successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
