<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';

  $id = intval($_POST['id']);

  $update = $conn->query("UPDATE Student_Pendencies SET Status = 1 WHERE Student_ID = $id");
  $update = $conn->query("UPDATE Students SET Document_Verified = now(), Payment_Received = now() WHERE ID = $id");
  if ($update) {
    echo json_encode(['status' => true, 'message' => "Document approved successfully!"]);
  } else {
    echo json_encode(['status' => false, 'message' => "Something went wrong!"]);
  }
}
