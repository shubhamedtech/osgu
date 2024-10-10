<?php
if (isset($_POST['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $source = mysqli_real_escape_string($conn, $_POST['source']);
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  if (empty($source) || empty($id)) {
    echo json_encode(['status' => 302, 'message' => 'Forbidden']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Sources WHERE Name LIKE '$source' AND ID <> $id");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Channel already exists!']);
  } else {
    $update = $conn->query("UPDATE Sources SET Name = '$source' WHERE ID = $id");
    if ($update) {
      echo json_encode(['status' => 200, 'message' => 'Channel updated successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }
}
