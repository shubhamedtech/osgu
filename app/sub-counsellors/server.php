<?php
// ini_set('display_errors',1);
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
  $orderby = "ORDER BY Users.ID ASC";
}

$session_query = "";
$university_query  = '';
if ($_SESSION['Role'] == 'Counsellor') {
  $university_query = " AND University_User.University_ID = " . $_SESSION['university_id'] . " AND University_User.Reporting = " . $_SESSION['ID'];
  $current_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = " . $_SESSION['university_id'] . " AND Current_Status = 1");
  if ($current_session->num_rows > 0) {
    $current_session = mysqli_fetch_assoc($current_session);
    $session_query = " AND Admission_Session_ID = " . $current_session['ID'];
  }
} elseif ($_SESSION['Role'] != 'Administrator') {
  $university_query = " AND University_User.University_ID = " . $_SESSION['university_id'];
  $current_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = " . $_SESSION['university_id'] . " AND Current_Status = 1");
  if ($current_session->num_rows > 0) {
    $current_session = mysqli_fetch_assoc($current_session);
    $session_query = " AND Admission_Session_ID = " . $current_session['ID'];
  }
}

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Users.Name like '%" . $searchValue . "%' OR Users.Code like '%" . $searchValue . "%' OR Users.Email like '%" . $searchValue . "%' OR Users.Mobile like '%" . $searchValue . "%')";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(DISTINCT Users.ID) as allcount FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Sub-Counsellor' $university_query GROUP BY Users.ID");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = empty($records['allcount']) ? 0 : $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(DISTINCT Users.ID) as filtered FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID LEFT JOIN Users as U1 ON University_User.Reporting = U1.ID WHERE Users.Role = 'Sub-Counsellor' $university_query $searchQuery GROUP BY Users.ID");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = empty($records['allcount']) ? 0 : $records['filtered'];

## Fetch records
$result_record = "SELECT Users.`ID`, Users.`Name`, Users.`Email`, Users.`Mobile`, Users.`Code`, Users.`Status`, Users.`Photo`, CONCAT(U1.Name, ' (', U1.Code, ')') AS `Reporting`, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID LEFT JOIN Users as U1 ON University_User.Reporting = U1.ID WHERE Users.Role = 'Sub-Counsellor' $university_query $searchQuery GROUP BY Users.ID $orderby LIMIT " . $row . "," . $rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $alloted = [];
  $alloted_universities = $conn->query("SELECT CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID WHERE `User_ID` = " . $row['ID'] . "");
  if ($alloted_universities->num_rows > 0) {
    while ($alloted_university = $alloted_universities->fetch_assoc()) {
      $alloted[] = $alloted_university['University'];
    }
  }

  $alloted_centers = array();
  $alloted_sub_centers = array();
  $centers = $conn->query("SELECT Code FROM Alloted_Center_To_SubCounsellor LEFT JOIN University_User ON Alloted_Center_To_SubCounsellor.Sub_Counsellor_ID = University_User.User_ID WHERE Alloted_Center_To_SubCounsellor.Sub_Counsellor_ID = " . $row['ID'] . " $university_query");
  while ($center = $centers->fetch_assoc()) {
    $alloted_centers[] = $center['Code'];
  }

  $added_for = array_filter($alloted_centers);

  $admissions['Applications'] = 0;

  if (!empty($added_for)) {
    $sub_centers = $conn->query("SELECT ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'Sub-Center' AND Reporting IN (" . implode(',', $added_for) . ")");
    if ($sub_centers->num_rows > 0) {
      while ($sub_center = $sub_centers->fetch_assoc()) {
        $alloted_sub_centers[] = $sub_center['ID'];
      }
      $added_for = array_filter(array_merge($alloted_centers, $alloted_sub_centers));
    }

    $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For IN (" . implode(',', $added_for) . ") AND Step = 4 AND University_ID = " . $_SESSION['university_id'] . " $session_query");
    $admissions = mysqli_fetch_assoc($admissions);
  }

  $data[] = array(
    "Photo" => $row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Mobile" => $row['Mobile'],
    "Code" => $row['Code'],
    "Reporting" => $row['Reporting'],
    "Center" => $centers->num_rows,
    "Admission" => $admissions['Applications'],
    "Password" => $row['password'],
    "Status"  => $row["Status"],
    "University" => implode('<br>', $alloted),
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
