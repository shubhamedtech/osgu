<?php
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: GET');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['key']) && isset($_GET['course_id'])){
    require '../../includes/db-config.php';

    $key = mysqli_real_escape_string($conn, $_GET['key']);
    $course_id = intval($_GET['course_id']);

    $university = $conn->query("SELECT ID FROM Universities WHERE Api_Key = '".$key."'");
    if($university->num_rows==0){
      http_response_code(400);
      exit(json_encode(['status'=>false, 'message'=>'Invalid API Key!']));
    }

    $university = $university->fetch_assoc();
    $university_id = $university['ID'];

    $scheme = $conn->query("SELECT Scheme_ID FROM Admission_Sessions WHERE University_ID = $university_id AND Current_Status = 1");
    if($scheme->num_rows==0){
      http_response_code(400);
      exit(json_encode(['status'=>false, 'message'=>'Please configure Admission Session!']));
    }

    $scheme = $scheme->fetch_assoc();
    $scheme_id = $scheme['Scheme_ID'];

    $options = array();
    $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE University_ID = $university_id AND Course_ID = $course_id AND Scheme_ID = $scheme_id");
    if($sub_courses->num_rows==0){
      http_response_code(404);
      exit(json_encode(['status'=>false, 'message'=>'Sub-Course not exists!']));
    }

    while($sub_course = $sub_courses->fetch_assoc()){
      $options[$sub_course['ID']] = $sub_course['Name'];
    }

    echo json_encode(['status'=>true, 'options'=>$options]);
  }
