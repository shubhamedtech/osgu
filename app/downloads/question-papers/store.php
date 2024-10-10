<?php
  if(isset($_POST['name']) && isset($_FILES['file'])){
    require '../../../includes/db-config.php';
    session_start();

    $allowed_file_extensions=array("zip", "rar", "7zip", "pdf", "PDF");
    $file_folder = '../../../uploads/question-papers/';

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    if($_SESSION['Role'] == 'Administrator' && isset($_POST['university_id'])){
      $university_id = intval($_POST['university_id']);
      if(empty($university_id)){
        echo json_encode(['status'=>403, 'message'=>'Please select university!']);
        exit();
      }
    }else{
      $university_id = $_SESSION['university_id'];
    }

    if(isset($_FILES["file"]['tmp_name']) && $_FILES["file"]['tmp_name']!=''){
      $file = mysqli_real_escape_string($conn, $_FILES["file"]['name']);
      $tmp_name = $_FILES["file"]["tmp_name"];
      $file_extension=pathinfo($file, PATHINFO_EXTENSION);
      $file = uniqid().".".$file_extension;
      if(in_array($file_extension, $allowed_file_extensions)){
        if(!move_uploaded_file($tmp_name, $file_folder.$file)){
          echo json_encode(['status'=>503, 'message'=>'Unable to upload file!']);
          exit();
        }else{
          $file = str_replace('../../..', '', $file_folder).$file;
          $update = $conn->query("INSERT INTO Downloads (Category, Name, File, University_ID) VALUES ('Question Papers', '$name', '$file', $university_id)");
          echo json_encode(['status'=>200, 'message'=>'Question Papers uploaded successfully!']);
        }
      }else{
        echo json_encode(['status'=>302, 'message'=>'The file type is not supported!']);
        exit();
      }
    }else{
      echo json_encode(['status'=>400, 'message'=>'File is required!']);
      exit();
    }
  }
