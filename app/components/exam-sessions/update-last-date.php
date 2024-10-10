<?php
if (isset($_POST['id']) && isset($_POST['type']) && isset($_POST['lastDate'])) {
  require '../../../includes/db-config.php';

  $id = intval($_POST['id']);
  $type = mysqli_real_escape_string($conn, $_POST['type']);
  $lastDate = mysqli_real_escape_string($conn, $_POST['lastDate']);
  $lastDate = !empty($lastDate) ? date("Y-m-d", strtotime($lastDate)) : '';
  $column = $type == 'RR' ? 'RR_Last_Date' : 'BP_Last_Date';

  if (empty($id) || empty($lastDate) || empty($type)) {
    exit(json_encode(['status' => false, 'message' => 'Mandatory field is required!']));
  }

  $update = $conn->query("UPDATE Exam_Sessions SET $column = '$lastDate' WHERE ID = $id");
  if ($update) {
    echo json_encode(['status' => true, 'message' => 'Updated successfully!']);
  } else {
    echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
  }
}
