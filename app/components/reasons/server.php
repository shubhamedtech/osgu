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
    $orderby = "ORDER BY Reasons.ID ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " AND (Reasons.Name LIKE '%".$searchValue."%' or Stages.Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Reasons");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Reasons.ID) as filtered FROM Reasons LEFT JOIN Stages ON Reasons.Stage_ID = Stages.ID WHERE Reasons.ID IS NOT NULL $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Reasons.ID, Reasons.Name, Reasons.Status, Stages.Name as Stages FROM Reasons LEFT JOIN Stages ON Reasons.Stage_ID = Stages.ID WHERE Reasons.ID IS NOT NULL $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
      "Name"    => $row["Name"],
      "Stages" => $row['Stages'],
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
