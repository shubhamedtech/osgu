<?php
  if(isset($_POST['student_id']) && isset($_POST['column']) && isset($_POST['value']) && isset($_POST['exam_session'])){
    require '../../includes/db-config.php';
    
    $value = intval($_POST['value']);
    $value = $value==0 ? 1 : 0;
    $student_id = intval($_POST['student_id']);
    $column = mysqli_real_escape_string($conn, $_POST['column']);
    $exam_session = mysqli_real_escape_string($conn, $_POST['exam_session']);

    $update = $conn->query("UPDATE Results SET $column = $value WHERE Student_ID = $student_id AND Exam_Session = '$exam_session'");
    if($update){
      echo json_encode(['status'=>true, 'message'=>'Status updated successfully!']);
    }else{
      echo json_encode(['status'=>false, 'message'=>'Something went wrong!']);
    }
  }
