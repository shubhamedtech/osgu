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
if($_SESSION['Role']=='University Head'){
  $university_query = " AND University_User.University_ID = ".$_SESSION['university_id'];
}elseif($_SESSION['Role']=='Center'){
  $university_query = " AND Center_SubCenter.Center = ".$_SESSION['ID'];
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count= $conn->query("SELECT COUNT(Users.ID) as allcount FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center WHERE Role = 'Sub-Center' $university_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Users.ID) as filtered FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center LEFT JOIN Users as U1 ON Center_SubCenter.Center = U1.ID WHERE Users.Role = 'Sub-Center' $university_query $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Users.`ID`, Users.`Name`, Users.`Email`, Users.`Mobile`, Users.`Code`, Users.`Status`, Users.`Photo`, CONCAT(U1.Name, ' (', U1.Code, ')') AS `Reporting`, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center LEFT JOIN Users as U1 ON Center_SubCenter.Center = U1.ID WHERE Users.Role = 'Sub-Center' $university_query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  
  $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For = ".$row['ID']."");
  $admissions = mysqli_fetch_assoc($admissions);

  $data[] = array( 
    "Photo"=> $row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Mobile" => $row['Mobile'],
    "Code" => $row['Code'],
    "Reporting" => $row['Reporting'],
    "Admission" => $admissions['Applications'],
    "Password" => $row['password'],
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
