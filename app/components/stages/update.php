<?php
if (isset($_POST['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $stage = mysqli_real_escape_string($conn, $_POST['stage']);
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  if (empty($stage) || empty($id)) {
    echo json_encode(['status' => 302, 'message' => 'Forbidden']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Stages WHERE Name LIKE '$stage' AND ID <> $id");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Stage already exists!']);
  } else {
    $update = $conn->query("UPDATE Stages SET Name = '$stage' WHERE ID = $id");
    if ($update) {
      echo json_encode(['status' => 200, 'message' => 'Stage updated successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }
}
