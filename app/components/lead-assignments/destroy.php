<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $check_for_cities = $conn->query("SELECT ID FROM Cities WHERE State_ID = $id");
  if ($check_for_cities->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Cities exists for this state!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM States WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM States WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'State deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'State not exists!']);
  }
}
