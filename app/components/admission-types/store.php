<?php
  if(isset($_POST['name']) && isset($_POST['university_id'])){
    require '../../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $university_id = intval($_POST['university_id']);
    
    if(empty($name) || empty($university_id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Admission_Types WHERE Name LIKE '$name' AND University_ID = $university_id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=> $name.' already exists!']);
      exit();
    }
    
    $add = $conn->query("INSERT INTO `Admission_Types`(`Name`, `University_ID`) VALUES ('$name', $university_id)");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$name.' added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
