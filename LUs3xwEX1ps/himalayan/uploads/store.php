<?php
  if(isset($_FILES['file'])){
    require '../../../includes/db-config.php';
    require ('../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
    require('../../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');

    session_start();

    $export_data = array();

    // Header
    $header = array('Lot', 'Center Code', 'Session', 'Name', 'Father Name', 'Student ID', 'Course', 'Contact', 'Address', 'Photo', 'Remark');
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

          $lot_no = mysqli_real_escape_string($conn, $row[0]);
          if($lot_no=='Lot' || empty($lot_no)){
            continue;
          }
          $center_code = mysqli_real_escape_string($conn, $row[1]);
          $session = mysqli_real_escape_string($conn, $row[2]);
          $name = mysqli_real_escape_string($conn, $row[3]);
          $father_name = mysqli_real_escape_string($conn, $row[4]);
          $student_id = mysqli_real_escape_string($conn, $row[5]);
          $course = mysqli_real_escape_string($conn, $row[6]);
          $contact = mysqli_real_escape_string($conn, $row[7]);
          $address = mysqli_real_escape_string($conn, $row[8]);
          
          $center_id = $conn->query("SELECT ID FROM Users WHERE Code LIKE '$center_code'");
          if($center_id->num_rows==0){
            $center_id = 0;
          }else{
            $center_id = $center_id->fetch_assoc();
            $center_id = $center_id['ID'];
          }

          $photo = "";
          $check = $conn->query("SELECT ID FROM Himalayan_ID_Cards WHERE Student_ID = '$student_id'");
          if($check->num_rows==0){
            $add = $conn->query("INSERT INTO Himalayan_ID_Cards (`Lot`, `Center_Code`, `Session`, `Name`, `Father_Name`, `Student_ID`, `Course`, `Contact`, `Address`) VALUES ('$lot_no', '$center_id', '$session', '$name', '$father_name', '$student_id', '$course', '$contact', '$address')");
            if($add){
              $remark[] = "Success!";
              $id = $conn->insert_id;
              $photo = $id.".jpg";
              $conn->query("UPDATE Himalayan_ID_Cards SET Photo = '$photo' WHERE ID = $id");
            }else{
              $remark[] = "Error!";
            }
          }else{
            $remark[] = "Student ID Already Exists!";
          }

          $export_data[] = array($lot_no, $center_code, $session, $name, $father_name, $student_id, $course, $contact, $address, $photo, implode(", ", $remark));
        }
      }
      unlink($uploadFilePath);
      $xlsx = SimpleXLSXGen::fromArray( $export_data )->downloadAs('ID Card Status.xlsx');
    }
  }
?>
