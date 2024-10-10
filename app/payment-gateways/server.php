<?php
## Database configuration
include '../../includes/db-config.php';
include '../../includes/helpers.php';
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
  $orderby = "ORDER BY Payment_Gateways.ID ASC";
}

## Search 
$searchQuery = "";

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Payment_Gateways");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$totalRecordwithFilter = $totalRecords;

## Fetch records
$result_record = "SELECT Payment_Gateways.`ID`, Payment_Gateways.Type, Access_Key, Secret_Key, Payment_Gateways.Status, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University_ID FROM Payment_Gateways LEFT JOIN Universities ON Payment_Gateways.University_ID = Universities.ID $orderby LIMIT " . $row . "," . $rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {

  $data[] = array(
    "University_ID" => $row["University_ID"],
    "Type" => $row['Type'] == 1 ? 'Easebuzz' : ($row['Type'] == 2 ? 'Razorpay' : ''),
    "Access_Key" => stringToSecret($row['Access_Key']),
    "Secret_Key" => stringToSecret($row['Secret_Key']),
    "Status"  => $row["Status"],
    "ID"      => $row["ID"],
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
