<?php
  if(isset($_FILES['file'])){
    require '../../../includes/db-config.php';
    require ('../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
    session_start();

    $allowed_file_extensions = array("pdf", "PDF");
    $file_folder = '../../../uploads/verification-sheets/';
    
    if($_SESSION['Role'] == 'Administrator' && isset($_POST['university_id'])){
      $university_id = intval($_POST['university_id']);
      if(empty($university_id)){
        echo json_encode(['status'=>403, 'message'=>'Please select university!']);
        exit();
      }
    }else{
      $university_id = $_SESSION['university_id'];
    }

    $header = array('File', 'Status');
    $finalData[] = $header;

    if (isset($_FILES["file"]["tmp_name"]) && $_FILES["file"]['tmp_name']!='' && count(array_filter($_FILES["file"]['tmp_name'])) > 0) {
      foreach ($_FILES["file"]["tmp_name"] as $key => $tmp_name) {
        $file = mysqli_real_escape_string($conn, $_FILES["file"]["name"][$key]);
        $tmp_name = $_FILES["file"]["tmp_name"][$key];
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array($extension, $allowed_file_extensions)) {

          $enrollmentNo = str_replace(".".$extension, "", $file);

          // Check
          $check = $conn->query("SELECT ID FROM Students WHERE Enrollment_No = '$enrollmentNo' AND University_ID = $university_id");
          if($check->num_rows==0){
            $finalData[] = array($file, 'Enrollment not exist!');
            continue;
          }

          // Remove Previous File
          if (file_exists($file_folder . $file)) {
            unlink($file_folder . $file);
          }

          if (move_uploaded_file($tmp_name, $file_folder . $file)) {
            $file = str_replace('../../..', '', $file_folder).$file;
            $update = $conn->query("INSERT INTO Downloads (Category, Name, File, University_ID) VALUES ('Verification Sheets', '$name', '$file', $university_id)");
            $finalData[] = array($file, 'File uploaded successfully!');
          } else {
            $finalData[] = array($file, 'Unable to upload!');
            continue;
          }
        } else {
          $finalData[] = array($file, 'File must be PDF!');
        }
      }
    }else{
      $finalData[] = array('Please upload file!');
    }

    $xlsx = SimpleXLSXGen::fromArray( $finalData )->downloadAs('Verification Sheet Status.xlsx');
  }
