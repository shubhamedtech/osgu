<?php
  if(isset($_GET['sub_course_id'])){
    require '../../includes/db-config.php';
    session_start();

    $sub_course_id = intval($_GET['sub_course_id']);

    if(empty($sub_course_id)){
      echo 'Mode';
      exit();
    }

    $mode = $conn->query("SELECT Modes.Name FROM Sub_Courses LEFT JOIN Modes ON Sub_Courses.Mode_ID = Modes.ID WHERE Sub_Courses.ID = $sub_course_id");
    $mode = mysqli_fetch_assoc($mode);
    echo $mode['Name'];
    
  }
