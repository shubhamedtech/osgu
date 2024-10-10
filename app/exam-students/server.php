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
  $searchQuery = "AND Exam_Students.Enrolment_Number like '%" . $searchValue . "%'  OR  Exam_Students.Name like '%" . $searchValue . "%'";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Exam_Students WHERE University_ID = ".$_SESSION['university_id']);
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Exam_Students WHERE Exam_Students.University_ID = ".$_SESSION['university_id']." $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$exam_student = $conn->query("SELECT Exam_Students.*, Courses.Name as Course, Courses.ID as Course_ID, Sub_Courses.Name as Sub_Course, Sub_Courses.ID as Sub_Course_ID,  Admission_Sessions.Name as Admission_Session,Admission_Sessions.ID as Admission_Session_ID, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Exam_Students LEFT JOIN Courses ON Exam_Students.Course = Courses.ID LEFT JOIN Sub_Courses ON Exam_Students.Sub_Course = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Exam_Students.Admission_Session = Admission_Sessions.ID LEFT JOIN Admission_Types ON Exam_Students.Admission_Type = Admission_Types.ID WHERE Exam_Students.University_ID = ".$_SESSION['university_id']." $searchQuery");
// $empRecords = mysqli_query($conn, $exam_student);
$data = array();
$i= 1;
while ($row = mysqli_fetch_assoc($exam_student)) {
  $data[] = array( 
    "Photo" => empty($row['Location']) ? '/assets/img/'.$row['Enrolment_Number'].'.jpg' : $row['Location'],
    "First_Name" => $row['Name'],
    "Phone_Number"=> $row['Phone_Number'],
    "Email"=> $row['Email'],
    "Enrollment_No" => !empty($row['Enrolment_Number']) ? $row['Enrolment_Number'] : '',
    "Adm_Session" => $row['Duration'],
    "Adm_Type" => $row['Admission_Type'],
    "Course" => $row['Course'],
    "Sub_Course" => $row['Sub_Course'],
    "Status" => 1,
    "DOB" => date("d-M-Y", strtotime($row['DOB'])),
    "Student_id" => $row['ID'],
    "ID" => $i++,
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
