

<?php
  ini_set('display_errors', 1); 
    if (isset($_GET['date_sheet_id']) && isset($_GET['syllabus_id']) && isset($_GET['id'])) {
        require '../../../includes/db-config.php';
        session_start();

        $date_sheet_id = intval($_GET['date_sheet_id']);
        $syllabus_id = intval($_GET['syllabus_id']);
        $student_id = intval($_GET['id']);

        $submited = $conn->query("INSERT INTO `Exam_Students_Final_Submit` (`Student_ID`, `Date_Sheet_ID`, `Syllabus_ID`, `Submited_At`) VALUES ($student_id, $date_sheet_id, $syllabus_id, now())");
        $data = [ 'status' => '200', 'message' => "Exam submitted successfuly"];
        header('Content-type: application/json');
        echo json_encode( $data );
    }
?>