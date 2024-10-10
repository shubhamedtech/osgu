<?php
  if(isset($_POST['id']) && isset($_POST['suffix'])){
    require '../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $suffix = mysqli_real_escape_string($conn, $_POST['suffix']);

    $update = $conn->query("UPDATE Universities SET Center_Suffix = '$suffix' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Center Code created successfully!']);
    }else{
      echo json_encode(['status'=>403, 'message'=>'Unable to create Center Code!']);
    }
  }
