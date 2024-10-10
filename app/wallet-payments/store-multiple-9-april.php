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
      $feeArr[] = $centerCourseFee;
    }
    // echo"<pre>"; print_r($_SESSION); die;
  }

  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];
    $balance = balanceAmount($conn, $id, $duration);

    if ($_SESSION['Role'] == 'Sub-Center') {

      $added_for_column = ", `Added_For`";
      $student_id = base64_decode($id);
      $student_ids = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $student_id));
      $added_for_value = "," . $student_ids;

      $studentCoursQuery = $conn->query("SELECT Added_For, Course_ID,Sub_Course_ID, University_ID FROM Students WHERE ID = $id");
      $studentCourseArr = $studentCoursQuery->fetch_assoc();

      $center_id = getCenterIdFunc($conn, $studentCourseArr['Added_For']);
      $center_sub_coursesArr = $conn->query("SELECT Fee, Course_ID, Sub_Course_ID FROM Center_Sub_Courses WHERE User_ID = $center_id AND Course_ID =" . $studentCourseArr['Course_ID'] . " AND Sub_Course_ID =" . $studentCourseArr['Sub_Course_ID'] . " AND University_ID=" . $studentCourseArr['University_ID'] . "");
      if ($center_sub_coursesArr->num_rows > 0) {
        $centerCourseFee = $center_sub_coursesArr->fetch_assoc();
        $centerFee = $centerCourseFee['Fee'];
      } else {
        $centerFee = 0;
      }

      $center_wallet_amount = $balance - $centerFee; // center wallet amount 

      $payment_type = "Settelment By Sub-Center";

      $add_wallet = $conn->query("INSERT INTO Wallets (Type, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, 
         File, University_ID $added_for_column, Status) VALUES (1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$center_wallet_amount', 
       '$payment_type',  " . $center_id . ", '$file', " . $_SESSION['university_id'] . " $added_for_value, 1)");
    }

    $add = $conn->query("INSERT INTO Wallet_Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, '$duration', " . $_SESSION['university_id'] . ", '$transaction_id', $balance)");
    $conn->query("UPDATE Students SET Process_By_Center = now() WHERE ID = $id ");
  }


  if ($add) {

    $add = $conn->query("INSERT INTO Wallet_Payments (Type, Status, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID) VALUES (3, 1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $_SESSION['ID'] . ", '$file', " . $_SESSION['university_id'] . ")");
    $last_insert_id = $conn->insert_id;
    // NEW KP
    $getPayment = $conn->query("SELECT Wallet_Payments.*,Wallet_Invoices.Student_ID  FROM Wallet_Payments LEFT JOIN Wallet_Invoices ON  Wallet_Payments.Transaction_ID = Wallet_Invoices.Invoice_No WHERE Wallet_Payments.Type = 3 AND Wallet_Payments.ID  = $last_insert_id");
    $payment = $getPayment->fetch_assoc();
    $student_id = $payment['Student_ID'];

    // $update = $conn->query("UPDATE Wallet_Payments SET Status = 1, Approved_By = " . $_SESSION['ID'] . ", Approved_On = now() WHERE ID = $last_insert_id");
    // if (!empty($student_id)) {
    //   $student = $conn->query("SELECT Duration, University_ID FROM Students WHERE ID = $student_id");
    //   $student = $student->fetch_assoc();
    //   $add_stu_ledger = $conn->query("INSERT INTO Student_Ledgers (Student_ID, Duration, Date, University_ID, Type, Source, Transaction_ID, Fee, Status) VALUES ($student_id, " . $student['Duration'] . ", '" . date("Y-m-d", strtotime($payment['Transaction_Date'])) . "', " . $student['University_ID'] . ", 3, 'Offline', '" . $payment['Transaction_ID'] . "', '" . json_encode(['Paid' => $payment['Amount']]) . "', 1)");
    //   if (!$add_stu_ledger) {
    //     exit(json_encode(['status' => 400, 'message' => 'Something went wrong!']));
    //   }
    //   $balance = 0;
    //   $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = $student_id AND Status = 1 AND Duration <= " . $student['Duration']);
    //   while ($ledger = $ledgers->fetch_assoc()) {
    //     $debit = $ledger['Type'] == 1 ? $ledger['Fee'] : 0;
    //     if ($ledger['Type'] == 3) {
    //       $fees = json_decode($ledger['Fee'], true);
    //       $fee_val =  reset($fees);
    //     }
    //     $credit = $ledger['Type'] == 3 ? $fee_val : 0;
    //     $balance = ($balance + $credit) - $debit;
    //   }
    //   if ($balance >= 0) {
    //     $conn->query("UPDATE Students SET Payment_Received = now() WHERE ID = $student_id");
    //   }
    // } else {
    $students = $conn->query("SELECT Student_ID, Duration, Amount, University_ID, Created_At FROM Wallet_Invoices WHERE Invoice_No = '" . $payment['Transaction_ID'] . "'");
    while ($student = $students->fetch_assoc()) {
      $add = $conn->query("INSERT INTO Student_Ledgers (Student_ID, Duration, Date, University_ID, Type, Source, Transaction_ID, Fee, Status) VALUES (" . $student['Student_ID'] . ", " . $student['Duration'] . ", '" . date("Y-m-d", strtotime($payment['Transaction_Date'])) . "', " . $student['University_ID'] . ", 3, 'Wallet', '" . $payment['Transaction_ID'] . "', '" . json_encode(['Paid' => (-1) * $student['Amount']]) . "', 1)");
      $update = $conn->query("UPDATE Students SET Process_By_Center = '" . $student['Created_At'] . "' WHERE ID = " . $student['Student_ID']);
      if (!$add) {
        exit(json_encode(['status' => 400, 'message' => 'Something went wrong!']));
      }
      // }
    }

// END KP 
    
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Payment added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
