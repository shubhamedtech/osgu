<?php
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    require '../../../includes/db-config.php';
    session_start();
    
    $id = intval($_GET['id']);
    
    $file = $conn->query("SELECT File FROM Downloads WHERE ID = $id");
    $file = $file->fetch_assoc();
    unlink('../../..'.$file['File']);
    
    $delete_details = $conn->query("DELETE FROM Downloads WHERE ID = $id");
    if($delete_details){
      echo json_encode(['status'=>200, 'message'=>'Date-Sheet deleted successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Server is not responding. Please try again later']);
    }
  }
