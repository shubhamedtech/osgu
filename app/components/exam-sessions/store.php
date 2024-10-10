<?php
if (isset($_POST['name']) && isset($_POST['university_id']) && isset($_POST['session']) && isset($_POST['table']) && isset($_POST['duration'])) {
  require '../../../includes/db-config.php';
  session_start();

  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $for = is_array($_POST['for']) ? array_filter($_POST['for']) : array();
  $tables = is_array($_POST['table']) ? array_filter($_POST['table']) : array();
  $sessions = is_array($_POST['session']) ? array_filter($_POST['session']) : array();
  $admissionTypes = is_array($_POST['admissionType']) ? array_filter($_POST['admissionType']) : array();
  $durations = is_array($_POST['duration']) ? $_POST['duration'] : array();
  $university_id = intval($_POST['university_id']);

  if (empty($name) || empty($university_id) || empty($for) || empty($sessions) || empty($durations)) {
    echo json_encode(['status' => 403, 'message' => 'All fields are mandatory!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Exam_Sessions WHERE Name LIKE '$name' AND University_ID = $university_id");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => $name . ' already exists!']);
    exit();
  }

  $admissionSessions = array();
  foreach ($for as $key => $value) {
    $admissionSessions[$key]['for'] = $value;
    $admissionSessions[$key]['table'] = $tables[$key];
    $admissionSessions[$key]['session'] = $sessions[$key];
    $admissionSessions[$key]['duration'] = explode(",", $durations[$key]);
    $admissionSessions[$key]['admissionType'] = array_key_exists($key, $admissionTypes) ? $admissionTypes[$key] : '';
  }

  $add = $conn->query("INSERT INTO `Exam_Sessions` (`Name`, `Admission_Session`, `University_ID`) VALUES ('$name', '" . json_encode($admissionSessions) . "', $university_id)");
  if ($add) {
    echo json_encode(['status' => 200, 'message' => $name . ' added successlly!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
