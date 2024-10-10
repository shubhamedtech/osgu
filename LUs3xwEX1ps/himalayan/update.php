<?php
  if(isset($_POST['lot']) && isset($_POST['student_id']) && isset($_POST['student_name']) && isset($_POST['id']) && isset($_POST['center'])){
    require '../../includes/db-config.php';
    session_start();

    if($_SESSION['Role']!='Administrator'){
      exit();
    }

    $allowed_file_extensions = array('jpg', 'png', 'jpeg', 'PNG', 'JPEG', 'JPG');

    $photo_folder = "images/";

    $id = intval($_POST['id']);
    $center_code = intval($_POST['center']);
    $lot = mysqli_real_escape_string($conn, $_POST['lot']);
    $session = mysqli_real_escape_string($conn, $_POST['session']);
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    if(isset($_FILES['photo']) && !empty($_FILES['photo']['name'])){
      $photo = mysqli_real_escape_string($conn, $_FILES["photo"]['name']);
      $tmp_name = $_FILES["photo"]["tmp_name"];
      $photo_extension=pathinfo($photo, PATHINFO_EXTENSION);
      $photo = $id.".".$photo_extension;
      if(in_array($photo_extension, $allowed_file_extensions)){
        if(!move_uploaded_file($tmp_name, $photo_folder.$photo)){
          echo json_encode(['status'=>503, 'message'=>'Unable to upload photo!']);
          exit();
        }else{
          $update = $conn->query("UPDATE Himalayan_ID_Cards SET Photo = '$photo' WHERE ID = $id");
        }
      }
    }

    $update = $conn->query("UPDATE Himalayan_ID_Cards SET `Lot` = '$lot', `Center_Code` = $center_code, `Session` = '$session', `Name` = '$student_name', `Father_Name` = '$father_name', `Student_ID` = '$student_id', `Course` = '$course', `Contact` = '$contact', `Address` = '$address' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Updated successfully!']);
    }else{
      echo json_encode(['status'=>503, 'message'=>'Something went wrong!']);
    }
  }
