<?php
  ini_set('display_errors', 1); 
  if(isset($_POST['course']) && isset($_POST['email']) && isset($_POST['full_name']) && isset($_POST['phone_number']) && isset($_POST['aadhar'])){
    require '../../includes/db-config.php';
    session_start();

    $center_id = intval($_POST['center']);
    $admission_session = intval($_POST['admission_session']);
    $admission_type = intval($_POST['admission_type']);
    $course = intval($_POST['course']);
    $sub_course = intval($_POST['sub_course']);
    $duration = intval($_POST['duration']);
    $phone_number = intval($_POST['phone_number']);
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $enrolment = mysqli_real_escape_string($conn, $_POST['Enrolment']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $employment_status = mysqli_real_escape_string($conn, $_POST['employment_status']);
    $marital_status = mysqli_real_escape_string($conn, $_POST['marital_status']);
    $religion = mysqli_real_escape_string($conn, $_POST['religion']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $aadhar = mysqli_real_escape_string($conn, $_POST['aadhar']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $dob = date('Y-m-d', strtotime($dob));

    // print_r($phone_number);
    // exit();
    $student_check = $conn->query("SELECT ID FROM Exam_Students WHERE Phone_Number = '$phone_number' AND Email = '$email' AND DOB = '$dob' AND University_ID = " . $_SESSION['university_id'] . " AND Course = $course");
    if ($student_check->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Student with same details already exists!']);
    exit();
    }
    $add = $conn->query("INSERT INTO Exam_Students (University_ID, Admission_Session, Admission_Type, Course, Sub_Course, Duration, Phone_Number, Name, Email, Enrolment_Number, Gender, Category, Emploment_Status, Marital_Status, Religion, Nationality, Aadhar, DOB, Status, Created_at, Updated_at) VALUES (" . $_SESSION['university_id'] . ", $admission_session, $admission_type, $course, $sub_course, $duration, $phone_number, '$name', '$email', '$enrolment', '$gender', '$category', '$employment_status', '$marital_status', '$religion','$nationality', '$aadhar', '$dob', 1, now(), now())");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Exam student added successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }