<?php
  if(isset($_POST['full_name']) && isset($_POST['email']) && isset($_POST['mobile'])){
    require '../../includes/db-config.php';

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = strtoupper(strtolower($full_name));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      exit(json_encode(['status'=>400, 'message'=>'Invalid Email!']));
    }

    if(!preg_match('/^[0-9]{10}+$/', $mobile)){
      exit(json_encode(['status'=>400, 'message'=>'Invalid Mobile!']));
    }

    if (!preg_match("/^[a-zA-Z-' ]*$/",$full_name)) {
      exit(json_encode(['status'=>400, 'message'=>'Invalid Name!']));
    }

    $add = $conn->query("INSERT INTO `Partners`(`Full_Name`, `Mobile`, `Email`) VALUES ('$full_name', '$mobile', '$email')");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Account created successfully! Please wait for approval.']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong! Please try again later.']);
    }
  }