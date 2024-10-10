<?php
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';

    $id = intval($_POST['id']);

    $status = $conn->query("SELECT CanCreateSubCenter FROM Users WHERE ID = $id");
    $status = mysqli_fetch_assoc($status);
    if($status['CanCreateSubCenter']==1){
      $update = $conn->query("UPDATE Users SET CanCreateSubCenter = 0 WHERE ID = $id");
    }else{
      $update = $conn->query("UPDATE Users SET CanCreateSubCenter = 1 WHERE ID = $id");
    }

    if($update){
      echo json_encode(['status'=>200, 'message'=>'Status changed successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
