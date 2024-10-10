<?php
  if(isset($_GET['email']) && isset($_GET['university_id'])){
    require '../../includes/db-config.php';
    session_start();

    $email =  mysqli_real_escape_string($conn, $_GET['email']);
    $university_id = mysqli_real_escape_string($conn, $_GET['university_id']);

    if(empty($university_id)){
      echo json_encode(['status'=>302, 'message'=>"Please select University!"]);
      exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>302, 'message'=>'Invalid email.']);
      exit();
    }

    $university_id_query = " AND Lead_Status.University_ID = $university_id";
    
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') $university_id_query");
    if($check->num_rows>0){
      echo json_encode(['status'=>302, 'message'=>'Email already exists!']);
    }else{
      echo json_encode(['status'=>200, 'message'=>'Email not exists!']);
    }
  }
