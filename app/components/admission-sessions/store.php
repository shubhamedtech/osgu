<?php
  if(isset($_POST['name']) && isset($_POST['university_id']) && isset($_POST['scheme'])){
    require '../../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $exam_session = mysqli_real_escape_string($conn, $_POST['exam_session']);
    $scheme = intval($_POST['scheme']);
    $university_id = intval($_POST['university_id']);
    
    if(empty($name) || empty($university_id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Admission_Sessions WHERE Name LIKE '$name' AND University_ID = $university_id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=> $name.' already exists!']);
      exit();
    }
    
    $add = $conn->query("INSERT INTO `Admission_Sessions` (`Name`, `Exam_Session`, `Scheme_ID`,  `University_ID`) VALUES ('$name', '$exam_session', $scheme, $university_id)");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$name.' added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
