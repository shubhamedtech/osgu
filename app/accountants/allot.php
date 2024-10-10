<?php
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';

    $id = intval($_POST['id']);
    $allot = array_key_exists('allot', $_POST) ? $_POST['allot'] : [];

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
        $head_id = $conn->query("SELECT ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE `Role` = 'University Head' AND University_User.University_ID = $university_id");
        $head_id = mysqli_fetch_assoc($head_id);
        $head_id = $head_id['ID'];
        $update = $conn->query("INSERT INTO University_User (`University_ID`, `User_ID`, `Reporting`) VALUES ($university_id, $id, $head_id)");
      }
      if($update){
        echo json_encode(['status'=>200, 'message'=>'Universities alloted successfully!']);
        exit();
      }
    }
    
  }
