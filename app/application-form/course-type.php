<?php
  if(isset($_GET['course_id']) && isset($_GET['admission_type_id'])){
    require '../../includes/db-config.php';

    $course_id = intval($_GET['course_id']);
    $admission_type_id = intval($_GET['admission_type_id']);
    if(empty($course_id) || empty($admission_type_id)){
      echo 'Please select a course!';
      exit();
    }

    $type = false;
    $admission_type = $conn->query("SELECT Name FROM Admission_Types WHERE ID = $admission_type_id AND Name like 'Credit Transfer'");
    if($admission_type->num_rows>0){
      $type = true;
    }

    $course_type = $conn->query("SELECT Course_Types.Name FROM Courses LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID WHERE Courses.ID = $course_id");
    $course_type = mysqli_fetch_array($course_type);
    $course_type = $course_type['Name'];
    $course_type = substr($course_type, 0, 2);


    if(strcasecmp($course_type, 'ug')==0 && $type){
      echo 'UG';
    }elseif(strcasecmp($course_type, 'pg')==0 && $type){
      echo 'PG';
    }elseif(strcasecmp($course_type, 'pg')==0){
      echo 'UG';
    }else{
      echo '';
    }

  }
