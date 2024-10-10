<?php
  ini_set('display_errors', 1); 

  if(isset($_FILES['file'])){
    require '../../includes/db-config.php';
    require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
    require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');

    session_start();

    $export_data = array();

    // Header
    $header = array('Enrollment Nomber', 'Student Name', 'Father Name',  'Mother Name',  'Course', 'Email', 'Phone', 'Aadhar', 'DOB', 'Gender', 'Category', 'Address', 'City', 'District', 'State', 'Pincode', 'Duraction(Sem/Year)', "Remark");
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
          $remark = [];
          $enrollment = mysqli_real_escape_string($conn, $row[0]);
          $student_name = mysqli_real_escape_string($conn, $row[1]);
          $father_name = mysqli_real_escape_string($conn, $row[2]);
          $mother_name = mysqli_real_escape_string($conn, $row[3]);
          $course = mysqli_real_escape_string($conn, $row[4]);
          $email = mysqli_real_escape_string($conn, $row[5]);
          $phone_number = mysqli_real_escape_string($conn, $row[6]);
          $aadhar = mysqli_real_escape_string($conn, $row[7]);
          $dob = mysqli_real_escape_string($conn, $row[8]);
          $dob = date('Y-m-d', strtotime($dob));
          $gender = mysqli_real_escape_string($conn, $row[9]);
          $category = mysqli_real_escape_string($conn, $row[10]);
          $address = mysqli_real_escape_string($conn, $row[11]);
          $city = mysqli_real_escape_string($conn, $row[12]);
          $distric = mysqli_real_escape_string($conn, $row[13]);
          $state = mysqli_real_escape_string($conn, $row[14]);
          $pincode = mysqli_real_escape_string($conn, $row[15]);
          $nationality = mysqli_real_escape_string($conn, $row[16]);
          $duration = 1;          
          if($enrollment =='Enrollment_NO'){
            continue;
          }
          if($phone_number == 'Contact'){
            continue;
          }

          $nationality = "INDIAN";
          $admission_session_id = 1;
          $admission_type_id = 1;
          $sub_course_id = 1;
          $employment_status = $distric;
          $marital_status = 1;
          $religion = 1;
          $add = $conn->query("INSERT INTO Exam_Students (University_ID, Admission_Session, Admission_Type, Course, Sub_Course, Duration, Phone_Number, Name, Email, Enrolment_Number, Gender, Category, Emploment_Status, Marital_Status, Father_Name, Mother_Name, Session_ch, Address, State, Pin, Sem, Religion, Nationality, Aadhar, DOB, Status, Created_at, Updated_at) VALUES (" . $_SESSION['university_id'] . ", $admission_session_id , $admission_type_id, '$course', $sub_course_id, $duration, $phone_number, '$student_name', '$email', '$enrollment', '$gender', '$category', '$employment_status', '$marital_status', '$father_name', '$mother_name', '$city', '$address', '$state', $pincode, $duration, '$religion','$nationality', '$aadhar', '$dob', 1, now(), now())");

          if($add){
            $export_data[] = array_merge($row, ['Student added successfully!']);
          }else{
            $export_data[] = array_merge($row, ['Something went wrong!']);
          }
        }
      }
      unlink($uploadFilePath);
      $xlsx = SimpleXLSXGen::fromArray( $export_data )->downloadAs('\Added Students Status '.date('h m s').'.xlsx');
    }
  }
?>
