<?php
  require '../../includes/db-config.php';
  session_start();

  $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
  $role_query = str_replace('{{ column }}', 'Added_For', $role_query);

  $students = $conn->query("SELECT ID, First_Name, Last_Name, Middle_Name, Unique_ID, RIGHT(CONCAT('000000', Students.ID), 6) as Student_ID FROM Students WHERE University_ID = ".$_SESSION['university_id']." $role_query");
  $options = '<option value="">Select</option>';
  while($student = $students->fetch_assoc()){
    $id = base64_encode($student['ID'].'W1Ebt1IhGN3ZOLplom9I');
    $name = implode(" ", array_filter([$student['First_Name'], $student['Middle_Name'], $student['Last_Name']]));
    $student_id = !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['Student_ID'];
    $options .= '<option value="'.$id.'">'.$name.' ('.$student_id.')</option>';
  }

  echo $options;