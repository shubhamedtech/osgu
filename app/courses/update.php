<?php
ini_set('display_errors',1);
  if(isset($_POST['name']) && isset($_POST['course_type']) && isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['department'])){
    require '../../includes/db-config.php';
    include '../../includes/helpers.php';

    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = mysqli_real_escape_string($conn, $_POST['short_name']);
    $course_type = intval($_POST['course_type']);
    $university_id = intval($_POST['university_id']);
    $department_id = intval($_POST['department']);

    if(empty($name) || empty($short_name) || empty($course_type) || empty($university_id) || empty($id) || empty($department_id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Courses WHERE (Name like '$name' OR Short_Name LIKE '$short_name') AND University_ID = $university_id AND ID <> $id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>$short_name.' already exists!']);
      exit();
    }
    
    $add = $conn->query("UPDATE `Courses` SET `Name` = '$name', `Short_Name` = '$short_name', `Course_Type_ID` = $course_type, `Department_ID` = $department_id WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$short_name.' updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
