<?php
  if(isset($_POST['id']) && isset($_POST['name'])){
    require '../../includes/db-config.php';
    session_start();
    $_SESSION['university_id'] = intval($_POST['id']);
    $_SESSION['university_name'] = mysqli_real_escape_string($conn, $_POST['name']);
    $_SESSION['university_logo'] = mysqli_real_escape_string($conn, $_POST['logo']);
    $_SESSION['unique_center'] = intval($_POST['unique_center']);
    $_SESSION['crm'] = intval($_POST['is_b2c']);
    echo json_encode(['status'=>200, 'message'=>'University updated successfully!']);
  }
