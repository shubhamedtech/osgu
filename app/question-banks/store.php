<?php
if (isset($_POST['date_sheet_id']) && isset($_FILES['file'])) {
  session_start();
  require '../../includes/db-config.php';
  require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $export_data = array();

  $fields = array('Question', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Answer', 'Marks', 'Remark');
  $export_data[] = $fields;

  $date_sheet_id = intval($_POST['date_sheet_id']);

  // Date Sheet
  $date_sheet = $conn->query("SELECT Syllabus_ID FROM Date_Sheets WHERE ID = $date_sheet_id");
  if ($date_sheet->num_rows == 0) {
    exit(json_encode(['status' => false, 'message' => 'No Date Sheet found!']));
  }

  $date_sheet = $date_sheet->fetch_assoc();
  $syllabus_id = $date_sheet['Syllabus_ID'];

  $mimes = array('text/csv');

  if (in_array($_FILES["file"]["type"], $mimes)) {
    // Upload File
    $file_data = fopen($_FILES['file']['tmp_name'], 'r');
    fgetcsv($file_data);
    $counter = 1;
    while ($row = fgetcsv($file_data)) {
      // Data
      $remark = [];
      $question = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[0])), "UTF-8");
      $option1 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[1])), "UTF-8");
      $option2 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[2])), "UTF-8");
      $option3 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[3])), "UTF-8");
      $option4 = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[4])), "UTF-8");
      $answer = mb_convert_encoding(trim(mysqli_real_escape_string($conn, $row[5])), "UTF-8");
      $marks = intval($row[6]);

      $row = array($question, $option1, $option2, $option3, $option4, $answer, $marks);

      if (empty($question)) {
        $export_data[] = array_merge($row, ['Question cannot be empty!']);
        continue;
      }

      // if (empty($option1) || empty($option2) || empty($option3) || empty($option4)) {
      //   $export_data[] = array_merge($row, ['Option cannot be empty!']);
      //   continue;
      // }

      if (empty($answer)) {
        $export_data[] = array_merge($row, ['Answer cannot be empty!']);
        continue;
      }

      if (empty($marks)) {
        $export_data[] = array_merge($row, ['Marks cannot be empty!']);
        continue;
      }

      $options = array($option1, $option2, $option3, $option4);
      $options = array_filter($options);
      $options = mysqli_real_escape_string($conn, json_encode($options));

      $add = $conn->query("INSERT INTO `MCQs` (`Date_Sheet_ID`, `Syllabus_ID`, `Question_No`, `Question`, `Options`, `Answer`, `Marks`) VALUES ($date_sheet_id, $syllabus_id, '$counter', '$question', '$options', '$answer', '$marks')");
      if ($add) {
        $export_data[] = array_merge($row, ['Question updated successfully!']);
      } else {
        $export_data[] = array_merge($row, ['Something went wrong!']);
      }
      $counter++;
    }
  }
  $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Question Status.xlsx');
}
