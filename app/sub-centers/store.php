<?php
  if(isset($_POST['name']) && isset($_POST['reporting'])){
    require '../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $reporting = intval($_POST['reporting']);

    if(empty($name) || empty($reporting)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $center_code = $conn->query("SELECT Code FROM Users WHERE ID = $reporting");
    $center_code = mysqli_fetch_array($center_code);
    $center_code = $center_code['Code'];

    $all_reporting_user = $conn->query("SELECT Users.Code FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Sub_Center = Users.ID WHERE Center = $reporting ORDER BY Center_SubCenter.Sub_Center DESC LIMIT 1");
    if($all_reporting_user->num_rows>0){
      $code = mysqli_fetch_array($all_reporting_user);
      $code = $code['Code'];
      $code = str_replace($center_code.'.', '', $code);
      $new_code = $code+1;
      $code = $center_code.'.'.$new_code;
    }else{
      $code = $center_code.'.1';
    }
    
    $check = $conn->query("SELECT ID FROM Users WHERE Code like '$code'");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>'Code already exists!', 'code'=>$code]);
      exit();
    }
     
    $password = "12345";
    $add = $conn->query("INSERT INTO `Users`(`Name`, `Code`, `Password`, `Role`, `Designation`, `Photo`, `Created_By`) VALUES ('$name', '$code', AES_ENCRYPT('$password','60ZpqkOnqn0UQQ2MYTlJ'), 'Sub-Center', 'Sub-Center', '/assets/img/default-user.png', ".$_SESSION['ID'].")");
    $add = $conn->query("INSERT INTO `Center_SubCenter`(`Center`, `Sub_Center`) VALUES ($reporting, $conn->insert_id)");
    
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Sub-Center added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
