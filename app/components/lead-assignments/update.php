<?php
if (isset($_POST['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $country = mysqli_real_escape_string($conn, $_POST['country']);
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  if (empty($country) || empty($id) || empty($state)) {
    echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM States WHERE `Name` LIKE '$country' AND Country_ID = $country AND ID <> $id");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'State already exists!']);
  } else {
    $update = $conn->query("UPDATE States SET Name = '$state', Country_ID = '$country' WHERE ID = $id");
    if ($update) {
      echo json_encode(['status' => 200, 'message' => 'State updated successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }
}
