<?php
  if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = mysqli_real_escape_string($conn, $_POST['short_name']);
    $contact_person_name = mysqli_real_escape_string($conn, $_POST['contact_person_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>400, "message"=>"Invalid email!"]);
      exit();
    }

    if(empty($name) || empty($email) || empty($contact) || empty($short_name) || empty($contact_person_name) || empty($address)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }
    
    $photo = '';
    if(isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!=''){
      $temp = explode(".", $_FILES["photo"]["name"]);
      $filename = round(microtime(true)) . '.' . end($temp);
      $tempname = $_FILES["photo"]["tmp_name"];
      $folder = "../../assets/img/centers/".$filename; 
      if(move_uploaded_file($tempname, $folder)){ 
        $filename = "/assets/img/centers/".$filename;
      }else{
        echo json_encode(['status'=>403, 'message'=>'Unable to save photo!']);
        exit();
      }
      $photo = " , Photo = '$filename'";
    }

    $add = $conn->query("UPDATE `Users` SET `Name` = '$name', `Short_Name` = '$short_name', `Contact_Name` = '$contact_person_name', `Email` = '$email', `Mobile` = '$contact', `Alternate_Mobile` = '$alternate_contact', `Address` = '$address', `Pincode` = '$pincode', `City` = '$city', `District` = '$district', `State` = '$state' $photo WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Center updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
