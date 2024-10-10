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
$university_id = intval($_POST['university_id']);

if(isset($columnSortOrder)){
    $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
    $orderby = "ORDER BY Fee_Structures.ID ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Fee_Structures.Name like '%".$searchValue."%' OR Applicables.Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Fee_Structures WHERE University_ID = $university_id");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Fee_Structures.ID) as filtered FROM Fee_Structures LEFT JOIN Fee_Applicables ON Fee_Structures.Fee_Applicable_ID = Fee_Applicables.ID WHERE University_ID = $university_id $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Fee_Structures.ID, Fee_Structures.Name, IF(Sharing=1, 'Yes', 'No') as Sharing, IF(Is_Constant=1, 'Yes', 'No') as Is_Constant, Status, Fee_Applicables.Name as Applicable FROM Fee_Structures LEFT JOIN Fee_Applicables ON Fee_Structures.Fee_Applicable_ID = Fee_Applicables.ID WHERE Fee_Structures.University_ID = $university_id $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "Name" => $row["Name"],
    "Sharing" => $row["Sharing"],
    "Is_Conctant" => $row["Is_Constant"],
    "Status" => $row["Status"],
    "Applicable" => $row["Applicable"],
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
