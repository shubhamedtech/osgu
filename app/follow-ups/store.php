<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';

  session_start();

  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $university_id = mysqli_real_escape_string($conn, $_POST['university_id']);
  $stage = mysqli_real_escape_string($conn, $_POST['stage']);
  $reason = mysqli_real_escape_string($conn, $_POST['reason']);
  $followUpDate = mysqli_real_escape_string($conn, $_POST['followup-date']);
  $followUpDate = date("Y-m-d", strtotime($followUpDate));
  $followUpTime = mysqli_real_escape_string($conn, $_POST['followup-time']);
  $followUpDate = $followUpDate . " " . $followUpTime . ":00";
  $remark = mysqli_real_escape_string($conn, $_POST['remark']);
  $status = 0;

  if (empty($stage) || empty($reason) || empty($remark) || empty($university_id)) {
    echo json_encode(['status' => 400, 'message' => 'All fields are required.']);
    exit();
  }

  if (isset($_POST['addFollowUpDate']) && empty($followUpDate)) {
    echo json_encode(['status' => 400, 'message' => 'Follow-Up date is required.']);
    exit();
  }

  if (!isset($_POST['addFollowUpDate'])) {
    $status = 1;
  }

  $lead = $conn->query("SELECT Leads.ID,Leads.Name,Lead_Status.User_ID,Lead_Status.University_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.ID = $id");
  if ($lead->num_rows > 0) {
    $lead = mysqli_fetch_assoc($lead);
  } else {
    echo json_encode(['status' => 302, 'message' => 'Lead not exists!']);
  }

  $university_id = $lead['University_ID'];

  //TODO: Lead History
  $old_lead_details = $conn->query("SELECT Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS Department,Courses.Name AS Category,Sub_Courses.Name AS Sub_Category,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID=$id");
  $old_lead_details = $old_lead_details->fetch_assoc();

  $update_lead_stage = $conn->query("UPDATE Lead_Status SET Stage_ID = $stage, Reason_ID = $reason WHERE ID = $id AND Stage_ID != (SELECT ID FROM Stages WHERE Is_Last = 1)");
  $update_followup_status = $conn->query("UPDATE Follow_Ups SET Status = 1 WHERE Lead_ID = " . $lead['ID'] . " AND `User_ID` = '" . $lead['User_ID'] . "' AND University_ID = $university_id");
  $new_lead_details = $conn->query("SELECT Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS Department,Courses.Name AS Category,Sub_Courses.Name AS Sub_Category,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID=$id");
  $new_lead_details = $new_lead_details->fetch_assoc();
  generateLeadHistory($conn, $lead['ID'], $lead['User_ID'], $old_lead_details, $new_lead_details);
  $addFollwoUp = $conn->query("INSERT INTO Follow_Ups (`Lead_ID`, `User_ID`, `University_ID`, `At`, `Remark`, `Status`) VALUES (" . $lead['ID'] . ", '" . $lead['User_ID'] . "', $university_id, '$followUpDate', '$remark', '$status')");
  if ($addFollwoUp) {
    if ($status == 1) {
      $message[] = 'Follow-Up close for ' . $lead['Name'] . ' on ' . date("l jS \of F Y h:i:s A", strtotime($followUpDate)) . '!';
    } else {
      $message[] = 'Follow-Up added for ' . $lead['Name'] . ' on ' . date("l jS \of F Y h:i:s A", strtotime($followUpDate)) . '!';
    }
    echo json_encode(['status' => 200, 'message' => implode(",", $message)]);
  } else {
    echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
  }
}
