<?php
  if(isset($_POST['id']) && $_POST['oa_number']){
    require '../../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $oa_number = mysqli_real_escape_string($conn, $_POST['oa_number']);

    if(empty($oa_number)){
      echo json_encode(['status'=>400, 'message'=>'OA Number is required.']);
      exit();
    }

    $update = $conn->query("UPDATE Students SET OA_Number = '$oa_number' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'OA Number updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
