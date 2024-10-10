<?php
  if(isset($_POST['name']) && isset($_POST['vertical']) && isset($_POST['id']) && isset($_POST['university_type'])){
    require '../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = strtoupper(strtolower(mysqli_real_escape_string($conn, $_POST['short_name'])));
    $vertical = mysqli_real_escape_string($conn, $_POST['vertical']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $logo = '';
    $university_type = intval($_POST['university_type']);

    if(empty($name) || empty($short_name) || empty($vertical) || empty($address)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }
    
    if(isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"]!=''){
      $temp = explode(".", $_FILES["logo"]["name"]);
      $filename = round(microtime(true)) . '.' . end($temp);
      $tempname = $_FILES["logo"]["tmp_name"];
      $folder = "../../assets/img/university/".$filename; 
      if(move_uploaded_file($tempname, $folder)){ 
        $logo = $conn->query("SELECT Logo FROM Universities WHERE ID = '".$_POST['id']."'");
        $logo = mysqli_fetch_assoc($logo);
        if(file_exists("../..".$logo['Logo'])){
          unlink("../..".$logo['Logo']);
        }
        $filename = "/assets/img/university/".$filename;
        $logo = ", `Logo` = '$filename'";
      }else{
        echo json_encode(['status'=>403, 'message'=>'Unable to save logo!']);
        exit();
      }
    }

    $update = $conn->query("UPDATE `Universities` SET `Name` = '$name', `Short_Name` = '$short_name', `Vertical` = '$vertical', `Address` = '$address', `Is_B2C` = '$university_type' $logo WHERE ID = '".$_POST['id']."'");
    if($update){
      echo json_encode(['status'=>200, 'message'=>$short_name.' updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
