<?php
## Database configuration
include '../../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Downloads.ID ASC";
}

$category_query = " AND Downloads.Category = 'Date-Sheets'";

$university_query  = '';
$session_query = '';
if($_SESSION['Role']!='Administrator'){
  $university_query = " AND Downloads.University_ID = ".$_SESSION['university_id'];
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Downloads.Name like '%".$searchValue."%' OR Universities.Short_Name like '%".$searchValue."%' OR Universities.Name like '%".$searchValue."%' OR Universities.Vertical  like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Downloads WHERE ID IS NOT NULL $category_query $university_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Downloads.ID) as filtered FROM Downloads LEFT JOIN Universities ON Downloads.University_ID = Universities.ID WHERE Downloads.ID IS NOT NULL $category_query $university_query $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Downloads.`ID`, Downloads.`Name`, Downloads.`File`, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Downloads LEFT JOIN Universities ON Downloads.University_ID = Universities.ID WHERE Downloads.ID IS NOT NULL $category_query $university_query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$downloads = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($downloads)) {

  $extension = explode('.', $row['File']);
  $extension = end($extension);

  $data[] = array( 
    "Name" => $row['Name'],
    "File" => $row['File'],
    "University" => $row['University'],
    "Extension" => $extension,
    "ID" => $row["ID"],
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
