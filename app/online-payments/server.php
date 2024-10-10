<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}

$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Payments.ID DESC";
}

$university_query = $_SESSION['Role'] != 'Administrator' ? " AND Payments.University_ID =" . $_SESSION['university_id'] : '';

$role_query = str_replace('{{ table }}', 'Payments', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'Added_By', $role_query);

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  if (strcasecmp($searchValue, 'approved') == 0) {
    $searchQuery = " AND Status = 1 ";
  }
  if (strcasecmp($searchValue, 'rejected') == 0) {
    $searchQuery = " AND Status = 2 ";
  }
  if (strcasecmp($searchValue, 'pending') == 0) {
    $searchQuery = " AND Status = 0 ";
  } else {
    $searchQuery = " AND (Transaction_ID like '%" . $searchValue . "%' OR Transaction_Date like '%" . $searchValue . "%' OR Amount like '%" . $searchValue . "%' OR Payment_Mode like '%" . $searchValue . "%' OR Universities.Short_Name like '%" . $searchValue . "%' Universities.Name like '%" . $searchValue . "%' OR Universities.Vertical like '%" . $searchValue . "%')";
  }
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(Payments.ID) as allcount FROM Payments WHERE Type = 2 AND Payments.Status = 1 $university_query $role_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Payments.ID) as filtered FROM Payments LEFT JOIN Universities ON Payments.University_ID = Universities.ID WHERE Type = 2 AND Payments.Status = 1 $university_query $searchQuery $role_query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Payments.ID, Payments.Created_At as Transaction_Date, Payments.Transaction_ID, Payments.Gateway_ID, Payments.Amount, Payments.File, Payments.Payment_Mode, Payments.Bank, Payments.Added_For, Payments.Added_By, Payments.Status, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Payments LEFT JOIN Universities ON Payments.University_ID = Universities.ID WHERE Type = 2 AND Payments.Status = 1 $university_query $searchQuery $role_query $orderby LIMIT " . $row . "," . $rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  // Added_For
  $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_By'] . " AND Role = 'Center'");
  if ($user->num_rows == 0) {
    $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row['Added_For']);
  }
  $user = mysqli_fetch_array($user);

  // RM
  $rm['Name'] = "";
  if (!empty($user)) {
    // RM
    $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'] . " WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id']);
    if ($rm->num_rows > 0) {
      $rm = mysqli_fetch_array($rm);
    } else {
      $rm = $user;
    }
  }


  $file_type = "";
  if (!empty($row['File'])) {
    $extension = explode(".", $row['File']);
    $extension = end($extension);
    $file_type = strcasecmp($extension, 'pdf') == 0 ? "pdf" : "image";
  }

  $data[] = array(
    "File" => empty($row['File']) ? '' : $row['File'],
    "File_Type" => $file_type,
    "Transaction_ID" => empty($row['Transaction_ID']) ? '' : $row['Transaction_ID'],
    "Transaction_Date" => empty($row['Transaction_Date']) ? '' : date("d-m-Y", strtotime($row['Transaction_Date'])),
    "Gateway_ID" => empty($row['Gateway_ID']) ? '' : $row['Gateway_ID'],
    "Bank" => empty($row['Bank']) ? '' : $row['Bank'],
    "Amount" => empty($row['Amount']) ? '' : $row['Amount'],
    "Payment_Mode" => empty($row['Payment_Mode']) ? '' : $row['Payment_Mode'],
    "University" => empty($row['University']) ? '' : $row['University'],
    "Center_Code" => $user['Code'],
    "Center_Name" => $user['Name'],
    "RM" => $rm['Name'],
    "Status" => $row['Status'],
    "ID" => $row['ID'],
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
