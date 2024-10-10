<?php
  // ini_set('error_reporting', E_ALL );
  // ini_set('display_errors', 1 );
  session_start();
  require '../../includes/db-config.php';
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $search_value = "";
  if(isset($_GET['search'])){
    $search_value = mysqli_real_escape_string($conn,$_GET['search']); // Search value
  }

  // Admin Query
  $query = $_SESSION['Role']!="Administrator" ? " AND Sub_Courses.University_ID = ".$_SESSION['university_id'] : "";

  
  $header = array('Course Type', 'Course', 'Specialization', 'Scheme', 'Mode', 'University', 'Status');
  
  ## Search 
  $searchQuery = " ";
  if($search_value != ''){
    $searchQuery = " AND (Sub_Courses.Name like '%".$searchValue."%' OR Sub_Courses.Short_Name like '%".$searchValue."%' OR Universities.Short_Name LIKE '%".$searchValue."%' OR Universities.Vertical  like '%".$searchValue."%' OR Modes.Name  like '%".$searchValue."%' OR Schemes.Name  like '%".$searchValue."%' OR Courses.Short_Name  like '%".$searchValue."%' OR Courses.Name  like '%".$searchValue."%')";
  }
  
  ## Fetch records
  $result_record = "SELECT Sub_Courses.ID, Sub_Courses.`Name`, Courses.Short_Name as Course, Course_Types.`Name` as CourseType, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University, Sub_Courses.Status, Schemes.`Name` as Scheme, Modes.`Name` as Mode FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Universities ON Sub_Courses.University_ID = Universities.ID LEFT JOIN Schemes ON Sub_Courses.Scheme_ID = Schemes.ID LEFT JOIN Modes ON Sub_Courses.Mode_ID = Modes.ID WHERE Sub_Courses.ID IS NOT NULL $query $searchQuery ORDER BY ID DESC";
  $empRecords = mysqli_query($conn, $result_record);
  $data[] = $header;
  
  while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
      "CourseType" => $row["CourseType"],
      "Course" => $row["Course"],
      "Name" => $row["Name"],
      "Scheme" => $row["Scheme"],
      "Mode" => $row["Mode"],
      "University" => $row["University"],
      "Status"  => $row["Status"]==1 ? "Active" : "Inactive"
    );

  }

  $xlsx = SimpleXLSXGen::fromArray( $data )->downloadAs('Sub-Courses.xlsx');
