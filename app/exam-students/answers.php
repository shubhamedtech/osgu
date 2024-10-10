<?php
ini_set('display_errors', 1);
if (isset($_POST['student_id']) && isset($_POST['date_sheet_id']) && isset($_POST['syllabus_id']) && isset($_POST['answer'])) {
  require '../../includes/db-config.php';
  session_start();
  $student_id = intval($_POST['student_id']);
  $date_sheet_id = intval($_POST['date_sheet_id']);
  $syllabus_id = intval($_POST['syllabus_id']);

  $answers = is_array($_POST['answer']) && !empty($_POST['answer']) ? $_POST['answer'] : array();
  foreach ($answers as $question => $answer) {
    $answer_check = $conn->query("SELECT * FROM Exam_Students_Answers WHERE Answer = '$answer' AND Student_ID = $student_id AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id AND Question_ID = $question");
    $check = $conn->query("SELECT Answer FROM Exam_Students_Answers WHERE Student_ID = $student_id AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id AND Question_ID = $question");
    if($answer_check->num_rows == 0 ){
      $update = $conn->query("UPDATE Exam_Students_Answers SET Answer = '$answer' WHERE Student_ID = $student_id AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id AND Question_ID = $question");
	  $check_ans = $check->fetch_assoc();
      if ($update && empty($check_ans['Answer'])) {
        echo json_encode(['status' => 200, 'data' => $question, 'message' => count($answers) . ' Answers submitted successfully!']);
      } else {
        echo json_encode(['status' => 400, 'Something went wrong!']);
      }
    }
  }
}
