<?php
## Database configuration
include '../../includes/db-config.php';
include '../../includes/helpers.php';
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
  $orderby = "ORDER BY Users.ID DESC";
}

// Session Query
$session_query = "";
$current_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = " . $_SESSION['university_id'] . " AND Current_Status = 1");
if ($current_session->num_rows > 0) {
  $current_session = mysqli_fetch_assoc($current_session);
  $session_query = " AND Admission_Session_ID = " . $current_session['ID'];
}

$center_query = "";
if ($_SESSION['Role'] != "Administrator") {
  $check_has_unique_center_code = $conn->query("SELECT Center_Suffix FROM Universities WHERE ID = " . $_SESSION['university_id'] . " AND Has_Unique_Center = 1");
  if ($check_has_unique_center_code->num_rows > 0) {
    $center_suffix = mysqli_fetch_assoc($check_has_unique_center_code);
    $center_suffix = $center_suffix['Center_Suffix'];
    $center_query = " AND Users.Code LIKE '$center_suffix%' AND Users.Is_Unique = 1";
  } else {
    $center_query = " AND Users.Is_Unique = 0";
  }
}

$center_ids = array();
$table = 'Alloted_Center_To_Counsellor';
$university_query = "";
if ($_SESSION['Role'] == 'University Head' || $_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'Operations') {
  $university_query = " AND University_ID = " . $_SESSION['university_id'];
  $table = 'Alloted_Center_To_Counsellor';
} elseif ($_SESSION['Role'] == 'Counsellor') {
  $university_query = " AND University_ID = " . $_SESSION['university_id'] . " AND Counsellor_ID =" . $_SESSION['ID'];
  $table = 'Alloted_Center_To_Counsellor';
} elseif ($_SESSION['Role'] == 'Sub-Counsellor') {
  $university_query = " AND University_ID = " . $_SESSION['university_id'] . " AND Sub_Counsellor_ID =" . $_SESSION['ID'];
  $table = 'Alloted_Center_To_SubCounsellor';
}
$alloted_centers = $conn->query("SELECT Code FROM $table WHERE ID IS NOT NULL $university_query GROUP BY Code");
while ($alloted_center = $alloted_centers->fetch_assoc()) {
  $center_ids[] = $alloted_center['Code'];
}

if (empty($center_ids)) {
  $center_ids[] = $_SESSION['ID'];
}

$center_ids = implode(',', $center_ids);

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Users.Name like '%" . $searchValue . "%' OR Users.Code like '%" . $searchValue . "%' OR Users.Email like '%" . $searchValue . "%' OR Users.Mobile like '%" . $searchValue . "%')";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Users WHERE Role = 'Center' $center_query AND ID IN ($center_ids)");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Users WHERE Role = 'Center' $center_query $searchQuery AND ID IN ($center_ids)");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Users.ID,Users.Name,Users.CanCreateSubCenter,Users.Email,Users.Mobile,Users.Code,Users.Status,Users.Photo,CAST(AES_DECRYPT(Users.`Password`,'60ZpqkOnqn0UQQ2MYTlJ')AS CHAR(50))as Password,GROUP_CONCAT(CONCAT(Fee_Structures.`Name`,' - ',Fee_Variables.Fee,IF(Fee_Structures.Sharing=1,'%',''))SEPARATOR'<br>') as Fee_Alloted FROM Users LEFT JOIN Fee_Variables ON Users.ID=Fee_Variables.Code AND Fee_Variables.University_ID=12 LEFT JOIN Fee_Structures ON Fee_Variables.Fee_Structure_ID=Fee_Structures.ID WHERE Users.`Role`='Center' AND Users.ID IN ($center_ids) $center_query $searchQuery GROUP BY Users.ID $orderby LIMIT " . $row . "," . $rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  // RM
  $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID WHERE Alloted_Center_To_Counsellor.Code = " . $row['ID'] . " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'] . "");
  $rm = mysqli_fetch_array($rm);

  // Admission Count
  $alloted_centers = array($row['ID']);
  $added_for = $alloted_centers;
  $sub_centers = $conn->query("SELECT ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'Sub-Center' AND Reporting = " . $row['ID'] . "");
  if ($sub_centers->num_rows > 0) {
    while ($sub_center = $sub_centers->fetch_assoc()) {
      $alloted_sub_centers[] = $sub_center['ID'];
    }
    $added_for = array_filter(array_merge($alloted_centers, $alloted_sub_centers));
  }

  $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For IN (" . implode(',', $added_for) . ") AND Step = 4 $session_query");
  $admissions = mysqli_fetch_assoc($admissions);

  $data[] = array(
    "Photo" => $row['Photo'],
    "Name" => $row['Name'],
    "Email" => stringToSecret($row['Email']),
    "Mobile" => stringToSecret($row['Mobile']),
    "Code" => $row['Code'],
    "Password" => $row['Password'],
    "Admission" => $admissions['Applications'],
    "RM" => $rm['Name'],
    "CanCreateSubCenter" => $row['CanCreateSubCenter'],
    "Status" => $row["Status"],
    "ID" => $row["ID"],
    "Fee_Alloted" => $row["Fee_Alloted"],
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
