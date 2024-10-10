<?php
## Database configuration
include '../../../includes/db-config.php';
session_start();
$filename = $_SESSION['Categories']."_".date('d_m_Y_h_i_s').".csv";
$f = fopen('php://output', 'w');
ob_end_clean (); // Clear memory
ob_start();
## Read value
$delimiter = ",";

$fields = array('S.No.', 'Name', $_SESSION['Departments'], 'Status');

fputcsv($f, $fields, $delimiter);

if(isset($_GET['value'])){
  $searchValue = mysqli_real_escape_string($conn,$_GET['value']); // Search value
}else{
  $searchValue = '';
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Categories.Name LIKE '".$searchValue."' OR Departments.Name LIKE '".$searchValue."'";
}

## Fetch records
$result_record = $conn->query("SELECT Categories.Name, Departments.Name as Departments, IF(Categories.Status=1, 'Active', 'Inactive') as Status FROM Categories LEFT JOIN Departments ON Categories.Department_ID = Departments.ID WHERE Categories.ID IS NOT NULL $searchQuery ORDER BY Categories.ID DESC");
$counter = 1;
while ($row = $result_record->fetch_assoc()) {
    $lineData = array($counter++, $row['Name'], $row['Departments'], $row['Status']);
    fputcsv($f, $lineData, $delimiter);
}

//set headers to download file rather than displayed
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer
fpassthru($f);
