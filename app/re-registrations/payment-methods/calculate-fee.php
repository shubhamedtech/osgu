<?php
foreach ($ids as $id) {
  $student = $conn->query("SELECT Duration, Added_For, Sub_Course_ID, Course_ID, University_ID FROM Students WHERE ID = $id AND University_ID = " . $_SESSION['university_id']);
  if ($student->num_rows > 0) {
    $student = $student->fetch_assoc();
    $addedFor = $student['Added_For'];
    $courseId = $student['Course_ID'];
    $subCourseId = $student['Sub_Course_ID'];
    $universityId = $student['University_ID'];
    $duration = $student['Duration'] + 1;


    $tablePrefix = "";
    $isSubCenter = $conn->query("SELECT ID FROM Users WHERE Role = 'Sub-Center' AND ID = $addedFor");
    if ($isSubCenter->num_rows > 0) {
      $tablePrefix = "Sub_";
    }

    // Check is Center LoggedIn
    if ($_SESSION['Role'] == 'Center') {
      $tablePrefix = "";
      $checkIsOwnerIsCenter = $conn->query("SELECT ID FROM Users WHERE Role = 'Center' AND ID = $addedFor");
      if ($checkIsOwnerIsCenter->num_rows == 0) {
        $center = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = $addedFor");
        if ($center->num_rows == 0) {
          continue;
        }

        $center = $center->fetch_assoc();
        $addedFor = $center['Center'];
      }
    }

    $check = $conn->query("SELECT ID FROM Re_Registrations WHERE Student_ID = $id AND Exam_Session_ID = " . $_SESSION['active_rr_session_id'] . " AND Duration = $duration AND University_ID = " . $_SESSION['university_id']);
    if ($check->num_rows > 0) {
      continue;
    }

    $fee = $conn->query("SELECT Fee FROM " . $tablePrefix . "Center_Sub_Courses WHERE User_ID = $addedFor AND Course_ID = $courseId AND Sub_Course_ID = $subCourseId AND University_ID = $universityId");
    if ($fee->num_rows == 0) {
      continue;
    }

    $fee = $fee->fetch_assoc();
    $totalFee[$id] = $fee['Fee'];
  }
}
