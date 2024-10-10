<?php
  if(isset($_POST['password']) && isset($_POST['confirm_password'])){
    include '../../includes/db-config.php';
    session_start();

    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if(empty($password) || empty($confirm_password)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    if($password!==$confirm_password){
      echo json_encode(['status'=>403, 'message'=>'Password not matched!']);
      exit();
    }

    $update = $conn->query("UPDATE Users SET `Password` = AES_ENCRYPT('$password','60ZpqkOnqn0UQQ2MYTlJ') WHERE ID = ".$_SESSION['ID']."");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Password updated successlly!']);
    }else{
      echo json_encode(['status'=>403, 'message'=>'Something went wrong!']);
    }
  }
