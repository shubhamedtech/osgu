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


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Users WHERE Role = 'University Head'");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Users WHERE Role = 'University Head' $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT `ID`, `Name`, `Email`, `Code`, `Status`, `Photo`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users WHERE Role = 'University Head' $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $alloted = [];
  $alloted_universities = $conn->query("SELECT CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID WHERE `User_ID` = ".$row['ID']."");
  if($alloted_universities->num_rows>0){
    while($alloted_university = $alloted_universities->fetch_assoc()){
      $alloted[] = $alloted_university['University'];
    }
  }

    $data[] = array( 
      "Photo"=> $row['Photo'],
      "Name" => $row['Name'],
      "Email" => $row['Email'],
      "Code" => $row['Code'],
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
