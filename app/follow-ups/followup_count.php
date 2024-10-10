<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    if(isset($_SESSION['university_id'])){
      $department_query = " AND Lead_Status.University_ID = ".$_SESSION['university_id']."";
    }else{
      $department_query = '';
    }

    if($id==0){
      $followup_query = " AND DATE(`At`) = CURDATE() AND Follow_Ups.Status = 0";
    }elseif($id==1){
      $followup_query = " AND DATE(`At`) = DATE(NOW() + INTERVAL 1 DAY) AND Follow_Ups.Status = 0";
    }elseif($id==2){
      $followup_query = " AND DATE(`At`) >= DATE(NOW() + INTERVAL 2 DAY) AND Follow_Ups.Status = 0";
    }elseif($id==3){
      $followup_query = " AND DATE(`At`) = NOW() - INTERVAL 3 MONTH AND Follow_Ups.Status = 0";
    }

    ## Role Query
    $role_query = str_replace("{{ table }}", "Follow_Ups", $_SESSION['RoleQuery']);
    $role_query = str_replace("{{ column }}", "User_ID", $role_query);

    ## Total number of records without filtering
    $all_count= $conn->query("SELECT COUNT(Follow_Ups.ID) as allcount FROM Follow_Ups LEFT JOIN Lead_Status ON Follow_Ups.Lead_ID = Lead_Status.Lead_ID AND Lead_Status.University_ID = Follow_Ups.University_ID WHERE Lead_Status.User_ID = Follow_Ups.User_ID $department_query $followup_query $role_query");
    $records = mysqli_fetch_assoc($all_count);
    echo $totalRecords = $records['allcount'];
  }
