<?php
## Database configuration
include '../../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value
$university_id = intval($_POST['university_id']);

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Exam_Sessions.ID DESC";
}

$admission_sessions = array();
$sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = $university_id");
while ($session = $sessions->fetch_assoc()) {
  $admission_sessions[$session['ID']] = $session['Name'];
}

$admission_types = array();
$types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = $university_id");
while ($type = $types->fetch_assoc()) {
  $admission_types[$type['ID']] = $type['Name'];
}

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Exam_Sessions.Name like '%" . $searchValue . "%')";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Exam_Sessions WHERE University_ID = $university_id");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Exam_Sessions WHERE University_ID = $university_id $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Exam_Sessions.ID, Exam_Sessions.Name, `Admission_Session`, `RR_Status`, `RR_Last_Date`, `BP_Status`, `BP_Last_Date`, `Status` FROM Exam_Sessions WHERE Exam_Sessions.University_ID = $university_id $searchQuery $orderby LIMIT " . $row . "," . $rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  $selected_sessions = array();
  $sessions = json_decode($row['Admission_Session'], true);
  foreach ($sessions as $key => $session) {
    $admissionType = !empty($session['admissionType']) ? '<br>Adm Type: ' . $admission_types[$session['admissionType']] : '';
    $selected_sessions[] = "For: " . $session['for'] . "<br>Session: " . $admission_sessions[$session['session']] . "<br>From: " . $session['table'] . "<br>Sem(s) - " . implode(",", $session['duration']) . $admissionType;
  }

  $data[] = array(
    "ID" => $row["ID"],
    "Name" => $row["Name"],
    "Admission_Session" => implode("<br>---------------------<br>", $selected_sessions),
    "RR_Status" => $row["RR_Status"],
    "RR_Last_Date" => !empty($row["RR_Last_Date"]) ? date("d-m-Y", strtotime($row["RR_Last_Date"])) : '',
    "BP_Status" => $row["BP_Status"],
    "BP_Last_Date" => !empty($row["BP_Last_Date"])  ? date("d-m-Y", strtotime($row["BP_Last_Date"])) : '',
    "Status" => $row["Status"]
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
