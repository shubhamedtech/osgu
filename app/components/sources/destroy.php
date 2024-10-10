<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $check_for_category = $conn->query("SELECT ID FROM Leads WHERE Source_ID = $id");
  if ($check_for_category->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Leads exists for this Channel!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Sources WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Sources WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Channel deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Channel not exists!']);
  }
}
