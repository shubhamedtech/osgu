<?php
  if(isset($_POST['name']) && isset($_POST['vertical']) && isset($_POST['university_type'])){
    require '../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = strtoupper(strtolower(mysqli_real_escape_string($conn, $_POST['short_name'])));
    $vertical = mysqli_real_escape_string($conn, $_POST['vertical']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $university_type = intval($_POST['university_type']);

    if(empty($name) || empty($short_name) || empty($vertical) || empty($address) || empty($_FILES["logo"]["name"])){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }
    
    if(isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"]!=''){
      $temp = explode(".", $_FILES["logo"]["name"]);
      $filename = round(microtime(true)) . '.' . end($temp);
      $tempname = $_FILES["logo"]["tmp_name"];
      $folder = "../../assets/img/university/".$filename; 
      if(move_uploaded_file($tempname, $folder)){ 
        $filename = "/assets/img/university/".$filename;
      }else{
        echo json_encode(['status'=>403, 'message'=>'Unable to save logo!']);
        exit();
      }
    }else{
      echo json_encode(['status'=>403, 'message'=>'Logo file is mandatory.']);
      exit();
    }

    $add = $conn->query("INSERT INTO `Universities`(`Name`, `Short_Name`, `Vertical`, `Address`, `Logo`, `Is_B2C`) VALUES ('$name', '$short_name', '$vertical', '$address', '$filename', '$university_type')");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$short_name.' added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
