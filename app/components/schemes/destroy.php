<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../../includes/db-config.php';

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $admission_sessions = $conn->query("SELECT ID FROM admission_sessions WHERE Scheme_ID = $id");
  if ($admission_sessions->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'This schemes already exist in admission sessions!']);
    exit();
  }

  // $students = $conn->query("SELECT ID FROM admission_sessions WHERE Scheme_ID = $id");
  // if ($students->num_rows > 0) {
  //   echo json_encode(['status' => 302, 'message' => 'This schemes already exist in admission sessions exists!']);
  //   exit();
  // }

  $check = $conn->query("SELECT ID FROM Schemes WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Schemes WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Scheme deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Scheme not exists!']);
  }
}
