<?php
  if(isset($_GET['university_id'])){
    require '../../includes/db-config.php';

    $university_id = intval($_GET['university_id']);

    echo '<option value="">Choose</option>';
    $courses = $conn->query("SELECT ID, Name FROM Courses WHERE Status = 1 AND University_ID = $university_id ORDER BY Name ASC");
    while ($course = $courses->fetch_assoc()){
      echo '<option value="'.$course['ID'].'">'.$course['Name'].'</option>';
    }
  }
