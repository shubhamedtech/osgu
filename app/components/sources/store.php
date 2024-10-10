<?php
if (isset($_POST['source'])) {
  include '../../../includes/db-config.php';
  session_start();

  $source = mysqli_real_escape_string($conn, $_POST['source']);

  $check = $conn->query("SELECT ID FROM Sources WHERE `Name` LIKE '$source'");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Channel already exists!']);
    exit();
  } else {
    $add = $conn->query("INSERT INTO Sources (`Name`) VALUES ('$source')");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Channel added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  }
}
