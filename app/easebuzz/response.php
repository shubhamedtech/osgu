<?php
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (isset($_POST)) {
  require '../../includes/db-config.php';

  if (isset($_POST['response'])) {
    $response = is_array($_POST['response']) ? $_POST['response'] : [];

    if (empty($response)) {
      echo json_encode(['status' => false, 'message' => 'Payment Failed!']);
      exit();
    }

    if (strcasecmp($response['status'], 'success') == 0) {
      $gateway_id = $_POST['response']['easepayid'];
      $transaction_id = $_POST['response']['txnid'];
      $mode = $_POST['response']['mode'];
      $meta = json_encode(["msg" => $response]);

      $update = $conn->query("UPDATE Payments SET Gateway_ID = '$gateway_id', Payment_Mode = '$mode', Meta = '$meta', Status = 1 WHERE Transaction_ID = '$transaction_id' AND `Type` = 2");
      if ($update) {
        $sutdents = $conn->query("SELECT Student_ID FROM Invoices WHERE Invoice_No = '$transaction_id'");
        while ($sutdent = $sutdents->fetch_assoc()) {
          $conn->query("UPDATE Students SET Process_By_Center = now() WHERE ID = " . $sutdent['Student_ID']);
        }

        echo json_encode(['status' => true, 'message' => 'Payment updated!']);
      } else {
        echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
      }
    }
  }
}
