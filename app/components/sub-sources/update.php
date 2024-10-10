<?php
if (isset($_POST['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $sub_source = mysqli_real_escape_string($conn, $_POST['sub_source']);
  $source = mysqli_real_escape_string($conn, $_POST['source']);
  $id = mysqli_real_escape_string($conn, $_POST['id']);

  if (empty($source) || empty($id) || empty($sub_source)) {
    echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Sub_Sources WHERE Name LIKE '$sub_source' AND Source_ID = $source AND ID <> $id");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Sub-Channel already exists!']);
  } else {
    $update = $conn->query("UPDATE Sub_Sources SET Name = '$sub_source', Source_ID = $source WHERE ID = $id");
    if ($update) {
      echo json_encode(['status' => 200, 'message' => 'Sub-Channel updated successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }
}
