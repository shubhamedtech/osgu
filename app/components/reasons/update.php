<?php
if (isset($_POST['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $reason = mysqli_real_escape_string($conn, $_POST['reason']);
  $stage = mysqli_real_escape_string($conn, $_POST['stage']);
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  if (empty($stage) || empty($id) || empty($reason)) {
    echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Reasons WHERE Name LIKE '$reason' AND Stage_ID = $stage AND ID <> $id");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Reason already exists!']);
  } else {
    $update = $conn->query("UPDATE Reasons SET Name = '$reason', Stage_ID = $stage WHERE ID = $id");
    if ($update) {
      echo json_encode(['status' => 200, 'message' => 'Reason updated successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }
}
