<?php
  if(isset($_POST['name']) && isset($_POST['university_id']) && isset($_POST['session'])){
    require '../../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sessions = is_array($_POST['session']) ? $_POST['session'] : [];
    $types = is_array($_POST['admission_type']) ? $_POST['admission_type'] : [];
    $semesters = is_array($_POST['semesters']) ? $_POST['semesters'] : [];
    $university_id = intval($_POST['university_id']);
    
    if(empty($name) || empty($university_id) || empty($sessions) || empty($semesters)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Exam_Sessions WHERE Name LIKE '$name' AND University_ID = $university_id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=> $name.' already exists!']);
      exit();
    }

    $admission_sessions = array();
    foreach($sessions as $key=>$session){
      $admission_sessions[$session] = explode(",",$semesters[$key]);
      $admission_types[$session] = $types[$key];
    }
    
    $add = $conn->query("INSERT INTO `Exam_Sessions` (`Name`, `Admission_Session`, `Admission_Type`, `University_ID`) VALUES ('$name', '".json_encode($admission_sessions)."', '".json_encode($admission_types)."', $university_id)");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$name.' added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
