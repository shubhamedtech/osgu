<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $students = $conn->query("SELECT ID FROM Students WHERE Sub_Course_ID = $id");
  if ($students->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Students exists!']);
    exit();
  }

  $students = $conn->query("SELECT ID FROM Center_Sub_Courses WHERE Sub_Course_ID = $id");
  if ($students->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'center_sub_courses exists!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Sub_Courses WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Fee_Constant WHERE Sub_Course_ID = $id");
    $delete = $conn->query("DELETE FROM Sub_Courses WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Specialization deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Specialization not exists!']);
  }
}
