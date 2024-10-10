<?php
  if(isset($_GET['id']) && isset($_GET['university_id'])){
    require '../../../includes/db-config.php';

    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);

    $update = $conn->query("UPDATE Admission_Sessions SET Current_Status = 0 WHERE University_ID = $university_id");
    $update = $conn->query("UPDATE Admission_Sessions SET Current_Status = 1 WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Current Status updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
