<?php
if (isset($_POST['ids']) && isset($_POST['center'])) {
  require '../../../../includes/db-config.php';
  require '../../../../includes/helpers.php';
  session_start();

  $center = intval($_POST['center']);
  $ids = is_array($_POST['ids']) ? array_filter($_POST['ids']) : [];

  if (empty($ids)) {
    exit(json_encode(['status' => false, 'message' => 'Please select student!']));
  }

  $invoice_no = strtoupper(uniqid('IN'));

  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];

    $balance = balanceAmount($conn, $id, $duration);

    $add = $conn->query("INSERT INTO Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES ($center, $id, $duration, " . $_SESSION['university_id'] . ", '$invoice_no', $balance)");
  }

  if ($add) {
    echo json_encode(['status' => true, 'message' => 'Invoice created successfully!']);
  } else {
    echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
  }
}
