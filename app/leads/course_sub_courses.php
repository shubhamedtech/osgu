<?php
  if(isset($_GET['course_id'])){
    require '../../includes/db-config.php';
    session_start();

    $course_id = intval($_GET['course_id']);
    
    $university_id = $conn->query("SELECT University_ID FROM Courses WHERE ID = $course_id");
    $university_id = $university_id->fetch_assoc();
    $university_id = $university_id['University_ID'];

    $scheme = $conn->query("SELECT Scheme_ID FROM Admission_Sessions WHERE University_ID = $university_id AND Current_Status = 1");
    $scheme = $scheme->fetch_assoc();
    $scheme_id = $scheme['Scheme_ID'];

    echo '<option value="">Choose</option>';
    $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE Course_ID = $course_id AND Scheme_ID = $scheme_id AND University_ID = $university_id");
    while ($sub_course = $sub_courses->fetch_assoc()){
      echo '<option value="'.$sub_course['ID'].'">'.$sub_course['Name'].'</option>';
    }
  }else{
    echo '<option value="">Choose</option>';
  }
