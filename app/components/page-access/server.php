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

$lms_condition = " AND Pages.Type <> 'LMS'";
$has_lms = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Has_LMS = 1");
if($has_lms->num_rows>0){
  $lms_condition = "";
}

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Pages.Type, Pages.Name ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Pages.Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Pages WHERE Pages.ID IS NOT NULL $lms_condition");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Pages WHERE Pages.ID IS NOT NULL $searchQuery $lms_condition");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Pages.ID, Pages.Name, Pages.Type, Page_Access.Center, Page_Access.Inhouse, Page_Access.Sub_Center, Page_Access.Student FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID AND Page_Access.University_ID = $university_id WHERE Pages.ID IS NOT NULL $searchQuery $lms_condition $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array( 
    "Name" => $row["Name"]." (".$row['Type'].")",
    "Inhouse" => $row["Inhouse"],
    "Center" => $row["Center"],
    "Sub_Center" => $row["Sub_Center"],
    "Student" => $row["Student"],
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
