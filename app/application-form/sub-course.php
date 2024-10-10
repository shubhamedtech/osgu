<?php
  if(isset($_GET['university_id']) && isset($_GET['session_id']) && isset($_GET['admission_type_id']) && isset($_GET['course_id']) && isset($_GET['center'])){
    require '../../includes/db-config.php';
    session_start();

    $university_id = intval($_GET['university_id']);
    $session_id = intval($_GET['session_id']);
    $admission_type_id = intval($_GET['admission_type_id']);
    $course_id = intval($_GET['course_id']);
    $user_id = intval($_GET['center']);

    if(empty($course_id)){
      echo '<option value="">Please add course</option>';
      exit();
    }

    if(empty($admission_type_id)){
      echo '<option value="">Please add admission-type</option>';
      exit();      
    }

    $admission_type = $conn->query("SELECT Name FROM Admission_Types WHERE ID = $admission_type_id");
    if($admission_type->num_rows==0){
      echo '<option value="">Please add admission-type</option>';
      exit();
    }
    $admission_type = mysqli_fetch_assoc($admission_type);
    $admission_type = $admission_type['Name'];

    $query = "";
    if(strcasecmp($admission_type, 'lateral')==0){
      $query = " AND `Lateral` = 1";
    }

    if(strcasecmp($admission_type, 'credit transfer')==0){
      $query = " AND `Credit_Transfer` = 1";
    }

    $scheme = $conn->query("SELECT Scheme_ID FROM Admission_Sessions WHERE ID = $session_id");
    if($scheme->num_rows==0){
      echo '<option value="">Please add scheme</option>';
      exit();
    }
    $scheme = mysqli_fetch_assoc($scheme);
    $scheme = $scheme['Scheme_ID'];

    $is_vocational = $conn->query("SELECT ID FROM Universities WHERE Is_Vocational = 1 AND ID = $university_id");
    if($is_vocational->num_rows>0){
      $ids = array();
      $sub_course_ids = $conn->query("SELECT DISTINCT Sub_Course_ID FROM Center_Sub_Courses WHERE `User_ID` = $user_id AND University_ID = $university_id AND Course_ID = $course_id");
      while($sub_course_id = $sub_course_ids->fetch_assoc()){
        $ids[] = $sub_course_id['Sub_Course_ID'];
      }

      if(empty($ids)){
        echo '<option value="">Please add sub-course</option>';
        exit();
      }

      $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE ID IN (".implode(',', $ids).") AND University_ID = $university_id AND Course_ID = $course_id $query");

    }else{
      $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE Scheme_ID = $scheme AND University_ID = $university_id AND Course_ID = $course_id $query");
    }
    
    if($sub_courses->num_rows==0){
      echo '<option value="">Please add sub-course</option>';
      exit();
    }

    while($sub_course = $sub_courses->fetch_assoc()){
      echo '<option value="'.$sub_course['ID'].'">'.$sub_course['Name'].'</option>';
    }
  }
