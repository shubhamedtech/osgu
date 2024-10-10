<?php
if (isset($_POST['id']) && isset($_POST['column'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $column = mysqli_real_escape_string($conn, $_POST['column']);

  if (empty($id) || empty($column)) {
    echo json_encode(['status' => 403, 'message' => 'Forbidden']);
    exit();
  }

  $get_status = $conn->query("SELECT $column FROM Universities WHERE ID = $id");
  if ($get_status->num_rows > 0) {
    $status = mysqli_fetch_assoc($get_status);
    if ($status[$column] == 1) {
      $update = $conn->query("UPDATE Universities SET $column = 0 WHERE ID = $id");
    } else {
      $update = $conn->query("UPDATE Universities SET $column = 1 WHERE ID = $id");
    }
    if ($update) {
      echo json_encode(['status' => 200, 'message' => 'Status changed successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 404, 'message' => 'No record found!']);
  }
} else {
  echo json_encode(['status' => 403, 'message' => 'Forbidden']);
  exit();
}
