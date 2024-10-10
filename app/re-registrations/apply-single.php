<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

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
    if($isSubCenter->num_rows>0){
        $tablePrefix = "Sub_";
    }
    
    // Check is Center LoggedIn
    if($_SESSION['Role']=='Center'){
        $checkIsOwnerIsCenter = $conn->query("SELECT ID FROM Users WHERE Role = 'Center' AND ID = $addedFor");
        if($checkIsOwnerIsCenter->num_rows==0){
            $center = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = $addedFor");
            if($center->num_rows==0){
                exit(json_encode(['status' => false, 'message' => 'Owner not found!']));   
            }
            
            $center = $center->fetch_assoc();
            $addedFor = $center['Center'];
        }
    }

    $check = $conn->query("SELECT ID FROM Re_Registrations WHERE Student_ID = $id AND Exam_Session_ID = " . $_SESSION['active_rr_session_id'] . " AND Duration = $duration AND University_ID = " . $_SESSION['university_id']);
    if ($check->num_rows > 0) {
      exit(json_encode(['status' => false, 'message' => 'RR already applied!']));
    }
   
   $fee = $conn->query("SELECT Fee FROM ".$tablePrefix."Center_Sub_Courses WHERE User_ID = $addedFor AND Course_ID = $courseId AND Sub_Course_ID = $subCourseId AND University_ID = $universityId");
   if($fee->num_rows==0){
      exit(json_encode(['status' => false, 'message' => 'Fee not found!'])); 
   }
   
   $fee = $fee->fetch_assoc();
   echo $fee = $fee['Fee'];
    
    
  }
}
