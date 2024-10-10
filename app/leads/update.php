<?php
if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['alternate_mobile']) && isset($_POST['user_id']) && isset($_POST['university_id'])) {
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';
  session_start();

  $id = intval($_POST['id']);
  $user_id = intval($_POST['user_id']);
  $university_id = intval($_POST['university_id']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
  $alternate_mobile = mysqli_real_escape_string($conn, $_POST['alternate_mobile']);
  $course = intval($_POST['course']);
  $sub_course = intval($_POST['sub_course']);
  $sub_course_query = !empty($sub_course) ? ", Sub_Course_ID = $sub_course" : '';
  $country = intval($_POST['country']);
  $country_query = !empty($country) ? " Country_ID = $country," : '';
  $state = intval($_POST['state']);
  $state_query = !empty($state) ? " State_ID = $state," : '';
  $extra_info = $_POST['extra_info'];
  $department_query = " AND Lead_Status.ID <> $id AND Lead_Status.University_ID = $university_id";

  $lead = $conn->query("SELECT Leads.ID, Lead_Status.User_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.ID = $id");
  if ($lead->num_rows > 0) {
    $lead = mysqli_fetch_assoc($lead);
  } else {
    echo json_encode(['status' => 302, 'message' => 'Lead not exists!']);
    exit();
  }

  if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 302, 'message' => 'Please enter a valid email!']);
      exit();
    }
  }

  if (!empty($alternate_email)) {
    if (!filter_var($alternate_email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 302, 'message' => 'Please enter a valid alternate email!']);
      exit();
    }
  }

  if (!empty($email)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') $department_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Email already exists!']);
      exit();
    }
  }

  if (!empty($alternate_email)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$alternate_email' OR Alternate_Email LIKE '$alternate_email') $department_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Alternate Email already exists!']);
      exit();
    }
  }

  if (!empty($mobile)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '$mobile' OR Alternate_Mobile LIKE '$mobile') $department_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Mobile already exists!']);
      exit();
    }
  }

  if (!empty($alternate_mobile)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '$alternate_mobile' OR Alternate_Mobile LIKE '$alternate_mobile') $department_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Alternate Mobile already exists!']);
      exit();
    }
  }

  // TODO:Lead History
  $old_lead_details = $conn->query("SELECT Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS Department,Courses.Name AS Course,Sub_Courses.Name AS Sub_Course,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID=$id");
  $old_lead_details = $old_lead_details->fetch_assoc();

  $update = $conn->query("UPDATE Leads SET `Name` = '$name', `Email` = '$email', `Alternate_Email` = '$alternate_email', `Alternate_Mobile` = '$alternate_mobile', $country_query $state_query `Extra` = '$extra_info' WHERE `ID` = " . $lead['ID'] . "");
  if ($update) {
    $update_status = $conn->query("UPDATE Lead_Status SET `Course_ID` = $course $sub_course_query WHERE ID = $id");
    if ($update_status) {
      $new_lead_details = $conn->query("SELECT Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS Department,Courses.Name AS Course,Sub_Courses.Name AS Sub_Course,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID=$id");
      $new_lead_details = $new_lead_details->fetch_assoc();
      generateLeadHistory($conn, $lead['ID'], $lead['User_ID'], $old_lead_details, $new_lead_details);
      echo json_encode(['status' => 200, 'message' => 'Lead updated successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
  }
} else {
  echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
}
