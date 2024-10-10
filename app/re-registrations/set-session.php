<?php
if (isset($_POST['id']) && isset($_POST['name'])) {
  session_start();
  require '../../includes/db-config.php';

  $_SESSION['active_rr_session_id'] = intval($_POST['id']);
  $_SESSION['active_rr_session_name'] = $_POST['name'];

  $examSession = $conn->query("SELECT RR_Status, RR_Last_Date FROM Exam_Sessions WHERE ID = " . $_SESSION['active_rr_session_id']);
  $examSession = $examSession->fetch_assoc();

  $showAction = 0;
  if ($examSession['RR_Status'] == 1) {
    $showAction = 1;
  }

  if (!empty($examSession['RR_Last_Date']) && $examSession['RR_Last_Date'] < date("Y-m-d")) {
    $showAction = 0;
  }

  $_SESSION['show_action_in_active_rr'] = $showAction;

  echo json_encode(['status' => true]);
}
