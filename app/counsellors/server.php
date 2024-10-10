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
  $orderby = "ORDER BY Users.ID ASC";
}

$university_query  = '';
$session_query = '';
if($_SESSION['Role']!='Administrator'){
  $university_query = " AND University_User.University_ID = ".$_SESSION['university_id'];

  $current_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']." AND Current_Status = 1");
  if($current_session->num_rows>0){
    $current_session = mysqli_fetch_assoc($current_session);
    $session_query = " AND Admission_Session_ID = ".$current_session['ID'];
  }
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' $university_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' $university_query $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT `ID`, `Name`, `Email`, `Mobile`, `Code`, `Status`, `Photo`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password, University_User.University_ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' $university_query $searchQuery GROUP BY Users.ID $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $alloted = [];
  $alloted_universities = $conn->query("SELECT Universities.ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID WHERE `User_ID` = ".$row['ID']." $university_query");
  if($alloted_universities->num_rows>0){
    while($alloted_university = $alloted_universities->fetch_assoc()){
      $alloted[$alloted_university['ID']] = $alloted_university['University'];
    }
  }

  $alloted_centers = array();
  $alloted_sub_centers = array();
  if(!empty($alloted)){
    $centers = $conn->query("SELECT Code FROM Alloted_Center_To_Counsellor LEFT JOIN University_User ON Alloted_Center_To_Counsellor.Counsellor_ID = University_User.User_ID AND Alloted_Center_To_Counsellor.University_ID = University_User.University_ID WHERE Alloted_Center_To_Counsellor.Counsellor_ID = ".$row['ID']." AND Alloted_Center_To_Counsellor.University_ID IN (".implode(',', array_keys($alloted)).")");
    while($center = $centers->fetch_assoc()){
      $alloted_centers[] = $center['Code'];
    }
  }
  
  $added_for = array_filter($alloted_centers);

  $admissions['Applications'] = 0;

  if(!empty($added_for)){
    $sub_centers = $conn->query("SELECT ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'Sub-Center' AND Users.ID IN (".implode(',', $added_for).")");
    if($sub_centers->num_rows>0){
      while($sub_center = $sub_centers->fetch_assoc()){
        $alloted_sub_centers[] = $sub_center['ID'];
      }
      $added_for = array_filter(array_merge($alloted_centers, $alloted_sub_centers));
    }
    $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For IN (".implode(',', $added_for).") AND Step = 4 AND University_ID IN (".implode(',', array_keys($alloted)).") $session_query");
    $admissions = mysqli_fetch_assoc($admissions);
  }

  $data[] = array( 
    "Photo"=> $row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Mobile" => $row['Mobile'],
    "Code" => $row['Code'],
    "Center" => !empty($alloted) ? $centers->num_rows : "",
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
