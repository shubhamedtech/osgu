<?php
if (isset($_POST['id']) && isset($_POST['university_id']) && isset($_POST['gateway_type']) && isset($_POST['access_key']) && isset($_POST['secret_key'])) {
  require '../../includes/db-config.php';

  $id = intval($_POST['id']);
  $university_id = intval($_POST['university_id']);
  $gateway_type = intval($_POST['gateway_type']);
  $access_key = mysqli_real_escape_string($conn, $_POST['access_key']);
  $secret_key = mysqli_real_escape_string($conn, $_POST['secret_key']);

  $check = $conn->query("SELECT ID FROM Payment_Gateways WHERE University_ID = $university_id AND ID <> $id");
  if ($check->num_rows > 0) {
    exit(json_encode(['status' => 400, 'message' => 'Gateway already exists!']));
  }

  $add = $conn->query("UPDATE Payment_Gateways SET `Type` = $gateway_type, `University_ID` = $university_id, `Access_Key` = '$access_key', `Secret_Key` = '$secret_key' WHERE ID = $id");
  if ($add) {
    echo json_encode(['status' => 200, 'message' => 'Payment Gateway updated successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
