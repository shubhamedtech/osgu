<?php
if (isset($_POST['stage']) && isset($_POST['reason'])) {
  include '../../../includes/db-config.php';
  session_start();

  $stage = mysqli_real_escape_string($conn, $_POST['stage']);
  $reason = mysqli_real_escape_string($conn, $_POST['reason']);

  if (empty($stage) || empty($reason)) {
    echo json_encode(['status' => 403, 'message' => 'All fields are required!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Reasons WHERE `Name` LIKE '$reason' AND Stage_ID = $stage");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Reason already exists!']);
    exit();
  } else {
    $add = $conn->query("INSERT INTO Reasons (`Name`, `Stage_ID`) VALUES ('$reason', '$stage')");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Reason added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  }
}
