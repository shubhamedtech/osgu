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
  $orderby = "ORDER BY Sub_Courses.ID ASC";
}

// Admin Query
$query = " AND Sub_Courses.University_ID = ".$_SESSION['university_id'];

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Sub_Courses.Name like '%".$searchValue."%' OR Sub_Courses.Short_Name like '%".$searchValue."%' OR Universities.Short_Name LIKE '%".$searchValue."%' OR Universities.Vertical  like '%".$searchValue."%' OR Modes.Name  like '%".$searchValue."%' OR Schemes.Name  like '%".$searchValue."%' OR Courses.Short_Name  like '%".$searchValue."%' OR Courses.Name  like '%".$searchValue."%' OR CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Sub_Courses WHERE ID IS NOT NULL $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Sub_Courses.ID) as filtered, Sub_Courses.`Name`, Courses.Short_Name as Course, Course_Types.`Name` as CourseType, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University, Sub_Courses.Status, Schemes.`Name` as Scheme, Modes.`Name` as Mode FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Universities ON Sub_Courses.University_ID = Universities.ID LEFT JOIN Schemes ON Sub_Courses.Scheme_ID = Schemes.ID LEFT JOIN Modes ON Sub_Courses.Mode_ID = Modes.ID WHERE Sub_Courses.ID IS NOT NULL $query $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Sub_Courses.ID, Sub_Courses.`Name`, Courses.Short_Name as Course, Course_Types.`Name` as CourseType, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University, Sub_Courses.Status, Schemes.`Name` as Scheme, Modes.`Name` as Mode FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Universities ON Sub_Courses.University_ID = Universities.ID LEFT JOIN Schemes ON Sub_Courses.Scheme_ID = Schemes.ID LEFT JOIN Modes ON Sub_Courses.Mode_ID = Modes.ID WHERE Sub_Courses.ID IS NOT NULL $query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {

    $data[] = array( 
      "Name" => $row["Name"],
      "Course" => $row["Course"],
      "CourseType" => $row["CourseType"],
      "University" => $row["University"],
      "Scheme" => $row["Scheme"],
      "Mode" => $row["Mode"],
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
