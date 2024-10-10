<?php
  if(isset($_GET['email'])){
    require '../../includes/db-config.php';
    session_start();

    $university = mysqli_real_escape_string($conn, $_GET['university']);
    $email =  mysqli_real_escape_string($conn, $_GET['email']);

    if(empty($university)){
      echo json_encode(['status'=>302, 'message'=>"Please select University!"]);
      exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>302, 'message'=>'Please enter a valid email!']);
      exit();
    }

    $department_query = " AND Lead_Status.University_ID = $university";
    
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') $department_query");
    if($check->num_rows>0){
      echo json_encode(['status'=>302, 'message'=>'Email already exists!']);
    }else{
      echo json_encode(['status'=>200, 'message'=>'Email not exists!']);
    }
  }
