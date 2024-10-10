<?php
if (isset($_FILES['file'])) {
  require '../../includes/db-config.php';
  require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  session_start();

  $export_data = array();

  // Header
  $documents = array('Photo', 'Student Signature', 'Parent Signature', 'Aadhar', 'Affidavit', 'Migration', 'Other Certificate', 'High School' => 'High School Marksheet', 'Intermediate' => 'Intermediate Marksheet', 'UG' => 'Graduation Marksheet', 'PG' => 'Post Graduation Marksheet', 'Other' => 'Other Marksheet');

  $fields = array('Student ID');

  foreach ($documents as $document) {
    $fields[] = $document . ' Remark';
  }

  array_push($fields, 'Remark');

  $export_data[] = $fields;

  $mimes = array('text/csv');

  // Check Student ID Creation
  $studentIdColumn = "ID";
  $university = $conn->query("SELECT ID FROM Universities WHERE Has_Unique_StudentID = 1 AND ID = " . $_SESSION['university_id']);
  if ($university->num_rows > 0) {
    $studentIdColumn = "Unique_ID";
  }

  if (in_array($_FILES["file"]["type"], $mimes)) {
    // Upload File
    $file_data = fopen($_FILES['file']['tmp_name'], 'r');
    fgetcsv($file_data);
    while ($row = fgetcsv($file_data)) {
      // Data
      $remark = [];
      $student_id = mysqli_real_escape_string($conn, $row[0]);

      $pendencyMarkedOn = array();
      $index = 1;
      foreach ($documents as $document) {
        if (!empty($row[$index])) {
          $pendencyMarkedOn[str_replace(" ", "_", $document)] = $row[$index];
        }
        $index++;
      }

      if (empty($pendencyMarkedOn)) {
        $export_data[] = array_merge($row, ['Empty row!']);
        continue;
      }

      $student_id = $conn->query("SELECT ID FROM Students WHERE $studentIdColumn = '$student_id' AND University_ID = " . $_SESSION['university_id']);
      if ($student_id->num_rows == 0) {
        $export_data[] = array_merge($row, ['Student not exists!']);
        continue;
      }

      $student_id = $student_id->fetch_assoc();
      $id = $student_id['ID'];

      $conn->query("UPDATE Student_Pendencies SET Status = 1 WHERE Student_ID = $id");

      $update = $conn->query("INSERT INTO Student_Pendencies (`Added_By`, `Student_ID`, `Pendency`) VALUES (" . $_SESSION['ID'] . ", " . $id . ", '" . json_encode($pendencyMarkedOn) . "')");
      if ($update) {
        $export_data[] = array_merge($row, ['Pendency Marked successfully!!']);
      } else {
        $export_data[] = array_merge($row, ['Something went wrong!']);
      }
    }
    $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Pendency Status.xlsx');
  }
}
