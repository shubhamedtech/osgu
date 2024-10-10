<?php
include '../../includes/db-config.php';
require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
session_start();

$studentIds = array();
$examSession = $conn->query("SELECT Admission_Session FROM Exam_Sessions WHERE ID = " . $_SESSION['active_rr_session_id']);
$examSession = $examSession->fetch_assoc();
$examSessions = json_decode($examSession['Admission_Session'], true);
foreach ($examSessions as $value) {
  if ($value['for'] == 'Re-Registrations') {
    $condition = "";
    if (!empty($value['admissionType'])) {
      $condition .= " AND Students.Admission_Type_ID = " . $value['admissionType'];
    }

    if ($value['table'] == 'Fresh') {
      $condition .= " AND Students.Admission_Session_ID = " . $value['session'] . " AND Students.Adm_Duration IN (" . implode(",", $value['duration']) . ")";
      $students = $conn->query("SELECT Students.ID FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND (Students.Adm_Duration+1) <= Sub_Courses.Min_Duration $condition");
    } elseif ($value['table'] == 'Re-Reg') {
      $condition .= " AND Students.Admission_Session_ID = " . $value['session'] . " AND Re_Registrations.Duration IN (" . implode(",", $value['duration']) . ")";
      $students = $conn->query("SELECT Student_ID as ID FROM Re_Registrations LEFT JOIN Students ON Re_Registrations.Student_ID = Students.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Re_Registrations.University_ID = " . $_SESSION['university_id'] . " AND (Re_Registrations.Duration+1) <= Sub_Courses.Min_Duration $condition");
    }

    while ($student = $students->fetch_assoc()) {
      $studentIds[] = $student['ID'];
    }
  }
}

$header = array('Status', 'Student ID', 'Student Name', 'Enrollment No', 'Adm Session', 'Course', 'Owner Code', 'Owner Name', 'RM');
$finalData[] = $header;
$role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);

$students = $conn->query("SELECT Students.ID, IF(Re_Registrations.Student_ID IS NULL, Students.Adm_Duration + 1, Re_Registrations.Duration) AS Duration, CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name) as First_Name, Students.Enrollment_No, Students.Unique_ID, Admission_Sessions.Name as Admission_Session_ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name,')') as Course_ID, Students.Added_For, IF(Re_Registrations.Student_ID IS NULL, 'Pending', 'Applied') as Status FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Re_Registrations ON Students.ID = Re_Registrations.Student_ID AND Exam_Session_ID = " . $_SESSION['active_rr_session_id'] . " AND Re_Registrations.University_ID = " . $_SESSION['university_id'] . " WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Students.ID IN (" . implode(",", $studentIds) . ") $role_query");
while ($row = $students->fetch_assoc()) {
  // Added_For
  if ($_SESSION['Role'] == 'Center') {
    $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . "");
  } else {
    $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . " AND Role = 'Center'");
    if ($user->num_rows == 0) {
      $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row['Added_For']);
    }
  }

  if ($user->num_rows > 0) {
    $user = mysqli_fetch_array($user);
  } else {
    $user['Name'] = "";
    $user['Code'] = "";
    $user['ID'] = 0;
  }

  // RM
  $rm['Name'] = "";
  if (!empty($user['Name'])) {
    // RM
    $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'] . " WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id']);
    if ($rm->num_rows > 0) {
      $rm = mysqli_fetch_array($rm);
    } else {
      $rm = $user;
    }
  }

  $finalData[] = array($row['Status'], $row['Unique_ID'], $row['First_Name'], $row['Enrollment_No'], $row['Admission_Session_ID'], $row['Course_ID'], $user['Code'], $user['Name'], $rm['Name']);
}

$xlsx = SimpleXLSXGen::fromArray($finalData)->downloadAs('RR - ' . $_SESSION['active_rr_session_name'] . '.xlsx');
