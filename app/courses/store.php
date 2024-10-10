<?php
  if(isset($_POST['name']) && isset($_POST['course_type']) && isset($_POST['university_id']) && isset($_POST['department'])){
    require '../../includes/db-config.php';
    include '../../includes/helpers.php';

    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = mysqli_real_escape_string($conn, $_POST['short_name']);
    $course_type = intval($_POST['course_type']);
    $university_id = intval($_POST['university_id']);
    $department_id = intval($_POST['department']);

    if(empty($name) || empty($short_name) || empty($course_type) || empty($university_id) || empty($department_id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Courses WHERE (Name like '$name' OR Short_Name LIKE '$short_name') AND University_ID = $university_id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>$short_name.' already exists!']);
      exit();
    }
    
    $add = $conn->query("INSERT INTO `Courses`(`Name`, `Short_Name`, `Course_Type_ID`, `University_ID`, `Department_ID`) VALUES ('$name', '$short_name', $course_type, $university_id, $department_id)");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$short_name.' added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
