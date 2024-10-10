<?php 
  if(isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['counsellor']) && isset($_POST['fee']) && isset($_POST['course_type'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $university_id = intval($_POST['university_id']);
    $counsellor = intval($_POST['counsellor']);
    $sub_counsellor = intval($_POST['sub_counsellor']);

    $course_types = is_array($_POST['course_type']) ? $_POST['course_type'] : [];

    $fees = is_array($_POST['fee']) ? $_POST['fee'] : [];
    $fees = array_filter($fees);

    if(empty($fees) || empty($counsellor) || empty($university_id) || empty($id) || empty($course_types)){
      echo json_encode(['status'=>403, 'message'=>'Missing required fields!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Alloted_Center_To_Counsellor WHERE Code = $id AND University_ID = $university_id");
    if($check->num_rows>0){
      $update_allot_counsellor = $conn->query("UPDATE Alloted_Center_To_Counsellor SET Counsellor_ID = $counsellor WHERE Code = $id AND University_ID = $university_id");
    }else{
      $update_allot_counsellor = $conn->query("INSERT INTO Alloted_Center_To_Counsellor (`Counsellor_ID`, `Code`, `University_ID`) VALUES ($counsellor, $id, $university_id)");
    }


    $conn->query("DELETE FROM Center_Course_Types WHERE `User_ID` = $id AND University_ID = $university_id");
    foreach($course_types as $course_type){
      $conn->query("INSERT INTO Center_Course_Types (`User_ID`, `Course_Type_ID`, `University_ID`) VALUES ($id, $course_type, $university_id)");
    }

    $conn->query("DELETE FROM Center_Sub_Courses WHERE `User_ID` = $id AND University_ID = $university_id");
    foreach ($fees as $sub_course_id=>$fee){
      $course_id = $conn->query("SELECT Course_ID FROM Sub_Courses WHERE ID = $sub_course_id AND University_ID = $university_id");
      $course_id = $course_id->fetch_assoc();
      $course_id = $course_id['Course_ID'];

      $allot = $conn->query("INSERT INTO Center_Sub_Courses (`Fee`, `User_ID`, `Course_ID`, `Sub_Course_ID`, `University_ID`) VALUES ($fee, $id, $course_id, $sub_course_id, $university_id)");
    }

    if($allot){
      echo json_encode(['status'=>200, 'message'=>'University alloted successfully!']);
    }else{
      echo json_encode(['status'=>403, 'message'=>'Unable to allot university!']);
    }
  }  
?>
