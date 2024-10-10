<?php
if(isset($_GET['file'])){
  require '../../../includes/db-config.php';

  $file = mysqli_real_escape_string($conn, $_GET['file']);
  $file_to_search = $file.".pdf";
  
  function search_file($dir,$file_to_search){
    $files = scandir($dir);
    foreach($files as $key => $value){
      $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
      if(!is_dir($path)){
        if($file_to_search == $value){
          echo json_encode(['status'=>200, 'file'=>$file_to_search, 'message'=>'File found!']);
          break;
        }else{
          echo json_encode(['status'=>404, 'message'=>'File not found!']);
        }
      } else if($value != "." && $value != "..") {
        search_file($path, $file_to_search);
      }
    } 
  }

  search_file('../../../uploads/verification-sheets',$file_to_search);
}
