<?php
  session_start();
  if($_SESSION['Role']=='Administrator'){
    include '../includes/db-config.php';
    include '../includes/helpers.php';
    $has_unique_student_id = $conn->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = ".$_SESSION['university_id']." AND Has_Unique_StudentID = 1");
    if($has_unique_student_id->num_rows>0){
      $has_unique_student_id = $has_unique_student_id->fetch_assoc();
      $suffix = $has_unique_student_id['ID_Suffix'];
      $characters = $has_unique_student_id['Max_Character'];
      $students = $conn->query("SELECT ID FROM Students WHERE University_ID = ".$_SESSION['university_id']." AND Unique_ID IS NULL");
      while($student = $students->fetch_assoc()){
        echo $student_id = $student['ID'];
        echo '<br>';
        $unique_id = generateStudentID($conn, $suffix, $characters, $_SESSION['university_id']);
        $conn->query("UPDATE Students SET Unique_ID = '$unique_id' WHERE ID = $student_id");
      }
    }
  }
