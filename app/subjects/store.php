<?php
ini_set('display_errors', 1);
  if(isset($_FILES['file'])){
    require '../../includes/db-config.php';
    require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
    require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');
    session_start();

    $export_data = array();

    // Header
    $header = array('Scheme', 'Course', 'Sub-Course', 'Semester', 'Subject Code', 'Subject Name', 'Type (Theory/Practical)', 'Credit', 'Minimum Marks', 'Maximum Marks', 'Remark');
    $export_data[] = $header;

    $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if(in_array($_FILES["file"]["type"],$mimes)){
      // Upload File
      $uploadFilePath = basename($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

      // Read File
      $reader = new SpreadsheetReader($uploadFilePath);

      // Sheet Count
      $totalSheet = count($reader->sheets());

      /* For Loop for all sheets */
      for($i=0; $i<$totalSheet; $i++){
        $reader->ChangeSheet($i);
   
        foreach ($reader as $row)
        {
          //print_r($row);
          //exit();
          // Data
            $remark = [];
            $scheme = isset($row[0]) ? mysqli_real_escape_string($conn, $row[0]) : null;
            $course = isset($row[1]) ? mysqli_real_escape_string($conn, $row[1]) : null;
            $sub_course = isset($row[2]) ? mysqli_real_escape_string($conn, $row[2]) : null;
            $semester = isset($row[3]) ? intval($row[3]) : null;
            $subject_code = isset($row[4]) ? mysqli_real_escape_string($conn, $row[4]) : null;
            $subject_name = isset($row[5]) ? mysqli_real_escape_string($conn, $row[5]) : null;
            $paper_type = isset($row[6]) ? mysqli_real_escape_string($conn, $row[6]) : null;
            $credit = isset($row[7]) ? intval($row[7]) : null;
            $min_marks = isset($row[8]) ? intval($row[8]) : null;
            $max_marks = isset($row[9]) ? intval($row[9]) : null;

          if($scheme=='Scheme'){
            continue;
          }

          if($min_marks>$max_marks){
            $export_data[] = array_merge($row, ['Min Marks cannot be greater than Max Marks.']);
            continue;
          }
          
          $scheme = $conn->query("SELECT ID FROM Schemes WHERE University_ID = ".$_SESSION['university_id']." AND Name LIKE '$scheme'");
          if($scheme->num_rows==0){
            $export_data[] = array_merge($row, ['Scheme not found!']);
            continue;
          }

          $scheme = $scheme->fetch_assoc();
          $scheme_id = $scheme['ID'];

          $course = $conn->query("SELECT ID FROM Courses WHERE University_ID = ".$_SESSION['university_id']." AND (Name LIKE '$course' OR Short_Name LIKE '$course')");
          if($course->num_rows==0){
            $export_data[] = array_merge($row, ['Course not found!']);
            continue;
          }

          $course_ids = array();
          while($course_id = $course->fetch_assoc()){
            $course_ids[] = $course_id['ID'];
          }

          $sub_course = $conn->query("SELECT ID, Course_ID FROM Sub_Courses WHERE University_ID = ".$_SESSION['university_id']." AND (Name LIKE '$sub_course' OR Short_Name LIKE '$sub_course') AND Scheme_ID = $scheme_id AND Course_ID IN (".implode(',', $course_ids).")");
          if($sub_course->num_rows==0){
            $export_data[] = array_merge($row, ['Sub-Course not found!']);
            continue;
          }

          $sub_course = $sub_course->fetch_assoc();
          $course_id = $sub_course['Course_ID'];
          $sub_course_id = $sub_course['ID'];

          $check = $conn->query("SELECT ID FROM Syllabi WHERE University_ID = ".$_SESSION['university_id']." AND Course_ID = $course_id AND Sub_Course_ID = $sub_course_id AND Scheme_ID = $scheme_id AND Code = '".$subject_code."'");
          if($check->num_rows>0){
            $export_data[] = array_merge($row, ['Subject Code already exists!']);
            continue;
          }

          $check = $conn->query("SELECT ID FROM Syllabi WHERE University_ID = ".$_SESSION['university_id']." AND Course_ID = $course_id AND Sub_Course_ID = $sub_course_id AND Scheme_ID = $scheme_id AND Name = '".$subject_name."'");
          if($check->num_rows>0){
            $export_data[] = array_merge($row, ['Subject Name already exists!']);
            continue;
          }
          $add = $conn->query("INSERT INTO `Syllabi`(`University_ID`, `Course_ID`, `Sub_Course_ID`, `Scheme_ID`, `Semester`, `Code`, `Name`, `Paper_Type`, `Credit`, `Min_Marks`, `Max_Marks`) VALUES (".$_SESSION['university_id'].", ".$course_id.", ".$sub_course_id.", ".$scheme_id.", $semester, '".$subject_code."', '".$subject_name."', '".$paper_type."', ".$credit.", ".$min_marks.", ".$max_marks.")");
          if($add){
            $export_data[] = array_merge($row, ['Subject added successfully!']);
          }else{
            $export_data[] = array_merge($row, ['Something went wrong!']);
          }
        }
      }
      unlink($uploadFilePath);
      $xlsx = SimpleXLSXGen::fromArray( $export_data )->downloadAs('Subjects Status '.date('h m s').'.xlsx');
    }
  }
?>
