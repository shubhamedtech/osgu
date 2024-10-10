<?php 
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';

    $id = intval($_POST['id']);

    $mobile = $conn->query("SELECT Mobile FROM Users WHERE ID = $id");
    $mobile = mysqli_fetch_array($mobile);
    echo $mobile['Mobile'];
  }
