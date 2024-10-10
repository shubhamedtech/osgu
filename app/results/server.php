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
  $orderby = "ORDER BY Results.ID DESC";
}

$statusQuery = "";
if(!in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Student'])){
 $statusQuery = " AND Results.User = 1";
}

$role_query = "";
$studentQuery = "";
if($_SESSION['Role']=='Student'){
  $studentQuery = " AND Results.Student = 1 AND Results.Student_ID = ".$_SESSION['ID'];
}else{
  $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
  $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)) like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Students.Father_Name LIKE '%".$searchValue."%' OR Students.Unique_ID LIKE '%".$searchValue."%' OR Results.Sem = '$searchValue' OR Courses.Short_Name LIKE '%".$searchValue."%' OR Sub_Courses.Name LIKE '%".$searchValue."%' OR Students.Enrollment_No LIKE '%".$searchValue."%' OR DATE_FORMAT(Results.Published_On, '%d-%m-%Y') LIKE '%".$searchValue."%' OR Results.Remarks LIKE '%".$searchValue."%' OR Results.`Type` LIKE '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(Results.Student_ID) as allcount FROM Results LEFT JOIN Students ON Results.Student_ID = Students.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' LEFT JOIN Users ON Students.Added_For = Users.ID WHERE Results.University_ID = ".$_SESSION['university_id']." $role_query $statusQuery $studentQuery GROUP BY Results.Student_ID,Results.Exam_Session,Results.`Type`");
$totalRecords = $all_count->num_rows;

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Results.Student_ID) as allcount FROM Results LEFT JOIN Students ON Results.Student_ID = Students.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' LEFT JOIN Users ON Students.Added_For = Users.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Results.University_ID = ".$_SESSION['university_id']." $role_query $searchQuery $statusQuery $studentQuery GROUP BY Results.Student_ID,Results.Exam_Session,Results.`Type`");
$totalRecordwithFilter = $filter_count->num_rows;

## Fetch records
$result_record = "SELECT MAX(Results.ID) as ID, TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)) as First_Name, Students.Father_Name, Students.Unique_ID, Students.Enrollment_No, Courses.Short_Name as Course_ID, Sub_Courses.Name as Sub_Course_ID, Student_Documents.`Location` as Photo, Results.`Type`, Results.Remarks, Results.User, Results.Student, Results.Exam_Session, Results.Student_ID, Results.Sem, Students.Added_For, Users.Code, Users.`Name`, DATE_FORMAT(Results.Published_On, '%d-%m-%Y') as Published_On FROM Results LEFT JOIN Students ON Results.Student_ID = Students.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID WHERE Results.University_ID = ".$_SESSION['university_id']." $searchQuery $role_query $statusQuery $studentQuery GROUP BY Results.Student_ID,Results.Exam_Session,Results.`Type` $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $data[] = array(
    "ID" => $row["Student_ID"],
    "Photo"=> $row['Photo'],
    "First_Name" => $row['First_Name'],
    "Enrollment_No" => $row['Enrollment_No'],
    "Course_ID" => $row['Course_ID'],
    "Sub_Course_ID" => $row['Sub_Course_ID'],
    "Father_Name" => $row['Father_Name'],
    "Sem" => $row['Sem'],
    "Exam_Session" => $row['Exam_Session'],
    "Unique_ID" => $row['Unique_ID'],
    "Student_ID" => base64_encode('W1Ebt1IhGN3ZOLplom9I'.$row['Student_ID'].'W1Ebt1IhGN3ZOLplom9I'),
    "Published_On" => !empty($row['Published_On']) ? $row['Published_On'] : 0,
    "Code" => $row['Code'],
    "Name" => $row['Name'],
    "User" => $row['User'],
    "Student" => $row['Student'],
    "Type"=> $row['Type'],
    "Remarks" => $row['Remarks'],
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
