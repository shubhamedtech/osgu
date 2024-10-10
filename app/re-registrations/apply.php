<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_POST['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $student = $conn->query("SELECT Duration FROM Students WHERE ID = $id AND University_ID = " . $_SESSION['university_id']);
  if ($student->num_rows > 0) {
    $student = $student->fetch_assoc();
    $duration = $student['Duration'] + 1;

    $check = $conn->query("SELECT ID FROM Re_Registrations WHERE Student_ID = $id AND Exam_Session_ID = " . $_SESSION['active_rr_session_id'] . " AND Duration = $duration AND University_ID = " . $_SESSION['university_id']);
    if ($check->num_rows > 0) {
      exit(json_encode(['status' => false, 'message' => 'RR already applied!']));
    }

    $add = $conn->query("INSERT INTO Re_Registrations (Student_ID, University_ID, Exam_Session_ID, Duration, Amount, Added_By) VALUES ($id, " . $_SESSION['university_id'] . ", " . $_SESSION['active_rr_session_id'] . ", $duration, 0, " . $_SESSION['ID'] . ")");
    if ($add) {
      $update = $conn->query("UPDATE Students SET Duration = $duration WHERE ID = $id");
      echo json_encode(['status' => true, 'message' => 'RR Applied!']);
    } else {
      echo json_encode(['status' => false, 'message' => mysqli_error($conn)]);
    }
  } else {
    echo json_encode(['status' => false, 'message' => 'Student not found!']);
  }
}
