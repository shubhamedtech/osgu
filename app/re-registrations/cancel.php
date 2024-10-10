<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $check = $conn->query("SELECT ID, Student_ID FROM Re_Registrations WHERE ID = $id");
  if ($check->num_rows == 0) {
    exit(json_encode(['status' => false, 'message' => 'RR already cancelled!']));
  }
  $check = $check->fetch_assoc();
  $studentId = $check['Student_ID'];
  $delete = $conn->query("DELETE FROM Re_Registrations WHERE ID = $id");
  if ($delete) {
    $conn->query("UPDATE Students SET Duration = Duration-1 WHERE ID = $studentId");
    echo json_encode(['status' => true, 'message' => 'RR cancelled!']);
  } else {
    echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
  }
}
