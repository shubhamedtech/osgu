<?php
  if(isset($_POST['name']) && isset($_POST['university_id']) && isset($_POST['constant']) && isset($_POST['applicable']) && isset($_POST['id'])){
    require '../../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $sharing = intval($_POST['sharing']);
    $constant = intval($_POST['constant']);
    $applicable = intval($_POST['applicable']);
    $university_id = intval($_POST['university_id']);

    if(empty($name) || empty($university_id) || empty($applicable) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    if($sharing && !$constant){
      echo json_encode(['status'=>400, 'message'=>'Fee sharing cannot be a variable!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Fee_Structures WHERE Name LIKE '$name' AND University_ID = $university_id AND ID <> $id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=> $name.' already exists!']);
      exit();
    }
    
    $add = $conn->query("UPDATE `Fee_Structures` SET `Name` = '$name', `Sharing` = $sharing, `Is_Constant` = $constant, `Fee_Applicable_ID` = $applicable WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$name.' updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
