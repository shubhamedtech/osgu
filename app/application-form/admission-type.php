<?php
if (isset($_GET['session_id']) && isset($_GET['university_id'])) {
  require '../../includes/db-config.php';

  $university_id = intval($_GET['university_id']);
  $session_id = intval($_GET['session_id']);

  if (empty($university_id) || empty($session_id)) {
    echo '<option value="">Please select session</option>';
    exit();
  }

  $status = $conn->query("SELECT CT_Status, LE_Status FROM Admission_Sessions WHERE ID = $session_id");
  $status = mysqli_fetch_assoc($status);

  $option = "";
  $admission_types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = $university_id");
  if ($admission_types->num_rows == 0) {
    echo '<option value="">Please add admission type</option>';
    exit();
  }
  while ($admission_type = $admission_types->fetch_assoc()) {
    if (strcasecmp($admission_type['Name'], 'lateral') == 0 && $status['LE_Status'] == 1) {
      $option .= '<option value="' . $admission_type['ID'] . '">' . $admission_type['Name'] . '</option>';
    }
    if (strcasecmp($admission_type['Name'], 'credit transfer') == 0 && $status['CT_Status'] == 1) {
      $option .= '<option value="' . $admission_type['ID'] . '">' . $admission_type['Name'] . '</option>';
    }
    if (strcasecmp($admission_type['Name'], 'regular') == 0 || strcasecmp($admission_type['Name'], 'fresh') == 0) {
      $option .= '<option value="' . $admission_type['ID'] . '">' . $admission_type['Name'] . '</option>';
    }
  }


  echo $option;
}
