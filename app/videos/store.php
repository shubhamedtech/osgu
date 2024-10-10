<?php
  ini_set('display_errors', 1); 
  require '../../includes/db-config.php';

  $allowedExtsVid = array("mp4");
  $extensionVid = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
  $allowedExtsThumb = array("pdf", "jpeg", "gif", "png");
  $extensionThumb = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);

  $course_id = intval($_POST['course_id']);
  $subject_id = intval($_POST['subject_id']);
  $unit= $_POST['unit'];
  $description= $_POST['description'];
  $semester= $_POST['semester'];

  $file_path="";
  $file_type=$extensionVid;
  $thumbnail_path="";
  $thumbnail_type=$extensionThumb;
  $created_by = 1;
  $created_at = date("Y-m-d:H:i:s");

  if(isset($_FILES["thumbnail"]["name"]) && $_FILES["thumbnail"]["name"]!=''){
      $temp1 = explode(".", $_FILES["thumbnail"]["name"]);
      $filename1  =  $temp1[0].'_'.time().'.' . end($temp1);
      $path1 = "../../uploads/videos/" . $filename1;
      $thumbnail_path = "uploads/videos/". $filename1;
      move_uploaded_file($_FILES["thumbnail"]["tmp_name"],
        $path1);
  }else{
    echo json_encode(['status'=>403, 'message'=>'Thumbnail is mandatory.']);
    exit();
  }

  if(isset($_FILES["video"]["name"]) && $_FILES["video"]["name"]!=''){

    if(($_FILES["video"]["type"] == "video/mp4") && ($_FILES["video"]["size"] < 1073741824) && in_array($extensionVid, $allowedExtsVid) && ($_FILES["video"]["error"] == 0)) {

      $temp = explode(".", $_FILES["video"]["name"]);
      $filename  =  $temp[0].'_'.time().'.' . end($temp);
      $path = "../../uploads/videos/" . $filename;
      $file_path = "uploads/videos/". $filename;
      move_uploaded_file($_FILES["video"]["tmp_name"],
        $path);
    }else {
      echo json_encode(['status'=>400, 'message'=>'Invalid video file!!']);
      exit();
    }
  }else{
    echo json_encode(['status'=>403, 'message'=>'Video is mandatory.']);
    exit();
  }
  

  $add = $conn->query("INSERT INTO `video_lectures`(`unit`,`description`,`semester`,`course_id`, `subject_id`, `thumbnail_url`, `thumbnail_type`,`video_url`, `video_type`, `created_by`, `created_at`) VALUES ('".$unit."','".$description."','".$semester."','".$course_id."', '".$subject_id."', '".$thumbnail_path."', '".$thumbnail_type."', '".$file_path."', '".$file_type."', '".$created_by."', '".$created_at."' ) ");

  if($add){
    echo json_encode(['status'=>200, 'message'=> "Video uploaded succefully!!"]);
  }else{
    echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
  }
  
  
