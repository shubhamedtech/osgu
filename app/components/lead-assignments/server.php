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
    $orderby = "ORDER BY Assignment_Rules.ID DESC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Assignment_Rules.Name like '%".$searchValue."%' OR Course like '%".$searchValue."%' OR Description like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(DISTINCT(Course)) as allcount FROM Assignment_Rules");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = intval($records['allcount']);

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(DISTINCT(Assignment_Rules.Course)) as filtered FROM Assignment_Rules WHERE Assignment_Rules.ID IS NOT NULL $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = intval($records['filtered']);

## Fetch records
$result_record = "SELECT Assignment_Rules.ID, Assignment_Rules.Name, Assignment_Rules.Description, Assignment_Rules.Course, Assignment_Rules.Status FROM Assignment_Rules WHERE Assignment_Rules.ID IS NOT NULL $searchQuery GROUP BY Course $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  $data[] = array( 
    "Name" => $row["Name"],
    "Course" => $row['Course'],
    "Description" => $row['Description'],
    "Status" => $row['Status'],
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
