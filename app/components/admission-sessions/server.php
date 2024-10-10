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
    $orderby = "ORDER BY Admission_Sessions.ID ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Admission_Sessions.Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Admission_Sessions WHERE University_ID = $university_id");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Admission_Sessions WHERE University_ID = $university_id $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Admission_Sessions.ID, Admission_Sessions.Name, `Exam_Session`, Schemes.Name as Scheme, Admission_Sessions.Status, `Current_Status`, `LE_Status`, `CT_Status` FROM Admission_Sessions LEFT JOIN Schemes ON Admission_Sessions.Scheme_ID = Schemes.ID WHERE Admission_Sessions.University_ID = $university_id $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

$admission_types = [];
$types = $conn->query("SELECT Name FROM Admission_Types WHERE University_ID = $university_id");
if($types->num_rows>0){
  while($type = $types->fetch_assoc()){
    $admission_types[] = $type['Name'];
  }  
}
$permissions['LE_Status'] = in_array('Lateral', $admission_types) ? true : false;
$permissions['CT_Status'] = in_array('Credit Transfer', $admission_types) ? true : false;

while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
      "Name" => $row["Name"],
      "Exam_Session" => $row["Exam_Session"],
      "Scheme" => $row["Scheme"],
      "Status" => $row["Status"],
      "Current_Status" => $row["Current_Status"],
      "LE_Status" => $row["LE_Status"],
      "CT_Status" => $row["CT_Status"],
      "ID" => $row["ID"],
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "permissions" => $permissions,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
