<?php
## Database configuration
include '../../../includes/db-config.php';
session_start();
$filename = "Sub_Sources_".date('d_m_Y_h_i_s').".csv";
$f = fopen('php://output', 'w');
ob_end_clean (); // Clear memory
ob_start();
## Read value
$delimiter = ",";

$fields = array('S.No.', 'Name', 'Sources', 'Status');

fputcsv($f, $fields, $delimiter);

if(isset($_GET['value'])){
  $searchValue = mysqli_real_escape_string($conn,$_GET['value']); // Search value
}else{
  $searchValue = '';
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Sub_Sources.Name LIKE '".$searchValue."' OR Sources.Name LIKE '".$searchValue."'";
}

## Fetch records
$result_record = $conn->query("SELECT Sub_Sources.Name, Sources.Name as Sources, IF(Sub_Sources.Status=1, 'Active', 'Inactive') as Status FROM Sub_Sources LEFT JOIN Sources ON Sub_Sources.Source_ID = Sources.ID WHERE Sub_Sources.ID IS NOT NULL $searchQuery ORDER BY Sub_Sources.ID DESC");
$counter = 1;
while ($row = $result_record->fetch_assoc()) {
    $lineData = array($counter++, $row['Name'], $row['Sources'], $row['Status']);
    fputcsv($f, $lineData, $delimiter);
}

//set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer
fpassthru($f);
