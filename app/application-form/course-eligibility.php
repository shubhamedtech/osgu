<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_GET['id']);

    $eligibility = $conn->query("SELECT Eligibility FROM Sub_Courses WHERE ID = $id");
    $eligibility = $eligibility->fetch_assoc();
    $eligibility = !empty($eligibility['Eligibility']) ? json_decode($eligibility['Eligibility'], true) : [];

    $eligibility = array_filter($eligibility);
    
    if(count($eligibility)>0){
      echo json_encode(['status'=>true, 'eligibility'=>$eligibility, 'count'=>count($eligibility)]);
    }else{
      echo json_encode(['status'=>false, 'eligibility'=>$eligibility, 'count'=>count($eligibility)]);
    }
  }
