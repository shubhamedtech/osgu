<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = intval($_POST['id']);
  echo '<option value="">Choose Program</option>';
  $programs = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Name FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Courses.Department_ID = $id AND Sub_Courses.University_ID = " . $_SESSION['university_id']);
  while ($program = $programs->fetch_assoc()) {
    echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
  }
}
