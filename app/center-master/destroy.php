<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $students = $conn->query("SELECT ID FROM Alloted_Center_To_Counsellor WHERE Code = $id");
    if ($students->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'This center alloted to counsellor exists!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Users WHERE ID = $id");
    if($check->num_rows>0){
      $delete = $conn->query("DELETE FROM Users WHERE ID = $id");
      if($delete){
        echo json_encode(['status'=>200, 'message'=>'Center deleted successfully!']);
      }else{
        echo json_encode(['status'=>302, 'message'=>'Please remove alloted universities!']);
      }
    }else{
      echo json_encode(['status'=>302, 'message'=>'Center not exists!']);
    }
  }
