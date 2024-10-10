<?php
if (isset($_POST['ids'])) {
  require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
  require $_SERVER['DOCUMENT_ROOT'] . '/includes/helpers.php';
  session_start();

  $ids = isset($_POST['ids']) ? array_filter(explode(",", $_POST['ids'])) : array();
  $examSession = $_SESSION['active_rr_session_id'];

  if (empty($ids) || empty($examSession)) {
    $conn->close();
    exit(json_encode(['status' => 400, 'message' => 'Please select student!']));
  }

  $totalFee = array();
  include 'calculate-fee.php';

  $amounts = $conn->query("SELECT sum(Amount) as total_amt FROM Wallets WHERE Added_By = " . $_SESSION['ID'] . " AND Status = 1 AND University_ID = " . $_SESSION['university_id']);
  $amounts = $amounts->fetch_assoc();

  //Debit Amount
  $debited_amount = 0;
  $debit_amts = $conn->query("SELECT sum(Amount) as debit_amt FROM Wallet_Payments WHERE Added_By = " . $_SESSION['ID'] . " AND Type = 3 AND University_ID = " . $_SESSION['university_id']);
  if ($debit_amts->num_rows > 0) {
    $debit_amt = $debit_amts->fetch_assoc();
    $debited_amount = $debit_amt['debit_amt'];
  }

  $walletBalance = $amounts['total_amt'] - $debited_amount;

  if (array_sum($totalFee) <= $walletBalance) {
    // Add in Wallet Payments
    $txnId = strtoupper(uniqid());
    $txnDate = date('Y-m-d');
    $add = $conn->query("INSERT INTO Wallet_Payments (`Type`, `Transaction_Date`, `Amount`, `Transaction_ID`, `Gateway_ID`, `Bank`, `Payment_Mode`, `Added_By`, `Source`, `Status`, `University_ID`) VALUES (3, '$txnDate', '" . array_sum($totalFee) . "', '$txnId', '$txnId', 'Re-Reg Fee for " . count($totalFee) . " Students', 'Wallet', " . $_SESSION['ID'] . ", 'Re-Reg', 1, ".$_SESSION['university_id'].")");
    if ($add) {
      $paymentId = $conn->insert_id;

      foreach ($totalFee as $studentId => $amount) {
        $student = $conn->query("SELECT Duration, Course_ID, Sub_Course_ID, University_ID FROM Students WHERE ID = $studentId");
        $student = $student->fetch_assoc();
        $rrSem = $student['Duration'] + 1;
        $addInRR = $conn->query("INSERT INTO Re_Registrations (`Student_ID`, `Duration`, `Exam_Session_ID`, `University_ID`, `Amount`, `Payment_ID`, `Added_By`, `Status`, `Payment_From`) VALUES ($studentId, $rrSem, $examSession, " . $_SESSION['university_id'] . ", $amount, $paymentId, " . $_SESSION['ID'] . ", 1, 'Wallet')");
        if ($addInRR) {
          // Add in Wallet Invoice
          $update = $conn->query("UPDATE Students SET Duration = $rrSem WHERE ID = $studentId");
          generateStudentLedger($conn, $studentId);
          $conn->query("INSERT INTO Student_Ledgers (`Student_ID`, `Duration`, `Date`, `University_ID`, `Type`, `Source`, `Fee`, `Status`,`Transaction_ID`) VALUES ($studentId, $rrSem, '".date("Y-m-d")."', ".$_SESSION['university_id'].", 3, 'Wallet', '".json_encode(['Paid'=> array_sum($totalFee)])."', 1, '$txnId')");
          $conn->query("INSERT INTO Wallet_Invoices (`Invoice_No`, `User_ID`, `Student_ID`, `Duration`, `University_ID`, `Amount`) VALUES ('$txnId', " . $_SESSION['ID'] . ", $studentId, $rrSem, " . $_SESSION['university_id'] . ", $amount)");

          if ($_SESSION['Role'] == 'Sub-Center') {
            $center = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = " . $_SESSION['ID']);
            if ($center->num_rows == 0) {
              continue;
            }

            $center = $center->fetch_assoc();

            $courseId = $student['Course_ID'];
            $subCourseId = $student['Sub_Course_ID'];
            $universityId = $student['University_ID'];
            $fee = $conn->query("SELECT Fee FROM Center_Sub_Courses WHERE User_ID = " . $center['Center'] . " AND Course_ID = $courseId AND Sub_Course_ID = $subCourseId AND University_ID = $universityId");
            if ($fee->num_rows == 0) {
              continue;
            }
            $fee = $fee->fetch_assoc();

            // Add Credit to Center
            $creditAmount = $amount - $fee['Fee'];
            $conn->query("INSERT INTO Wallets (`Type`, `Transaction_ID`, `Gateway_ID`, `Bank`, `Payment_Mode`, `Amount`, `Added_By`, `Source`, `Status`, `University_ID`) VALUES (1, '$txnId', '$txnId', 'Wallet', 'Settelment By Sub-Center', '$creditAmount', '".$center['Center']."', 'Re-Reg', 1, ".$_SESSION['university_id'].")");
          }
        }
      }
    }

    if($addInRR){
      echo json_encode(['status'=>200, 'message'=>'Re Reg applied successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 400, 'message' => 'Insufficient wallet balance!']);
  }
}
