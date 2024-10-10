<?php
if (isset($_POST['university_id']) && isset($_POST['gateway_type']) && isset($_POST['access_key']) && isset($_POST['secret_key'])) {
  require '../../includes/db-config.php';

  $university_id = intval($_POST['university_id']);
  $gateway_type = intval($_POST['gateway_type']);
  $access_key = mysqli_real_escape_string($conn, $_POST['access_key']);
  $secret_key = mysqli_real_escape_string($conn, $_POST['secret_key']);

  $add = $conn->query("INSERT INTO Payment_Gateways (`Type`, `University_ID`, `Access_Key`, `Secret_Key`) VALUES ($gateway_type, $university_id, '$access_key', '$secret_key')");
  if ($add) {
    echo json_encode(['status' => 200, 'message' => 'Payment Gateway added and activated successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
