<?php
ini_set('display_errors', 1); 
require '../../includes/db-config.php';

$sub_course_id = intval($_POST['sub_course_id']);

$studentSubjects = "SELECT Syllabi.`ID` as subject_id,Syllabi.Name from Syllabi WHERE Syllabi.Sub_Course_ID = $sub_course_id ";
$subjects = mysqli_query($conn, $studentSubjects);

$html='<option value="">Select</option>';
while ($row = mysqli_fetch_assoc($subjects)) {
  $html = $html.'<option value="'.$row['subject_id'].'">'.$row['Name'].'</option>';
}

echo $html; die;

?>