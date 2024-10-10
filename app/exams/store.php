<?php
  ini_set('display_errors', 1); 
  if(isset($_POST['exam_type']) && isset($_POST['exam_session']) && isset($_POST['name']) && isset($_POST['exam_start_date']) && isset($_POST['exam_end_time'])){
    require '../../includes/db-config.php';
    session_start();

    $exam_type = intval($_POST['exam_type']);
    $exam_session = intval($_POST['exam_session']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $start_date = mysqli_real_escape_string($conn, $_POST['exam_start_date']);
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = mysqli_real_escape_string($conn, $_POST['exam_end_date']);
    $end_date = date('Y-m-d', strtotime($end_date));
    $start_time = mysqli_real_escape_string($conn, $_POST['exam_start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['exam_end_time']);

    $add = $conn->query("INSERT INTO Exams (Exam_Type, Exam_Session_ID, University_ID, Name, Start_Date, End_Date, Start_Time, End_Time) VALUES ($exam_type, $exam_session, ".$_SESSION['university_id'].", '$name', '$start_date', '$end_date', '$start_time', '$end_time')");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Exam added successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }