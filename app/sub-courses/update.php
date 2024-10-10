<?php
  if(isset($_POST['name']) && isset($_POST['course']) && isset($_POST['university_id']) && isset($_POST['scheme']) || isset($_POST['mode']) && isset($_POST['min_duration']) && isset($_POST['max_duration']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $university_id = intval($_POST['university_id']);
    $course = intval($_POST['course']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = mysqli_real_escape_string($conn, $_POST['short_name']);
    $scheme = intval($_POST['scheme']);
    $mode = intval($_POST['mode']);
    $min_duration = intval($_POST['min_duration']);
    $max_duration = intval($_POST['max_duration']);
    // $fee = $_POST['fee'];
    $applicable = array_key_exists('applicable_in', $_POST) ? $_POST['applicable_in'] : [];
    $lateral = intval($_POST['lateral']);
    $le_start = mysqli_real_escape_string($conn, $_POST['le_start']);
    $le_sol = intval($_POST['le_sol']);
    $ct_transfer = intval($_POST['ct_transfer']);
    $ct_start = intval($_POST['ct_start']);
    $ct_sol = intval($_POST['ct_sol']);
    $eligibilities = is_array($_POST['eligibilities']) ? array_filter($_POST['eligibilities']) : [];

    // $fee_structure_ids = array_keys($fee);
    // $fee_structure_id = implode(',', $fee_structure_ids);

    if(empty($name) || empty($short_name) || empty($course) || empty($university_id) || empty($scheme) || empty($mode) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    if(!empty($lateral) && (empty($le_start) || empty($le_sol))){
      echo json_encode(['status'=>403, 'message'=>'Please fill all LE fields!']);
      exit();
    }

    if(!empty($ct_transfer) && (empty($ct_start) || empty($ct_sol))){
      echo json_encode(['status'=>403, 'message'=>'Please fill all CT fields!']);
      exit();
    }

    // $fee_structures = $conn->query("SELECT ID, Name, Fee_Applicable_ID FROM Fee_Structures WHERE Status = 1 AND Is_Constant = 1  AND University_ID = $university_id AND ID IN ($fee_structure_id) ORDER BY Name ASC");
    // while ($fee_structure = $fee_structures->fetch_assoc()){
    //   $fee_head_applicability[$fee_structure['ID']] = $fee_structure['Fee_Applicable_ID'];
    //   if($fee[$fee_structure['ID']]<0){
    //     echo json_encode(['status'=>400, 'message'=>$fee_structure['Name'].' cannot be empty!']);
    //     exit();
    //   }

    //   if(($fee_structure['Fee_Applicable_ID']==2 || $fee_structure['Fee_Applicable_ID']==3) && (empty($applicable[$fee_structure['ID']]) || empty(array_filter($applicable[$fee_structure['ID']])))){
    //     echo json_encode(['status'=>400, 'message'=>$fee_structure['Name'].' applicable cannot be empty!']);
    //     exit();
    //   }elseif($fee_structure['Fee_Applicable_ID']==1){
    //     $applicablity_in = [];
    //     for($i=1; $i<=$min_duration; $i++){
    //       $applicablity_in[] = $i;
    //     }
    //     $applicable[$fee_structure['ID']][1] = $applicablity_in;
    //   }elseif($fee_structure['Fee_Applicable_ID']==4){
    //     $applicable[$fee_structure['ID']][4] = [];
    //   }
    // }
    
    $check = $conn->query("SELECT ID FROM Sub_Courses WHERE (Name like '$name' OR Short_Name LIKE '$short_name') AND University_ID = $university_id AND Course_ID = $course AND Scheme_ID = $scheme AND ID <> $id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>$short_name.' already exists!']);
      exit();
    }
    
    $update = $conn->query("UPDATE `Sub_Courses` SET `Name` = '$name', `Short_Name` = '$short_name', `Course_ID` = $course, `Scheme_ID` = $scheme, `Mode_ID` = $mode, `Min_Duration` = $min_duration, `SOL` = $max_duration, `Lateral` = $lateral, `LE_Start` = '$le_start', `LE_SOL` = $le_sol, `Credit_Transfer` = $ct_transfer, `CT_Start` = $ct_start, `CT_SOL` = $ct_sol, Eligibility = '".json_encode($eligibilities)."' WHERE ID = $id");
    if($update){
      // foreach($fee as $key => $value){
      //   $applicable_in = [];
      //   if($fee_head_applicability[$key]==2){
      //     $applicable_in[2] = array_keys($applicable[$key][2]);
      //   }else{
      //     $applicable_in[$fee_head_applicability[$key]] = $applicable[$key][$fee_head_applicability[$key]];
      //   }
      //   $final_applicability = json_encode($applicable_in);
      //   $conn->query("DELETE FROM Fee_Constant WHERE Course_ID = $course AND Sub_Course_ID = $id AND Fee_Structure_ID = $key");
      //   $add_fee = $conn->query("INSERT INTO Fee_Constant (`Fee_Structure_ID`, `University_ID`, `Fee`, `Course_ID`, `Sub_Course_ID`, `Applicable_In`) VALUES ($key, $university_id, '$value', $course, $id, '$final_applicability')");
      // }
      // if($add_fee){
        echo json_encode(['status'=>200, 'message'=>$short_name.' updated successlly!']);
      }
    // }else{
    //   echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    // }
  }
