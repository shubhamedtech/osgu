<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  include '../../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $check_for_leads = $conn->query("SELECT ID FROM Leads WHERE Sub_Source_ID = $id");
  if ($check_for_leads->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Leads exists for this Sub-Channel']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Sub_Sources WHERE ID = $id");
  if ($check->num_rows > 0) {
    $delete = $conn->query("DELETE FROM Sub_Sources WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Sub-Channel deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Sub-Channel not exists!']);
  }
}
