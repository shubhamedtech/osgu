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
    $orderby = "ORDER BY Universities.ID ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
    $searchQuery = " AND (Universities.Name like '%".$searchValue."%' OR Universities.Short_Name like '%".$searchValue."%' OR Universities.Vertical like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Universities");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Universities WHERE ID IS NOT NULL $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT `ID`, `Short_Name`, `Vertical`, `Logo`, `Is_B2C`, `Is_Vocational`, `Has_LMS`, `Has_Unique_Center`, `Center_Suffix`, `Has_Unique_StudentID`, `ID_Suffix`, `Max_Character`, `Status` Status FROM Universities WHERE ID IS NOT NULL $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
      "Short_Name" => $row["Short_Name"],
      "Vertical" => $row["Vertical"],
      "Logo" => $row["Logo"],
      "Is_B2C" => $row["Is_B2C"],
      "Is_Vocational" => $row["Is_Vocational"],
      "Has_LMS" => $row["Has_LMS"],
      "Has_Unique_Center" => $row["Has_Unique_Center"],
      "Center_Suffix" => !empty($row["Center_Suffix"]) ? $row["Center_Suffix"] : "",
      "Has_Unique_StudentID" => $row["Has_Unique_StudentID"],
      "ID_Suffix" => !empty($row["ID_Suffix"]) ? $row['ID_Suffix'] : "",
      "Max_Character" => !empty($row["Max_Character"]) ? str_repeat('X', $row['Max_Character']) : "",
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
