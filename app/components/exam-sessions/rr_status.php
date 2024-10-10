<?php
  if(isset($_GET['id'])){
    require '../../../includes/db-config.php';

    $id = intval($_GET['id']);

    $status = $conn->query("SELECT RR_Status FROM Exam_Sessions WHERE ID = $id");
    $status = mysqli_fetch_assoc($status);

    $status = $status['RR_Status']==1 ? 0 : 1;

    $update = $conn->query("UPDATE Exam_Sessions SET RR_Status = $status WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Re-Reg Status updated successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
