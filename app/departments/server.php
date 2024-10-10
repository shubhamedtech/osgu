<?php
## Database configuration
include '../../includes/db-config.php';
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
  $orderby = "ORDER BY Departments.ID ASC";
}

// Admin Query
$query = " AND Departments.University_ID = ".$_SESSION['university_id'];


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Departments.Name like '%".$searchValue."%' OR CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Departments WHERE ID IS NOT NULL $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Departments.ID) as filtered FROM Departments LEFT JOIN Universities ON Departments.University_ID = Universities.ID WHERE Departments.ID IS NOT NULL $searchQuery $query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Departments.`ID`, Departments.`Name`, Departments.`Status`, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Departments LEFT JOIN Universities ON Departments.University_ID = Universities.ID WHERE Departments.ID IS NOT NULL $searchQuery $query $orderby LIMIT ".$row.",".$rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {

    $data[] = array( 
      "Name" => $row["Name"],
      "University" => $row["University"],
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
