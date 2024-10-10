<?php
if (isset($_POST['ids']) && isset($_POST['center'])) {
  require '../../../includes/db-config.php';
  require '../../../includes/helpers.php';
  session_start();

  $center = intval($_POST['center']);
  $ids = is_array($_POST['ids']) ? array_filter($_POST['ids']) : [];

  if (empty($ids)) {
    exit(json_encode(['status' => false, 'message' => 'Please select student!']));
  }

  $invoice_no = strtoupper(uniqid('IN'));

  $balance = array();

  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];

    $balance[] = balanceAmount($conn, $id, $duration);
  }

  $amount = array_sum($balance);
  $amount = $amount < 0 ? (-1) * $amount : $amount;
  echo json_encode(['status' => true, 'amount' => $amount,  'ids'=>$ids]);

  //echo json_encode(['status' => true, 'amount' => $amount]);
}
