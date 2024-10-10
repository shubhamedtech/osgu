<?php 
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';

    $id = intval($_POST['id']);

    $email = $conn->query("SELECT Email FROM Users WHERE ID = $id");
    $email = mysqli_fetch_array($email);
    echo $email['Email'];
  }
