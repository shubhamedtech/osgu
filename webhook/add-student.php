<?php
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: POST');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  if(isset($_POST['key'])){
    require '../includes/db-config.php';

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $full_name = str_replace('  ', ' ', $full_name);
    $full_name = explode(' ', $full_name, 3);
    $count = count($full_name);

    if($count==2){
      $first_name = trim($full_name[0]);
      $first_name = strtoupper(strtolower($first_name));
      $middle_name = NULL;
      $last_name = trim($full_name[1]);
      $last_name = strtoupper(strtolower($last_name));
    }elseif($count>2){
      $first_name = trim($full_name[0]);
      $first_name = strtoupper(strtolower($first_name));
      $middle_name = trim($full_name[1]);
      $middle_name = strtoupper(strtolower($middle_name));
      $last_name = trim($full_name[2]);
      $last_name = strtoupper(strtolower($last_name));
    }else{
      $first_name = trim($full_name[0]);
      $first_name = strtoupper(strtolower($first_name));
      $middle_name = NULL;
      $last_name = NULL;
    }


    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);
    
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);

    $university = mysqli_real_escape_string($conn, $_POST['university']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $sub_course = mysqli_real_escape_string($conn, $_POST['sub_course']);

    $center = mysqli_real_escape_string($conn, $_POST['added_for']);

    $check_university = $conn->query("SELECT ID FROM Universities WHERE Name like '$university'");
    if($check_university->num_rows==0){
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'University not exists in Panel!']);
      exit();
    }

    $university = $check_university->fetch_assoc();

    $check_course = $conn->query("SELECT ID FROM Courses WHERE Name like '$course' AND University_ID = ".$university['ID']."");
    if($check_course->num_rows==0){
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'Course not exists in Panel!']);
      exit();
    }

    $course = $check_course->fetch_assoc();

    $check_sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE Name like '$sub_course' AND University_ID = ".$university['ID']." AND Course_ID = ".$course['ID']."");
    if($check_sub_course->num_rows==0){
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'Sub-Course not exists in Panel!']);
      exit();
    }

    $sub_course = $check_sub_course->fetch_assoc();

    $mode = $conn->query("SELECT Mode_ID FROM Sub_Courses WHERE ID = ".$sub_course['ID']."");
    $mode = mysqli_fetch_assoc($mode);
    $mode = $mode['Mode_ID'];

    $admission_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = ".$university['ID']." AND Current_Status = 1");
    if($admission_session->num_rows==0){
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'Admission Session not configured!']);
      exit();
    }

    $admission_session = $admission_session->fetch_assoc();

    $admission_type = $conn->query("SELECT ID FROM Admission_Types WHERE University_ID = ".$university['ID']."");
    if($admission_type->num_rows==0){
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'Admission Type not configured!']);
      exit();
    }

    $admission_type = $admission_type->fetch_assoc();

    $center = $conn->query("SELECT ID FROM Users WHERE Code = '$center'");
    if($center->num_rows==0){
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'Employee not exists in Panel!']);
      exit();
    }

    $center = $center->fetch_assoc();

    $add_student = $conn->query("INSERT INTO Students (Added_By, Added_For, University_ID, Admission_Type_ID, Admission_Session_ID, Course_ID, Sub_Course_ID, Mode_ID, Duration, First_Name, Middle_Name, Last_Name, Email, Alternate_Email, Contact, Alternate_Contact, Step) VALUES(".$center['ID'].", ".$center['ID'].", ".$center['ID'].", ".$university['ID'].", ".$admission_type['ID'].", ".$admission_session['ID'].", ".$course['ID'].", ".$sub_course['ID'].", $mode, 1, '$first_name', '$middle_name', '$last_name', '$email', '$alternate_email', '$contact', '$alternate_contact', 1)");
    if($add_student){
      http_response_code(201);
      echo json_encode(['status'=>true, 'message'=>'Student added successlly!']);
    }else{
      http_response_code(400);
      echo json_encode(['status'=>false, 'message'=>'Something went wrong!']);
    }
  }
