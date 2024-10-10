<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  if ($_SESSION['Role'] != 'Center') {
    echo json_encode(['status' => 400, 'message' => 'You are not allowed to delete this!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Users WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Center_SubCenter WHERE `Sub_Center` = $id");
    $delete = $conn->query("DELETE FROM University_User WHERE `User_ID` = $id AND University_ID = " . $_SESSION['university_id']);
    $check = $conn->query("SELECT ID FROM University_User WHERE `User_ID` = $id");
    if ($check->num_rows == 0) {
      $delete = $conn->query("DELETE FROM Users WHERE ID = $id");
    }
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'User deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'User not exists!']);
  }
}
