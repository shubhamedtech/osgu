<?php
if (isset($_GET['id']) && isset($_GET['university_id'])) {
  require '../../../includes/db-config.php';

  $id = intval($_GET['id']);
  $university_id = intval($_GET['university_id']);

  $check = $conn->query("SELECT ID,Center FROM Page_Access WHERE Page_ID = $id AND University_ID = $university_id");
  if ($check->num_rows > 0) {
    $access = $check->fetch_assoc();
    $status = $access['Center'] == 1 ? 0 : 1;
    $update = $conn->query("UPDATE Page_Access SET Center = $status WHERE ID = " . $access['ID'] . "");
  } else {
    $status = 1;
    $update = $conn->query("INSERT INTO Page_Access (`Page_ID`, `University_ID`, `Center`) VALUES ($id, $university_id, 1)");
  }

  if ($update) {
    $message = $status == 1 ? 'accessible' : 'unaccessible';
    echo json_encode(['status' => 200, 'message' => "Page is now $message to Center!"]);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
