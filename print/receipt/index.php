<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['id'])) {
  session_start();
  require '../../includes/db-config.php';
  require '../../includes/helpers.php';

  $id = intval($_GET['id']);
  $duration = $_GET['duration'];

// echo "SELECT Type, Student_ID, Transaction_ID,University_ID FROM Student_Ledgers  WHERE ID =$id AND Duration = $duration";die;
  $get_type = $conn->query("SELECT Type, Student_ID, Transaction_ID,University_ID FROM Student_Ledgers  WHERE ID =$id AND Duration = $duration");
  if ($get_type->num_rows == 0){
    echo "No record found";die();
  }
    $typeArr = $get_type->fetch_assoc();
    $type = $typeArr['Type'];
    $student_id = $typeArr['Student_ID'];
    $transaction_ID = $typeArr['Transaction_ID'];


    if ($type == 3) { // wallet
      $student = $conn->query("SELECT Universities.Short_Name as University,Sub_Courses.Name as Sub_Cour_name, Students.Duration as no_semester, Courses.Short_Name as Course ,DATE_FORMAT(Students.DOB, '%d-%m-%Y') as DOB,RIGHT(CONCAT('000000', Wallet_Payments.ID), 6) as ID , Students.ID as Student_Table_ID, Wallet_Payments.Bank, Wallet_Payments.Amount  AS total_amount,Wallet_Payments.Payment_Mode,Wallet_Invoices.Amount AS Invoiced_Amount ,Wallet_Payments.Transaction_ID,Wallet_Payments.Gateway_ID,Wallet_Payments.Type,Wallet_Invoices.Duration,CONCAT(IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ' . ', TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) as Unique_ID,DATE_FORMAT(Wallet_Invoices.Created_At, '%d-%m-%Y') as Created_At,Wallet_Payments.Transaction_Date FROM Students LEFT JOIN Wallet_Invoices ON Students.ID = Wallet_Invoices.Student_ID LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID LEFT JOIN Student_Ledgers ON Wallet_Payments.Transaction_ID=Student_Ledgers.Transaction_ID   WHERE Wallet_Invoices.Student_ID = $student_id");
    } else {
      $student = $conn->query("SELECT Universities.Short_Name as University,Sub_Courses.Name as Sub_Cour_name, Students.Duration as no_semester, Courses.Short_Name as Course ,DATE_FORMAT(Students.DOB, '%d-%m-%Y') as DOB,RIGHT(CONCAT('000000', Payments.ID), 6) as ID , Students.ID as Student_Table_ID, Payments.Bank, Payments.Amount  AS total_amount,Payments.Payment_Mode,Invoices.Amount AS Invoiced_Amount ,Payments.Transaction_ID,Payments.Gateway_ID,Payments.Type,Invoices.Duration,CONCAT(IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ' . ', TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) as Unique_ID,DATE_FORMAT(Invoices.Created_At, '%d-%m-%Y') as Created_At,Payments.Transaction_Date FROM Students LEFT JOIN Invoices ON Students.ID = Invoices.Student_ID LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID  LEFT JOIN Student_Ledgers ON Payments.Transaction_ID=Student_Ledgers.Transaction_ID  WHERE Invoices.Student_ID = $student_id");
    }

    // $student = $conn->query("SELECT Universities.Short_Name as University, Sub_Courses.Name as Sub_Cour_name, Students.Duration as no_semester, Courses.Short_Name as Course, DATE_FORMAT(Students.DOB, '%d-%m-%Y') as DOB, RIGHT(CONCAT('000000', Payments.ID), 6) as ID, RIGHT(CONCAT('000000', Students.ID), 6) as Student_Table_ID, Students.Unique_ID as Student_ID, Payments.Bank, Payments.Amount,Payments.Payment_Mode,JSON_UNQUOTE(JSON_EXTRACT(Student_Ledgers.Fee,'$.Paid'))AS Invoiced_Amount,Payments.Transaction_ID,Payments.Gateway_ID,Payments.Type,Student_Ledgers.Duration,CONCAT(IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), '  .  ', TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) as Unique_ID,DATE_FORMAT(Student_Ledgers.Created_At, '%d-%m-%Y') as Created_At,Payments.Transaction_Date FROM Student_Ledgers  LEFT JOIN Payments ON Student_Ledgers.Transaction_ID=Payments.Transaction_ID LEFT JOIN Students ON Student_Ledgers.Student_ID=Students.ID LEFT JOIN Sub_Courses ON
    // Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID WHERE Student_Ledgers.ID= $id AND Student_Ledgers.Duration = $duration");
    $details = $student->fetch_assoc();
   

    $student_id = !empty($details['Student_ID']) ? $details['Student_ID'] : $details['Student_Table_ID'];

    $ledgerSummary = getLedgerSummary($conn, (int) $details['Student_Table_ID']);
    $ledgerSummary = !empty($ledgerSummary) ? json_decode($ledgerSummary, true) : array('totalFee' => 0, 'totalRemitted' => 0, 'totalBalance' => 0);

    // Accountant
    $accountant = $conn->query("SELECT UPPER(`Name`) as Name FROM Users WHERE `Role` = 'Accountant'");
    if ($accountant->num_rows > 0) {
      $accountant = $accountant->fetch_assoc();
      $accountant = $accountant['Name'];
    } else {
      $accountant = 'Accountant';
    }


    $details['Amount'] = abs($details['Invoiced_Amount']);  // kp-17-9
    // $balance = $ledgerSummary['totalFee'] - $details['Amount']; // kp-17-9
    $balance = $details['Amount'] - $details['Amount'];

    // echo "<pre>"; print_r($details);
    require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

    $pdf = new Fpdi();

    $pdf->SetTitle('Fee Receipt');
    $pageCount = $pdf->setSourceFile('receipt.pdf');
    $pdf->SetFont('Times', 'B', 12);

    // Page 1
    $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 210);

    $pdf->SetXY(28, 53.5);
    $pdf->Write(1, $details['Unique_ID']);

    $pdf->SetXY(47, 59.5);
    $pdf->Write(1, $details['DOB']);

    $pdf->SetXY(33.5, 65.8);
    $pdf->Write(1, $details['Course'] . ' ' . $details['Sub_Cour_name'] . ' ( ' . $duration . ' Semester )');

    $pdf->SetXY(148, 53.5);
    $pdf->Write(1, $details['ID']);

    $pdf->SetXY(135, 59.5);
    $pdf->Write(1, $details['Created_At']);

    $pdf->SetXY(148, 65.8);
    $pdf->Write(1, $details['University']);

    $pdf->SetXY(18, 83.5);
    $pdf->Write(1, 'COURSE FEE');

    $pdf->SetXY(172, 83.5);
    $pdf->Write(1, $details['Amount']);

    $pdf->SetXY(70, 106);
    $pdf->Write(1, 'TOTAL AMOUNT');

    $pdf->SetXY(172, 106);
    $pdf->Write(1, $details['Amount']);

    $pdf->SetFont('Times', 'B', 9.5);

    $pdf->SetXY(16, 111.6);
    $pdf->Write(1, 'Total Fee : ' . $ledgerSummary['totalFee']);

    $pdf->SetXY(50, 111.6);
    $pdf->Write(1, 'Total Remitted Fee : ' . $details['Amount']);

    $pdf->SetXY(100, 111.6);
    $pdf->Write(1, 'Balance Fee : ' . $balance);

    $pdf->SetFont('Times', '', 12);

    $amountInWords = ucwords(strtolower(numberTowords(($details['Amount']))));

    $pdf->SetXY(16, 116.6);
    $pdf->Write(1, $amountInWords);

    $pdf->SetXY(16, 124);
    $pdf->Write(1, 'By ' . $details['Payment_Mode']);

    $pdf->SetXY(110, 124);
    $pdf->Write(1, 'Txn. ID ' . $details['Gateway_ID']);

    $pdf->SetXY(16, 132);
    $pdf->Write(1, 'Pay At ' . strtoupper(strtolower($details['Bank'])));

    $pdf->SetXY(110, 132);
    // $pdf->Write(1, 'Txn. No ' . strtoupper(strtolower($details['Transaction_ID'])));
    $pdf->Write(1, 'Txn. No ' . strtoupper(strtolower($transaction_ID)));


    $pdf->SetFont('Times', 'B', 11);

    $pdf->SetXY(159, 144);
    $pdf->Write(1, $accountant);

    $pdf->SetFont('Times', 'B', 12);

    $pdf->SetXY(28, 171.4);
    $pdf->Write(1, $details['Unique_ID']);

    $pdf->SetXY(47, 177.4);
    $pdf->Write(1, $details['DOB']);

    $pdf->SetXY(33.5, 183.8);
    $pdf->Write(1, $details['Course']);

    $pdf->SetXY(148, 171.4);
    $pdf->Write(1, $details['ID']);

    $pdf->SetXY(135, 177.4);
    $pdf->Write(1, $details['Created_At']);

    $pdf->SetXY(148, 183.8);
    $pdf->Write(1, $details['University']);

    $pdf->SetXY(18, 201.4);
    $pdf->Write(1, 'COURSE FEE');

    $pdf->SetXY(172, 201.4);
    $pdf->Write(1, $details['Amount']);

    $pdf->SetXY(70, 224.4);
    $pdf->Write(1, 'TOTAL AMOUNT');

    $pdf->SetXY(172, 224.4);
    $pdf->Write(1, $details['Amount']);

    $pdf->SetFont('Times', '', 12);

    $pdf->SetXY(16, 229.5);
    $pdf->Write(1, $amountInWords);

    $pdf->SetXY(16, 237);
    $pdf->Write(1, 'By ' . $details['Payment_Mode']);

    $pdf->SetXY(110, 237);
    $pdf->Write(1, 'Txn. ID ' . $details['Gateway_ID']);

    $pdf->SetXY(16, 245);
    $pdf->Write(1, 'Pay At ' . strtoupper(strtolower($details['Bank'])));

    $pdf->SetXY(110, 245);
    // $pdf->Write(1, 'Txn. No ' . strtoupper(strtolower($details['Transaction_ID'])));
    $pdf->Write(1, 'Txn. No ' . strtoupper(strtolower($transaction_ID)));


    $pdf->SetFont('Times', 'B', 11);

    $pdf->SetXY(159, 261);
    $pdf->Write(1, $accountant);

    $pdf->Output('D', $student_id . '_' . $details['Transaction_ID'] . '_Fee Receipt.pdf');
  
}
