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
  $query = $_SESSION['Role']!="Administrator" ? " AND Courses.University_ID = ".$_SESSION['university_id'] : "";

  
  $header = array('Course Type', 'Name', 'Short Name', 'University', 'Status');
  
  ## Search 
  $searchQuery = " ";
  if($search_value != ''){
  $searchQuery = " AND (Courses.Name like '%".$searchValue."%' OR Courses.Short_Name like '%".$searchValue."%')";
  }
  
  ## Fetch records
  $result_record = "SELECT Courses.`ID`, Courses.`Name`, Courses.`Short_Name`, Course_Types.Name as CourseType, Courses.`Status`, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Courses LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Universities ON Courses.University_ID = Universities.ID WHERE Courses.ID IS NOT NULL $searchQuery $query ORDER BY ID DESC";
  $empRecords = mysqli_query($conn, $result_record);
  $data[] = $header;
  
  while ($row = mysqli_fetch_assoc($empRecords)) {
    $data[] = array( 
      "Type" => $row["CourseType"],
      "Name" => $row["Name"],
      "Short_Name" => $row["Short_Name"],
      "University" => $row["University"],
      "Status"  => $row["Status"]==1 ? "Active" : "Inactive"
    );
  }

  $xlsx = SimpleXLSXGen::fromArray( $data )->downloadAs('Courses.xlsx');
