<?php
if (isset($_POST['stage'])) {
  include '../../../includes/db-config.php';
  session_start();

  $stage = mysqli_real_escape_string($conn, $_POST['stage']);

  $check = $conn->query("SELECT ID FROM Stages WHERE `Name` LIKE '$stage'");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Stage already exists!']);
    exit();
  } else {
    $add = $conn->query("INSERT INTO Stages (`Name`) VALUES ('$stage')");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Stage added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  }
}
