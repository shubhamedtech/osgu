<?php
  if(isset($_POST['name']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    if(empty($name) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $add = $conn->query("UPDATE `Users` SET `Name` = '$name' WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Sub-Center updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
