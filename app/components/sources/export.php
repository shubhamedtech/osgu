<?php
## Database configuration
include '../../../includes/db-config.php';
session_start();
$filename = "Sources_".date('d_m_Y_h_i_s').".csv";
$f = fopen('php://output', 'w');
ob_end_clean (); // Clear memory
ob_start();
## Read value
$delimiter = ",";

$fields = array('S.No.', 'Name', 'Status');

fputcsv($f, $fields, $delimiter);

if(isset($_GET['value'])){
  $searchValue = mysqli_real_escape_string($conn,$_GET['value']); // Search value
}else{
  $searchValue = '';
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND Name LIKE '".$searchValue."'";
}

## Fetch records
$result_record = $conn->query("SELECT Name, IF(Status=1, 'Active', 'Inactive') as Status FROM Sources WHERE ID IS NOT NULL $searchQuery ORDER BY ID ASC");
$counter = 1;
while ($row = $result_record->fetch_assoc()) {
    $lineData = array($counter++, $row['Name'], $row['Status']);
    fputcsv($f, $lineData, $delimiter);
}

//set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer
fpassthru($f);
