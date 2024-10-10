<?php
ini_set('display_errors', 1);
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
  $orderby = "ORDER BY Exams.ID ASC";
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Exams.Name like '%".$searchValue."%' OR Exam_Sessions.Name like '%".$searchValue."%' OR DATE_FORMAT(Start_Date, '%d-%m-%Y') like '%".$searchValue."%' OR DATE_FORMAT(Start_Date, '%d-%m-%Y') like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Exams WHERE University_ID = ".$_SESSION['university_id']);
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Exams.ID) as filtered FROM Exams LEFT JOIN Exam_Sessions ON Exams.Exam_Session_ID = Exam_Sessions.ID WHERE Exams.University_ID = ".$_SESSION['university_id']." $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Exams.`ID`, Exam_Type, Exams.`Name`, DATE_FORMAT(Start_Date, '%d-%m-%Y') as Start_Date, DATE_FORMAT(End_Date, '%d-%m-%Y') as End_Date, Start_Time, End_Time, Exam_Sessions.Name as Exam_Session FROM Exams LEFT JOIN Exam_Sessions ON Exams.Exam_Session_ID = Exam_Sessions.ID WHERE Exams.University_ID = ".$_SESSION['university_id']." $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  $data[] = array( 
    "ID" => $row["ID"],
    "Exam_Session_ID" => $row["Exam_Session"],
    "Name" => $row["Name"],
    "Start_Date" => $row['Start_Date'],
    "End_Date" => $row['End_Date'],
    "Start_Time" => date("h:i A", strtotime($row['Start_Time'])),
    "End_Time" => date("h:i A", strtotime($row['End_Time'])),
    "Exam_Type" => $row["Exam_Type"],
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
