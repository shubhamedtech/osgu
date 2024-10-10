<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_GET['id']);

  $check = $conn->query("SELECT Photo FROM Himalayan_ID_Cards WHERE ID = $id");
  if ($check->num_rows > 0) {
    $photo = $check->fetch_assoc();
    $file = "images/" . $photo['Photo'];
    if (file_exists($file)) {
      unlink($file);
    }
    $delete = $conn->query("DELETE FROM Himalayan_ID_Cards WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'ID Card not exists!']);
  }
}
