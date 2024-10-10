<?php
  if(isset($_POST['email']) && isset($_POST['contact'])){
    require '../../includes/db-config.php';
    session_start();

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>400, "message"=>"Invalid email!"]);
    }

    $conn->query("DELETE FROM Contact_Us WHERE University_ID = ".$_SESSION['university_id']."");
    $update = $conn->query("INSERT INTO Contact_Us (`University_ID`,`Email`,`Mobile`) VALUES (".$_SESSION['university_id'].",'$email','$contact')");
    if($update) {
      echo json_encode(['status'=>200, "message"=>"Contact Details updated successfully!"]);
    }else{
      echo json_encode(['status'=>400, "message"=>"Something went wrong!"]);
    }
  }