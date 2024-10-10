<?php
if (isset($_POST['id'])) {
  session_start();
  require '../../includes/db-config.php';

  $id = intval($_POST['id']);
  $report = is_array($_POST['report']) ? array_filter($_POST['report']) : [];
  $remark = is_array($_POST['remark']) ? array_filter($_POST['remark']) : [];

  if (empty($report) || empty($remark)) {
    exit(json_encode(['status' => 400, 'message' => 'Fields are mandatory!']));
  }

  $pendency = array();

  foreach ($report as $value) {
    $value = str_replace(" ", "_", $value);
    $pendency[$value] = $remark[$value];
  }

  $conn->query("UPDATE Student_Pendencies SET Status = 1 WHERE Student_ID = $id");

  $update = $conn->query("INSERT INTO Student_Pendencies (`Added_By`, `Student_ID`, `Pendency`) VALUES (" . $_SESSION['ID'] . ", " . $id . ", '" . json_encode($pendency) . "')");
  if ($update) {
    echo json_encode(["status" => 200, 'message' => 'Pendency marked successfully!']);
  } else {
    echo json_encode(["status" => 400, 'message' => 'Something went wrong!']);
  }
}
