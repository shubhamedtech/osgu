<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);

  if ($_SESSION['Role'] != 'Center') {
    $update = $conn->query("UPDATE Students SET Payment_Received = now() WHERE ID = $id");
    if ($update) {
      echo json_encode(['status' => true, 'message' => 'Payment marked as received!']);
    } else {
      echo json_encode(['status' => false, 'message' => 'Sorry, Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => false, 'message' => 'You are not authorized!']);
  }
}
