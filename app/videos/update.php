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
  $id=$_POST['id'];

  $file_path="";
  $file_type=$extensionVid;
  $thumbnail_path="";
  $thumbnail_type=$extensionThumb;
  $updated_by = 1;
  $updated_at = date("Y-m-d:H:i:s");

  if(isset($_FILES["thumbnail"]["name"]) && $_FILES["thumbnail"]["name"]!=''){
      $temp1 = explode(".", $_FILES["thumbnail"]["name"]);
      $filename1  =  $temp1[0].'_'.time().'.' . end($temp1);
      $path1 = "../../uploads/videos/" . $filename1;
      $thumbnail_path = "uploads/videos/". $filename1;
      move_uploaded_file($_FILES["thumbnail"]["tmp_name"],
        $path1);

    $update = $conn->query("UPDATE `video_lectures` SET `thumbnail_url`='$thumbnail_path', `thumbnail_type`='$thumbnail_type' WHERE id = '".$id."' ");
  }

  if(isset($_FILES["video"]["name"]) && $_FILES["video"]["name"]!=''){

    if(($_FILES["video"]["type"] == "video/mp4") && ($_FILES["video"]["size"] < 1073741824) && in_array($extensionVid, $allowedExtsVid) && ($_FILES["video"]["error"] == 0)) {

      $temp = explode(".", $_FILES["video"]["name"]);
      $filename  =  $temp[0].'_'.time().'.' . end($temp);
      $path = "../../uploads/videos/" . $filename;
      $file_path = "uploads/videos/". $filename;
      move_uploaded_file($_FILES["video"]["tmp_name"],
        $path);
    $update = $conn->query("UPDATE `video_lectures` SET `video_url`='$file_path', `video_type`='$file_type' WHERE id = '".$id."' ");
    }else {
      echo json_encode(['status'=>400, 'message'=>'Invalid video file!!']);
      exit();
    }
  }
  


  $update = $conn->query("UPDATE `video_lectures` SET `unit`='$unit',`description`='$description',`semester`='$semester',`course_id`='$course_id', `subject_id`='$subject_id', `updated_by`='$updated_by', `updated_at`='$updated_at' WHERE id = '".$id."' ");


  if($update){
    echo json_encode(['status'=>200, 'message'=> "Video Updated succefully!!"]);
  }else{
    echo json_encode(['status'=>400, 'message'=>'Something went to wrong!!']);
  }
  
  