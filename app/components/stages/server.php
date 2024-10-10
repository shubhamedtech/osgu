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
    $orderby = "ORDER BY Stages.ID ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " AND Stages.Name like '%".$searchValue."%'";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Stages");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Stages WHERE ID IS NOT NULL $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT ID, Name, Status, Is_First, Is_Last, Is_ReEnquired FROM Stages WHERE ID IS NOT NULL $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
      "Name"    => $row["Name"],
      "Status"  => $row["Status"],
      "Is_First" => $row['Is_First'],
      "Is_Last" => $row['Is_Last'],
      "Is_ReEnquired" => $row['Is_ReEnquired'],
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
