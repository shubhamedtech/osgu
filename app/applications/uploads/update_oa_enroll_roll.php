<?php
  if(isset($_FILES['file'])){
    require '../../../includes/db-config.php';
    require ('../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
    require('../../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');

    session_start();

    $export_data = array();

    // Header
    $header = array('Student ID', 'OA Number', 'Enrollment No', 'Roll No', 'Remark');
    if($_SESSION['has_lms']){
      $header = array('Student ID', 'OA Number', 'Enrollment No', 'Roll No', 'ID Card', 'Admit Card', 'Exam', 'Remark');
    }
    $export_data[] = $header;

    $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    if(in_array($_FILES["file"]["type"],$mimes)){
      // Upload File
      $uploadFilePath = basename($_FILES['file']['name']);
      move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

      // Read File
      $reader = new SpreadsheetReader($uploadFilePath);

      // Sheet Count
      $totalSheet = count($reader->sheets());

      /* For Loop for all sheets */
      for($i=0; $i<$totalSheet; $i++){
        $reader->ChangeSheet($i);
        foreach ($reader as $row)
        {
          // Data
          $remark = [];
          $id = mysqli_real_escape_string($conn, $row[0]);
          $oa = mysqli_real_escape_string($conn, $row[1]);
          $enrollment = mysqli_real_escape_string($conn, $row[2]);
          $roll = mysqli_real_escape_string($conn, $row[3]);

          if($id=='Student ID'){
            continue;
          }

          $student_id = $conn->query("SELECT ID FROM Students WHERE (ID = '$id' OR Unique_ID = '$id') AND University_ID = ".$_SESSION['university_id']."");
          if($student_id->num_rows==0){
            continue;
          }

          $student_id = $student_id->fetch_assoc();
          $id = $student_id['ID'];


          if(!empty($id)){
            if(!empty($oa)){
              $update = $conn->query("UPDATE Students SET OA_Number = '$oa' WHERE ID = $id AND University_ID = ".$_SESSION['university_id']."");
              if($update){
                $remark[] = "OA Number updated successfully!";
              }else{
                $remark[] = "Can't update OA Number!";
              }
            }

            if(!empty($enrollment)){

              $column = "";
              $columns = array();

              if($_SESSION['has_lms']){
                $id_card = intval($row[4]);
                $admit_card = intval($row[5]);
                $exam = intval($row[6]);

                $columns = ['ID Card', 'Admit Card', 'Exam'];

                $column .= ", Status = 1";
                $column .= ", `ID_Card` = ".$id_card;
                $column .= ", `Admit_Card` = ".$admit_card;
                $column .= ", `Exam` = ".$exam;
              }


              $update = $conn->query("UPDATE Students SET Enrollment_No = '$enrollment' $column WHERE ID = $id AND University_ID = ".$_SESSION['university_id']."");
              if($update){
                $remark[] = "Enrollment Number ".implode(" ", $columns)." updated successfully!";
              }else{
                $remark[] = "Can't update Enrollment No!";
              }
            }

            if(!empty($roll)){
              $update = $conn->query("UPDATE Students SET Roll_No = '$roll' WHERE ID = $id AND University_ID = ".$_SESSION['university_id']."");
              if($update){
                $remark[] = "Roll No updated successfully!";
              }else{
                $remark[] = "Can't update Roll No!";
              }
            }

            $export_data[] = array($id, $oa, $enrollment, $roll, empty($remark)? 'Values are missing!' : implode(", ", $remark));
          }
        }
      }
      unlink($uploadFilePath);
      $xlsx = SimpleXLSXGen::fromArray( $export_data )->downloadAs('OA Enrollment & Roll No Status.xlsx');
    }
  }