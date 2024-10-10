<?php
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: GET');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['key'])){
    require '../../includes/db-config.php';

    $key = mysqli_real_escape_string($conn, $_GET['key']);

    $university = $conn->query("SELECT ID FROM Universities WHERE Api_Key = '".$key."'");
    if($university->num_rows==0){
      http_response_code(400);
      exit(json_encode(['status'=>false, 'message'=>'Invalid API Key!']));
    }

    $university = $university->fetch_assoc();
    $university_id = $university['ID'];

    $options = array();
    $users = $conn->query("SELECT CONCAT(UPPER(Users.Name) ,' (', Users.Code, ')') as Name, Users.ID FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Code = Users.ID WHERE University_ID = $university_id ORDER BY Users.Code ASC");
    if($users->num_rows==0){
      http_response_code(404);
      exit(json_encode(['status'=>false, 'message'=>'User not exists!']));
    }

    while($user = $users->fetch_assoc()){
      $options[$user['ID']] = $user['Name'];
    }

    echo json_encode(['status'=>true, 'options'=>$options]);
  }
