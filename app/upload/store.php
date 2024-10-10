<?php
  ini_set('display_errors', 1);
  if(isset($_FILES) && isset($_POST['column']) && isset($_POST['table']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $column = mysqli_real_escape_string($conn, $_POST['column']);
    $table = mysqli_real_escape_string($conn, $_POST['table']);
    $id = intval($_POST['id']);

    $extensions = array("jpeg","jpg","png","gif","JPG","PNG","JPEG","PDF","pdf","doc","docx","csv","application/vnd.ms-excel","text/xls","text/xlsx","application/vnd.oasis.opendocument.spreadsheet", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    
    if($_SESSION['Role']!='Student'){
      $directory = "../../uploads/".strtolower($column);

      if (!file_exists($directory)){
        mkdir($directory, 0777);
      }

      $files = [];
      foreach($_FILES["file"]["tmp_name"] as $key=>$tmp_name) {
        $file_name = uniqid().'_'.$_FILES["file"]["name"][$key];
        $tmp_name = $_FILES["file"]["tmp_name"][$key];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        if(in_array($file_extension,$extensions)) {
          $file_name = file_exists($directory.$file_name) ? time().'_'.$file_name : $file_name;
          if(move_uploaded_file($tmp_name, $directory."/".$file_name)){
            $files[] = str_replace("../..", "", $directory)."/".$file_name;
          }else{
            echo json_encode(['status'=>400, 'message'=>'Unable to upload file(s)!']);
            exit();  
          }
        }else{
          echo json_encode(['status'=>400, 'message'=>'Invalid file format!']);
          exit();
        }       
      }
    }else{
      
    }

    $update = $conn->query("UPDATE $table SET $column = '".implode("|", $files)."' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200,'message'=>'File uploaded successfully!']);
    }else{
      echo json_encode(['status'=>400,'message'=>'Something went wrong!']);
    }
  }