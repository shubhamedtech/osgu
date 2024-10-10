<?php
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: POST');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  if(isset($_POST['key'])){
    require '../../../includes/db-config.php';

    $key = mysqli_real_escape_string($conn, $_POST['key']);

    if(empty($key)){
      http_response_code(403);
      echo json_encode(['status'=>false, 'message'=>'Key cannot be empty!']);
      exit();
    }

    $crm = $conn->query("SELECT * FROM Integrations WHERE Name = 'CRM'");
    if($crm->num_rows>0){
      $crm = $crm->fetch_assoc();
      $crm_key = $crm['Key'];

      if($key==$crm_key){
        $conn->query("UPDATE Integrations SET Verified = now() WHERE ID = ".$crm['ID']."");
        http_response_code(200);
        echo json_encode(['status'=>true, 'message'=>'Key verified successfully!']);
      }else{
        http_response_code(400);
        echo json_encode(['status'=>false, 'message'=>'Key not matched!']);
      }
    }else{
      http_response_code(403);
      echo json_encode(['status'=>false, 'message'=>'Please contact your provider!']);
    }
  }else{
    http_response_code(403);
    echo json_encode(['status'=>false, 'message'=>'Forbidden']);
  }
