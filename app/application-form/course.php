<?php
  if(isset($_GET['university_id']) && isset($_GET['session_id']) && isset($_GET['admission_type_id']) && isset($_GET['center'])){
    require '../../includes/db-config.php';
    session_start();

    $ids = [];
    $university_id = intval($_GET['university_id']);
    $session_id = intval($_GET['session_id']);
    $admission_type_id = intval($_GET['admission_type_id']);
    $user_id = intval($_GET['center']);

    if(empty($admission_type_id)){
      echo '<option value="">Please configure Admission-Type</option>';
      exit();      
    }

    $admission_type = $conn->query("SELECT Name FROM Admission_Types WHERE ID = $admission_type_id");
    if($admission_type->num_rows==0){
      echo '<option value="">Please configure Admission-Type</option>';
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

    if(!empty($_GET['form'])){
      $status_query = "";
    }else{
      $status_query = " AND Status = 1";
    }

    $scheme = $conn->query("SELECT Scheme_ID FROM Admission_Sessions WHERE ID = $session_id");
    if($scheme->num_rows==0){
      echo '<option value="">Please configure Scheme</option>';
      exit();
    }
    $scheme = mysqli_fetch_assoc($scheme);
    $scheme = $scheme['Scheme_ID'];

    $is_vocational = $conn->query("SELECT ID FROM Universities WHERE Is_Vocational = 1 AND ID = $university_id");
    if($is_vocational->num_rows>0){
      $course_ids = $conn->query("SELECT Center_Sub_Courses.Course_ID FROM Center_Sub_Courses LEFT JOIN Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID WHERE `User_ID` = $user_id AND Center_Sub_Courses.University_ID = $university_id $status_query $query GROUP BY Center_Sub_Courses.Course_ID");  
    }else{
      $course_ids = $conn->query("SELECT Course_ID FROM Sub_Courses WHERE Scheme_ID = $scheme AND University_ID = $university_id $query $status_query GROUP BY Course_ID");
    }
    
    if($course_ids->num_rows==0){
      echo '<option value="">Please configure Academics</option>';
      exit();
    }

    while($course_id = $course_ids->fetch_assoc()){
      $ids[] = $course_id['Course_ID'];
    }
    
    $courses = $conn->query("SELECT Courses.ID, Courses.Name FROM Courses WHERE ID IN (".implode(',', $ids).")");
    while($course = $courses->fetch_assoc()){
      echo '<option value="'.$course['ID'].'">'.$course['Name'].'</option>';
    }
  }
