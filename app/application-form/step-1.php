<?php
if (isset($_POST['center'])) {
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';
  session_start();

  $id = array_key_exists('inserted_id', $_POST) ? intval($_POST['inserted_id']) : 0;
  $lead_id = intval($_POST['lead_id']);
  $center = intval($_POST['center']);
  $admission_session = intval($_POST['admission_session']);
  $admission_type = intval($_POST['admission_type']);
  $course = intval($_POST['course']);
  $sub_course = intval($_POST['sub_course']);
  $duration = intval($_POST['duration']);

  $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
  $full_name = str_replace('  ', ' ', $full_name);
  $full_name = explode(' ', $full_name, 3);
  $count = count($full_name);

  if ($count == 2) {
    $first_name = trim($full_name[0]);
    $first_name = strtoupper(strtolower($first_name));
    $middle_name = NULL;
    $last_name = trim($full_name[1]);
    $last_name = strtoupper(strtolower($last_name));
  } elseif ($count > 2) {
    $first_name = trim($full_name[0]);
    $first_name = strtoupper(strtolower($first_name));
    $middle_name = trim($full_name[1]);
    $middle_name = strtoupper(strtolower($middle_name));
    $last_name = trim($full_name[2]);
    $last_name = strtoupper(strtolower($last_name));
  } else {
    $first_name = trim($full_name[0]);
    $first_name = strtoupper(strtolower($first_name));
    $middle_name = NULL;
    $last_name = NULL;
  }

  $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
  $father_name = strtoupper(strtolower($father_name));
  $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
  $mother_name = strtoupper(strtolower($mother_name));

  $dob = mysqli_real_escape_string($conn, $_POST['dob']);
  $dob = date('Y-m-d', strtotime($dob));

  $gender = mysqli_real_escape_string($conn, $_POST['gender']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $employment_status = mysqli_real_escape_string($conn, $_POST['employment_status']);
  $marital_status = mysqli_real_escape_string($conn, $_POST['marital_status']);
  $religion = mysqli_real_escape_string($conn, $_POST['religion']);
  $aadhar = mysqli_real_escape_string($conn, $_POST['aadhar']);
  $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);

  // if(empty($center) || empty($admission_session) || empty($admission_type) || empty($course) || empty($sub_course) || empty($duration) || empty($first_name)){
  //   echo json_encode(['status'=>400, 'message'=>'All fields are required']);
  //   exit();
  // }

  $mode = $conn->query("SELECT Mode_ID FROM Sub_Courses WHERE ID = $sub_course");
  $mode = mysqli_fetch_assoc($mode);
  $mode = $mode['Mode_ID'];

  if (!empty($id)) {
    $add_student = $conn->query("UPDATE Students SET Admission_Type_ID = $admission_type, Admission_Session_ID = $admission_session, Course_ID = $course, Sub_Course_ID = $sub_course, Mode_ID = $mode, Duration = $duration, First_Name = '$first_name', Middle_Name = '$middle_name', Last_Name = '$last_name', Father_Name = '$father_name', Mother_Name = '$mother_name', DOB = '$dob', Aadhar_Number = '$aadhar', Category = '$category', Gender = '$gender', Nationality = '$nationality', Employement_Status = '$employment_status', Marital_Status = '$marital_status', Religion = '$religion' WHERE ID = $id");
    if ($add_student) {
      generateStudentLedger($conn, $id);
      echo json_encode(['status' => 200, 'message' => 'Step 1 details saved successfully!', 'id' => $id]);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  } else {

    $aadhar_check = $conn->query("SELECT ID FROM Students WHERE Aadhar_Number = '$aadhar' AND University_ID = " . $_SESSION['university_id'] . "");
    if ($aadhar_check->num_rows > 0) {
      echo json_encode(['status' => 400, 'message' => 'Aadhar number already exists!']);
      exit();
    }

    $student_check = $conn->query("SELECT ID FROM Students WHERE First_Name = '$first_name' AND Father_Name = '$father_name' AND Mother_Name = '$mother_name' AND DOB = '$dob' AND University_ID = " . $_SESSION['university_id'] . " AND Course_ID = $course AND Added_For = $center");
    if ($student_check->num_rows > 0) {
      echo json_encode(['status' => 400, 'message' => 'Student with same details already exists!']);
      exit();
    }

    $add_student = $conn->query("INSERT INTO Students (Added_By, Added_For, University_ID, Admission_Type_ID, Admission_Session_ID, Course_ID, Sub_Course_ID, Mode_ID, Duration, First_Name, Middle_Name, Last_Name, Father_Name, Mother_Name, DOB, Aadhar_Number, Category, Gender, Nationality, Employement_Status, Marital_Status, Religion, Step) VALUES(" . $_SESSION['ID'] . ", $center, " . $_SESSION['university_id'] . ", $admission_type, $admission_session, $course, $sub_course, $mode, $duration, '$first_name', '$middle_name', '$last_name', '$father_name', '$mother_name', '$dob', '$aadhar', '$category', '$gender', '$nationality', '$employment_status', '$marital_status', '$religion', 1)");
    if ($add_student) {
      $student_id = $conn->insert_id;
      
      if (empty($lead_id)) {
        $has_unique_student_id = $conn->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = " . $_SESSION['university_id'] . " AND Has_Unique_StudentID = 1");
        if ($has_unique_student_id->num_rows > 0) {
          $has_unique_student_id = $has_unique_student_id->fetch_assoc();
          $suffix = $has_unique_student_id['ID_Suffix'];
          $characters = $has_unique_student_id['Max_Character'];
          $unique_id = generateStudentID($conn, $suffix, $characters, $_SESSION['university_id']);
          $conn->query("UPDATE Students SET Unique_ID = '$unique_id' WHERE ID = $student_id");
        }
      } else {
        $unique_id = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE ID = $lead_id");
        $unique_id = $unique_id->fetch_assoc();
        $conn->query("UPDATE Students SET Unique_ID = '" . $unique_id['Unique_ID'] . "' WHERE ID = $student_id");

        $final_stage = $conn->query("SELECT ID FROM Stages WHERE Is_Last = 1");
        if ($final_stage->num_rows > 0) {
          $final_stage = $final_stage->fetch_assoc();
          $final_stage = $final_stage['ID'];
        } else {
          $final_stage = $conn->query("INSERT INTO Stages (`Name`, Is_Last) VALUES ('Admission Done', 1)");
          $final_stage = $conn->insert_id;
        }

        $conn->query("UPDATE Lead_Status SET Admission = 1, Stage_ID = $final_stage, Reason_ID = NULL WHERE ID = $lead_id");
      }

      generateStudentLedger($conn, $student_id);

      echo json_encode(['status' => 200, 'message' => 'Step 1 details saved successfully!', 'id' => $student_id]);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
  }
}
