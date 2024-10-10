<?php
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: GET');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['key'])){
    require '../../includes/db-config.php';

    $key = mysqli_real_escape_string($conn, $_GET['key']);

    $university = $conn->query("SELECT ID FROM Universities WHERE Api_Key = '".$key."'");
    if($university->num_rows==0){
      http_response_code(400);
      exit(json_encode(['status'=>false, 'message'=>'Invalid API Key!']));
    }

    $university = $university->fetch_assoc();
    $university_id = $university['ID'];

    $options = array();
    $courses = $conn->query("SELECT Courses.ID, Courses.Name FROM Courses WHERE University_ID = $university_id ORDER BY Courses.Name ASC");
    if($courses->num_rows==0){
      http_response_code(404);
      exit(json_encode(['status'=>false, 'message'=>'Course not exists!']));
    }

    while($course = $courses->fetch_assoc()){
      $options[$course['ID']] = $course['Name'];
    }

    echo json_encode(['status'=>true, 'options'=>$options]);
  }
