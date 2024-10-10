<?php
  if(isset($_GET['university_id'])){
    require '../../includes/db-config.php';
    session_start();

    $university_id = intval($_GET['university_id']);

    echo '<option value="">Choose</option>';
    $courses = $conn->query("SELECT ID, Name FROM Courses WHERE University_ID = $university_id AND Status = 1 ORDER BY Name ASC");
    while ($course = $courses->fetch_assoc()){
      echo '<option value="'.$course['ID'].'">'.$course['Name'].'</option>';
    }
  }
