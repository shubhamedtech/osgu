<?php
if (isset($_POST['source']) && isset($_POST['sub_source'])) {
  include '../../../includes/db-config.php';
  session_start();

  $source = mysqli_real_escape_string($conn, $_POST['source']);
  $sub_source = mysqli_real_escape_string($conn, $_POST['sub_source']);

  if (empty($source) || empty($sub_source)) {
    echo json_encode(['status' => 403, 'message' => 'All fields are required!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Sub_Sources WHERE `Name` LIKE '$sub_source' AND Source_ID = $source");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Sub-Channel already exists!']);
    exit();
  } else {
    $add = $conn->query("INSERT INTO Sub_Sources (`Name`, `Source_ID`) VALUES ('$sub_source', '$source')");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Sub-Channel added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  }
}
