<?php
  if(isset($_POST['id']) && $_POST['unique_id']){
    require '../../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $unique_id = mysqli_real_escape_string($conn, trim($_POST['unique_id']));

    if(empty($unique_id)){
      echo json_encode(['status'=>400, 'message'=>'Student ID is required.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Lead_Status WHERE Unique_ID = $unique_id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>'Student ID already exists.']);
    }

    $update = $conn->query("UPDATE Lead_Status SET Unique_ID = '$unique_id' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Student ID updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
