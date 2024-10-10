<?php
ini_set('display_errors', 1);
if (isset($_POST['student_id']) && isset($_POST['date_sheet_id']) && isset($_POST['syllabus_id']) && isset($_POST['answer'])) {
  require '../../includes/db-config.php';
  session_start();

  $student_id = intval($_POST['student_id']);
  $date_sheet_id = intval($_POST['date_sheet_id']);
  $syllabus_id = intval($_POST['syllabus_id']);

  $answers = is_array($_POST['answer']) && !empty($_POST['answer']) ? $_POST['answer'] : array();

  if (empty($answers)) {
    echo json_encode(['status' => 400, 'message' => 'No answer selected!']);
    exit;
  }

  foreach ($answers as $question => $answer) {
    $update = $conn->query("UPDATE Students_Answers SET Answer = '$answer' WHERE Student_ID = $student_id AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id AND Question_ID = $question");
  }

  if ($update) {
    echo json_encode(['status' => 200, 'message' => count($answers) . ' Answers submitted successfully!']);
  } else {
    echo json_encode(['status' => 400, 'Something went wrong!']);
  }
} else {
  echo json_encode(['status' => 400, 'message' => 'No answer selected!']);
}
