<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $students = $conn->query("SELECT ID FROM Courses WHERE Department_ID = $id");
  if ($students->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Course(s) exists!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Departments WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Departments WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Department deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Department not exists!']);
  }
}
