<?php
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: GET');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['key'])){
    require '../../includes/db-config.php';
    session_start();

    $unique_id = $_GET['unique_id'];
    $enlorment_number = mysqli_real_escape_string($conn, $_GET['Elorment_number']);

    $key = mysqli_real_escape_string($conn, $_GET['key']);

    $university = $conn->query("SELECT ID FROM Universities WHERE Api_Key = '".$key."'");
    if($university->num_rows==0){
      http_response_code(400);
      exit(json_encode(['status'=>false, 'message'=>'Invalid API Key!']));
    }

    if (!empty($unique_id) && !empty($enlorment_number)) {
      $add_enrollment_no = $conn->query("UPDATE Students SET Enrollment_No = $enlorment_number WHERE Unique_ID = $unique_id");
      if ($add_enrollment_no) {
        echo json_encode(['status' => 200, 'message' => 'Student Enrolled succesfully']);
      } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
      }
    } else {
      echo json_encode(['status' => 400, 'message' => 'UUID and Enrollment_No should not be null']);
    }
  }else {
    echo json_encode(['status' => 400, 'message' => 'Key required']);
  }
