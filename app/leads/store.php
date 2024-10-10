<?php
if (isset($_POST['name'])) {
  require '../../includes/db-config.php';
  session_start();

  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
  $alternate_mobile = mysqli_real_escape_string($conn, $_POST['alternate_mobile']);
  $stage = intval($_POST['stage']);
  $reason = intval($_POST['reason']);
  $source = intval($_POST['source']);
  $sub_source = intval($_POST['sub_source']);

  if ($_SESSION['Role'] == 'Administrator') {
    $university_id = intval($_POST['university_id']);
    $user = intval($_POST['user']);
  } else {
    $university_id = $_SESSION['university_id'];
    $user = $_SESSION['ID'];
  }

  if (empty($user)) {
    echo json_encode(['status' => 302, 'message' => 'Please select lead owner!']);
    exit();
  }

  $university_query = " AND Lead_Status.University_ID = $university_id";
  $course = intval($_POST['course']);
  $sub_course = intval($_POST['sub_course']);
  $country = intval($_POST['country']);
  $state = intval($_POST['state']);
  if (empty($state)) {
    $state = NULL;
  }
  $extra = mysqli_real_escape_string($conn, $_POST['extra_info']);

  if (empty($name) || empty($mobile) || empty($source) || empty($course) || empty($stage) || empty($reason)) {
    echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
    exit();
  }

  if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 302, 'message' => 'Please enter a valid email!']);
      exit();
    }
  }

  if (!empty($email)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') $university_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Email already exists!']);
      exit();
    }
  }

  if (!empty($mobile)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '$mobile' OR Alternate_Mobile LIKE '$mobile') $university_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Mobile already exists!']);
      exit();
    }
  }

  if (!empty($alternate_mobile)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '$alternate_mobile' OR Alternate_Mobile LIKE '$alternate_mobile') $university_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Alternate Mobile already exists!']);
      exit();
    }
  }



  $conn->query('SET foreign_key_checks = 0');
  $check = $conn->query("SELECT ID FROM Leads WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') OR (Mobile LIKE '$mobile' OR Alternate_Mobile LIKE '$mobile')");
  if ($check->num_rows > 0) {
    $lead  = mysqli_fetch_assoc($check);
    $add_lead = true;
    $id = $lead['ID'];
  } else {
    $add_lead = $conn->query("INSERT INTO Leads (`Name`, `Email`, `Mobile`, `Alternate_Mobile`, `Source_ID`, `Sub_Source_ID`, `Country_ID`, `State_ID`, `Extra`, `Created_By`) VALUES ('$name', '$email', '$mobile', '$alternate_mobile', '$source', '$sub_source', '$country', '$state', '$extra', '" . $_SESSION['ID'] . "')");
    if ($add_lead) {
      $id = $conn->insert_id;
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }

  if ($add_lead) {
    $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `University_ID`, `Course_ID`, `Sub_Course_ID`, `Stage_ID`, `Reason_ID`, `User_ID`) VALUES ('$id', '$university_id', '$course', '$sub_course', '$stage', '$reason', '$user')");
    if ($add_lead_status) {
      echo json_encode(['status' => 200, 'message' => 'Lead added successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  }
} else {
  echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
}
