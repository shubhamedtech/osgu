<?php
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';

    $id = intval($_POST['id']);
    $allot = array_key_exists('allot', $_POST) ? $_POST['allot'] : [];
    $reporting = array_key_exists('reporting', $_POST) ? $_POST['reporting'] : [];

    if(empty($allot)){
      $delete = $conn->query("DELETE FROM University_User WHERE `User_ID` = $id");
      if($delete){
        echo json_encode(['status'=>200, 'message'=>'Alloted university deleted successfully!']);
        exit();
      }
    }

    if(!empty($allot)){
      $conn->query("DELETE FROM University_User WHERE `User_ID` = $id");
      foreach($allot as $university_id){
        $update = $conn->query("INSERT INTO University_User (`University_ID`, `User_ID`, `Reporting`) VALUES ($university_id, $id, ".$reporting[$university_id].")");
      }
      if($update){
        echo json_encode(['status'=>200, 'message'=>'Universities alloted successfully!']);
        exit();
      }else{
        echo json_encode(['status'=>403, 'message'=>'Something went wrong!']);
      }
    }
  }
