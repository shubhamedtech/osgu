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
  $orderby = "ORDER BY video_lectures.id ASC";
}




// Admin Query
$query = "";
//$query = " AND Sub_Courses.University_ID = ".$_SESSION['university_id'];

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Syllabi.Name like '%".$searchValue."%' OR Sub_Courses.Name like '%".$searchValue."%' OR Sub_Courses.Short_Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(id) as allcount FROM video_lectures WHERE video_lectures.status!=2 $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(video_lectures.id) as filtered FROM video_lectures LEFT JOIN Sub_Courses ON Sub_Courses.ID = video_lectures.course_id LEFT JOIN Syllabi ON Syllabi.ID = video_lectures.subject_id WHERE video_lectures.status!=2 $searchQuery $query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Sub_Courses ON Sub_Courses.ID = video_lectures.course_id LEFT JOIN Syllabi ON Syllabi.ID = video_lectures.subject_id WHERE video_lectures.status !=2  $searchQuery $query $orderby LIMIT ".$row.",".$rowperpage;



$results = mysqli_query($conn, $result_record);
$data = array();
while ($row = mysqli_fetch_assoc($results)) {

    $data[] = array( 
         "unit" => ucwords($row["unit"]),
        "semester" => $row["semester"],
        "description" => ucwords($row["description"]),
        "course_name" => ucwords($row["course_name"]).'( '.$row["course_short_name"].' )',
        "subject_name" => ucwords($row["subject_name"]),
        "video_type" => $row["video_type"],
        "video_url" => $row["video_url"],
        "thumnail_url" => $row["thumbnail_url"],
        "thumnail_type" => $row["thumbnail_type"],
        "status" => $row["status"],
        "ID" => $row["id"],
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
