<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = str_replace("UZJkrI5snMyURJgpMWbM", "", base64_decode($_GET['id']));

  $lead = $conn->query("SELECT Lead_ID,`User_ID` FROM Lead_Status WHERE ID = $id");
  if ($lead->num_rows == 0) {
    echo json_encode(['status' => 400, 'message' => 'Lead not found!']);
    exit();
  }

  $lead = $lead->fetch_assoc();
  $lead_id = $lead['Lead_ID'];
  $user_id = $lead['User_ID'];

  $check_for_followups = $conn->query("SELECT ID FROM Follow_Ups WHERE Lead_ID = $lead_id AND `User_ID` = $user_id AND Status = 0");
  if ($check_for_followups->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Follow-Up(s) exists for this lead!']);
    exit();
  }

  $delete = $conn->query("DELETE FROM Lead_Status WHERE ID = $id");
  if ($delete) {
    echo json_encode(['status' => 200, 'message' => 'Lead deleted successfully!']);
  } else {
    echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
  }
}
