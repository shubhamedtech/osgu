<?php
## Database configuration
include '../../includes/db-config.php';
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

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Students.ID DESC";
}

if (isset($_SESSION['current_session'])) {
  if ($_SESSION['current_session'] == 'All') {
    $session_query = '';
  } else {
    $session_query = "AND Admission_Sessions.Name like '%" . $_SESSION['current_session'] . "%'";
  }
} else {
  $get_current_session = $conn->query("SELECT Name FROM Admission_Sessions WHERE Current_Status = 1 AND University_ID = '" . $_SESSION['university_id'] . "'");
  if ($get_current_session->num_rows > 0) {
    $gsc = mysqli_fetch_assoc($get_current_session);
    $session_query = "AND Admission_Sessions.Name like '%" . $gsc['Name'] . "%'";
  } else {
    $session_query = '';
  }
}

$role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);

$step_query = "";
// if($_SESSION['Role']!='Sub-Center' && $_SESSION['Role']!='Center'){
//   $step_query = " AND Step = 4";
// }


## Search 
$searchQuery = " ";
if ($searchValue != '') {
  if (strcasecmp($searchValue, 'completed') == 0) {
    $searchQuery = " AND Step = 4 ";
  } else {
    $searchQuery = " AND (RIGHT(CONCAT('000000', Students.ID), 6) like '%" . $searchValue . "%' OR Students.ID like '%" . $searchValue . "%' OR Students.Unique_ID like '%" . $searchValue . "%' OR Students.First_Name like '%" . $searchValue . "%' OR Students.Middle_Name like '%" . $searchValue . "%' OR Students.Last_Name like '%" . $searchValue . "%' OR Admission_Sessions.Name like '%" . $searchValue . "%' OR Admission_Types.Name like '%" . $searchValue . "%' OR Students.Step like '%" . $searchValue . "%' OR Students.Father_Name like '%" . $searchValue . "%' OR Students.Email like '%" . $searchValue . "%' OR Students.Contact like '%" . $searchValue . "%' OR Sub_Courses.Short_Name like '%" . $searchValue . "%' OR Students.Enrollment_No like '%" . $searchValue . "%' OR Students.OA_Number like '%" . $searchValue . "%')";
  }
}

$filterByDepartment = "";
if (isset($_SESSION['filterByDepartment'])) {
  $filterByDepartment = $_SESSION['filterByDepartment'];
}

$filterBySubCourse = "";
if (isset($_SESSION['filterBySubCourses'])) {
  $filterBySubCourse = $_SESSION['filterBySubCourses'];
}

$filterByStatus = "";
if (isset($_SESSION['filterByStatus'])) {
  $filterByStatus = $_SESSION['filterByStatus'];
}

$filterQueryUser = "";
if (isset($_SESSION['filterByUser'])) {
  $filterQueryUser = $_SESSION['filterByUser'];
}

$filterByDate = "";
if (isset($_SESSION['filterByDate'])) {
  $filterByDate = $_SESSION['filterByDate'];
}

$searchQuery .= $filterByDepartment . $filterQueryUser . $filterByDate . $filterBySubCourse . $filterByStatus;

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(Students.ID) as allcount FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Process_By_Center IS NULL AND Step = 4 $role_query $step_query $session_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Students.ID) as filtered FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID=Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID=Admission_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Users ON Students.Added_For=Users.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Process_By_Center IS NULL AND Step = 4 $searchQuery $role_query $step_query $session_query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Student_Pendencies.ID as Pendency, UPPER(DATE_FORMAT(Students.DOB, '%d%b%Y')) as DOB, Students.Status, Students.ID, Students.Added_For,CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Unique_ID, CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name) as First_Name, Students.Father_Name, Students.Enrollment_No, Students.OA_Number, Students.Duration, Students.Step, Students.Process_By_Center, Students.Payment_Received, Students.Document_Verified, Students.Processed_To_University, Admission_Sessions.`Name` as Adm_Session, Admission_Types.`Name` as Adm_Type, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Short_Name, Student_Documents.`Location`, Students.ID_Card, Students.Admit_Card, Students.Exam FROM Students LEFT JOIN Student_Pendencies ON Students.ID = Student_Pendencies.Student_ID AND Student_Pendencies.Status = 0 LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Photo' WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Process_By_Center IS NULL AND Step = 4 $searchQuery $role_query $step_query $session_query $orderby LIMIT " . $row . "," . $rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  // Added_For
  if ($_SESSION['Role'] == 'Center') {
    $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . "");
  } else {
    $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . " AND Role = 'Center'");
    if ($user->num_rows == 0) {
      $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row['Added_For']);
    }
  }

  $user = mysqli_fetch_array($user);

  // RM
  $rm['Name'] = "";
  if (!empty($user)) {
    // RM
    $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'] . " WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id']);
    if ($rm->num_rows > 0) {
      $rm = mysqli_fetch_array($rm);
    } else {
      $rm = $user;
    }
  }

  $data[] = array(
    "Photo" => empty($row['Location']) ? '/assets/img/default-user.png' : $row['Location'],
    "First_Name" => $row['First_Name'],
    "Father_Name" => $row['Father_Name'],
    "Unique_ID" => $row['Unique_ID'],
    "Enrollment_No" => !empty($row['Enrollment_No']) ? $row['Enrollment_No'] : '',
    "OA_Number" => !empty($row['OA_Number']) ? $row['OA_Number'] : '',
    "Duration" => $row['Duration'],
    "Step" => $row['Step'],
    "Process_By_Center" => !empty($row['Process_By_Center']) ? date("d-m-Y", strtotime($row['Process_By_Center'])) : "1",
    "Payment_Received" => !empty($row['Payment_Received']) ? date("d-m-Y", strtotime($row['Payment_Received'])) : "1",
    "Document_Verified" => !empty($row['Document_Verified']) ? date("d-m-Y", strtotime($row['Document_Verified'])) : "1",
    "Processed_To_University" => !empty($row['Processed_To_University']) ? date("d-m-Y", strtotime($row['Processed_To_University'])) : '1',
    "Adm_Session" => $row['Adm_Session'],
    "Adm_Type" => $row['Adm_Type'],
    "Short_Name" => $row['Short_Name'],
    "Center_Code" => $user['Code'],
    "Center_Name" => $user['Name'],
    "RM" => $rm['Name'],
    "Status" => $row['Status'],
    "DOB" => $row['DOB'],
    "ID_Card" => $row['ID_Card'],
    "Admit_Card" => $row['Admit_Card'],
    "Exam" => $row['Exam'],
    "Pendency" => empty($row['Pendency']) ? 0 : (int)$row['Pendency'],
    "ID" => base64_encode($row['ID'] . 'W1Ebt1IhGN3ZOLplom9I'),
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
