<?php
  if(isset($_POST['id']) && isset($_POST['suffix']) && isset($_POST['character'])){
    require '../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $suffix = mysqli_real_escape_string($conn, $_POST['suffix']);
    $character = mysqli_real_escape_string($conn, $_POST['character']);

    $update = $conn->query("UPDATE Universities SET ID_Suffix = '$suffix', Max_Character = '$character' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Student ID created successfully!']);
    }else{
      echo json_encode(['status'=>403, 'message'=>'Unable to create Student ID!']);
    }
  }
