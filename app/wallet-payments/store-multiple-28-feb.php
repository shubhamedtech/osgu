<?php
if (isset($_POST['amount']) && isset($_POST['ids'])) {
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';
  session_start();

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG", "pdf", "PDF");
  $file_folder = '../../uploads/offline-payments/';

  $ids = mysqli_real_escape_string($conn, $_POST['ids']);
  $ids = explode("|", $ids);

  //$bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
  //$payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
  //$gateway_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);

  $transaction_id = strtoupper(strtolower(uniqid()));
  $file = $transaction_id;
  $payment_type = "Wallet";
  $bank_name = "Wallet";
  $gateway_id = $transaction_id;
  $amount = mysqli_real_escape_string($conn, $_POST['amount']);
  $transaction_date = $transaction_date = date("Y-m-d");
  $student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';

  $check = $conn->query("SELECT ID FROM Wallet_Payments WHERE Transaction_ID = '$gateway_id' AND Type = 3 AND Payment_Mode != 'Cash'");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Transaction ID already exists!']);
    exit();
  }

  $amount_update = 0;
  $amount_check = $conn->query("SELECT sum(Amount) as total_amount FROM Wallets WHERE Added_By = " . $_SESSION['ID'] . " AND University_ID = " . $_SESSION['university_id'] . " ");
  if ($amount_check->num_rows > 0) {
    $amount_check = $amount_check->fetch_assoc();
    $amount_update = $amount_check['total_amount'];
  } else {
    echo json_encode(['status' => 400, 'message' => 'Please recharge wallet first!']);
    exit();
  }

  if ($amount_update == 0) {
    echo json_encode(['status' => 400, 'message' => 'Please recharge wallet first!']);
    exit();
  }

  if ($amount_update < $amount) {
    echo json_encode(['status' => 400, 'message' => 'Please recharge wallet first!']);
    exit();
  }

  // GET center id
  if ($_SESSION['Role'] == 'Sub-Center') {
    $subcenterId = $_SESSION['ID'];
    $center_id = getCenterIdFunc($conn, $subcenterId);
    $center_sub_coursesArr = $conn->query("SELECT Fee, Course_ID, Sub_Course_ID FROM Center_Sub_Courses WHERE User_ID = $center_id AND University_ID=" . $_SESSION['university_id'] . "");
    while ($centerCourseFee = $center_sub_coursesArr->fetch_assoc()) {
      // $subCoursesNameQuery = $conn->query("SELECT Name FROM Sub_Courses WHERE ID = ".$centerCourseFee['Sub_Course_ID']."");
      // $subCoursesNameArr= $subCoursesNameQuery->fetch_assoc();
      // $subCourseName[] = $subCoursesNameArr["Name"];
      $feeArr[] = $centerCourseFee;


    }
    // echo"<pre>"; print_r($_SESSION); die;



  }

  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];
    $balance = balanceAmount($conn, $id, $duration);
    
    // if ($_SESSION['Role'] == 'Sub-Center') {

    //   $added_for_column = ", `Added_For`";
    //   $student_id = base64_decode($id);
    //   $student_ids = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $student_id));
    //   $added_for_value = "," . $student_ids;

    //   $studentCoursQuery = $conn->query("SELECT Added_For, Course_ID,Sub_Course_ID, University_ID FROM Students WHERE ID = $id");
    //   $studentCourseArr = $studentCoursQuery->fetch_assoc();

    //   $center_id = getCenterIdFunc($conn, $studentCourseArr['Added_For']);
    //   $center_sub_coursesArr = $conn->query("SELECT Fee, Course_ID, Sub_Course_ID FROM Center_Sub_Courses WHERE User_ID = $center_id AND Course_ID =" . $studentCourseArr['Course_ID'] . " AND Sub_Course_ID =" . $studentCourseArr['Sub_Course_ID'] . " AND University_ID=" . $studentCourseArr['University_ID'] . "");
    //   $centerCourseFee = $center_sub_coursesArr->fetch_assoc();
    //   $centerFee = $centerCourseFee['Fee'];
    //   $center_wallet_amount = $balance - $centerFee; // center wallet amount 

    //   $payment_type = "Settelment By Sub-Center";

    //   $add_wallet = $conn->query("INSERT INTO Wallets (Type, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, 
    //    File, University_ID $added_for_column) VALUES (1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$center_wallet_amount', 
    //  '$payment_type',  " . $center_id . ", '$file', " . $_SESSION['university_id'] . " $added_for_value)");

    // }

    $add = $conn->query("INSERT INTO Wallet_Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, '$duration', " . $_SESSION['university_id'] . ", '$transaction_id', $balance)");
    $conn->query("UPDATE Students SET Process_By_Center = now() WHERE ID = $id ");
  }

  if ($add) {
    $add = $conn->query("INSERT INTO Wallet_Payments (Type, Status, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID) VALUES (3, 1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $_SESSION['ID'] . ", '$file', " . $_SESSION['university_id'] . ")");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Payment added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
