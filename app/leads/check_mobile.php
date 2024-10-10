<?php
  if(isset($_GET['mobile']) && isset($_GET['university_id'])){
    require '../../includes/db-config.php';
    session_start();

    $university_id = mysqli_real_escape_string($conn, $_GET['university_id']);

    if(empty($university_id)){
      echo json_encode(['status'=>302, 'message'=>"Please select University!"]);
      exit();
    }

    $mobile =  mysqli_real_escape_string($conn, $_GET['mobile']);
    $mobile = trim($mobile);

    $university_id_query = " AND Lead_Status.University_ID = $university_id";
  
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '%$mobile' OR Alternate_Mobile LIKE '%$mobile') $university_id_query");
    if($check->num_rows>0){
      echo json_encode(['status'=>302, 'message'=>'Mobile already exists!']);
    }else{
      echo json_encode(['status'=>200, 'message'=>'Mobile not exists!']);
    }
  }
