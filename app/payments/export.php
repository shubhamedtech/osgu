<?php
if (isset($_GET['type'])) {
  session_start();
  include '../../includes/db-config.php';
  require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $type = intval($_GET['type']);

  $header = array('Transaction ID', 'Gateway ID', 'Mode', 'Bank Name', 'Total Amount', 'Student Name', 'Student ID', 'Amount', 'Date', 'Status', 'Sub Center Name', 'Sub Center Code', 'Center Name', 'Center Code', 'Type');

  $finalData[] = $header;

  $statusQuery = $type == 2 ? " AND Payments.Status = 1" : "";

  $role_query = str_replace('{{ table }}', 'Payments', $_SESSION['RoleQuery']);
  $role_query = str_replace('{{ column }}', 'Added_By', $role_query);

  $filterQueryUser = "";
  if (isset($_SESSION['filterByUser'])) {
    $filterQueryUser = $_SESSION['filterByUser'];
  }

  $filterByDate = "";
  if (isset($_SESSION['filterByDate'])) {
    $filterByDate = $_SESSION['filterByDate'];
  }

  $role_query .= $filterQueryUser . $filterByDate;

  $payments = $conn->query("SELECT Payments.Created_At, Payments.Transaction_Date, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount as InvoicedAmount, Payments.Amount, Payments.File, Payments.Payment_Mode, Payments.Bank, Payments.Added_For, Payments.Added_By, Payments.Status, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University, Students.Unique_ID, TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)) as Student_Name, Users.Name as UserName, Users.Code, Users.Role FROM Payments LEFT JOIN Universities ON Payments.University_ID = Universities.ID LEFT JOIN Invoices ON Payments.Transaction_ID = Invoices.Invoice_No LEFT JOIN Students ON Invoices.Student_ID = Students.ID LEFT JOIN Users ON Payments.Added_By = Users.ID WHERE Type = $type AND Payments.University_ID = " . $_SESSION['university_id'] . " $statusQuery $role_query ORDER BY Payments.ID DESC");
  while ($payment = $payments->fetch_assoc()) {

    $amount = !empty($payment['InvoicedAmount']) ? (-1) * $payment['InvoicedAmount'] : $payment['Amount'];
    $payment['Transaction_Date'] = !empty($payment['Transaction_Date']) ? $payment['Transaction_Date'] : $payment['Created_At'];
    $status = $payment['Status'] == 0 ? 'Pending' : ($payment['Status'] == 1 ? 'Approved' : 'Rejected');
    $payment['Role'] = $payment['Role'] == 'Counsellor' ? 'National Coordinator' : ($payment['Role'] == 'Sub-Counsellor' ? 'Regional Coordinator' : $payment['Role']);

    $data = array(
      $payment['Transaction_ID'],
      $payment['Gateway_ID'],
      $payment['Payment_Mode'],
      $payment['Bank'],
      sprintf("%.2f", $payment['Amount']),
      $payment['Student_Name'],
      $payment['Unique_ID'],
      sprintf("%.2f", $amount),
      date("d/m/Y", strtotime($payment['Transaction_Date'])),
      $status
    );

    if ($payment['Role'] == 'Sub-Center') {
      $center = $conn->query("SELECT Users.Name, Users.Code, Center_SubCenter.Center FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Center = Users.ID WHERE Sub_Center = " . $payment['Added_By']);
      $center = $center->fetch_assoc();

      $nc['Name'] = $nc['Code'] = "";
      $coordinator = $conn->query("SELECT Users.Name, Users.Code FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Cournsellor_ID = Users.ID WHERE Code = " . $center['Center'] . " AND University_ID = " . $_SESSION['university_id']);
      if ($coordinator->num_rows > 0) {
        $nc = $coordinator->fetch_assoc();
      }

      array_push($data, $center['Name'], $center['Code'], $nc['Name'], $nc['Code'], $payment['Role']);
    } else {
      $nc['Name'] = $nc['Code'] = "";

      $coordinator = $conn->query("SELECT Users.Name, Users.Code FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID WHERE Alloted_Center_To_Counsellor.Code = " . $payment['Added_By'] . " AND University_ID = " . $_SESSION['university_id']);
      if ($coordinator->num_rows > 0) {
        $nc = $coordinator->fetch_assoc();
      }

      array_push($data, $payment['UserName'], $payment['Code'], $nc['Name'], $nc['Code'], $payment['Role']);
    }

    $finalData[] = $data;
  }

  $xlsx = SimpleXLSXGen::fromArray($finalData)->downloadAs('Payments_' . date("d_m_Y_h_m_i") . '.xlsx');
}
