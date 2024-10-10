<?php
  if(isset($_POST['rule_name']) && isset($_POST['description']) && isset($_POST['category']) && isset($_POST['departments'])){
    include '../../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['rule_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $departments = array_filter($_POST['departments']);
    $sources = isset($_POST['sources']) ? array_filter($_POST['sources']) : array();
    $sub_sources = isset($_POST['sub_sources']) ? array_filter($_POST['sub_sources']) : array();
    $countries = isset($_POST['countries']) ? array_filter($_POST['countries']) : array();
    $states = isset($_POST['states']) ? array_filter($_POST['states']) : array();
    $cities = isset($_POST['cities']) ? array_filter($_POST['cities']) : array();
    $days = isset($_POST['show_after_days']) ? array_filter($_POST['show_after_days']) : array();
    $hours = isset($_POST['show_after_time']) ? array_filter($_POST['show_after_time']) : array();
    $roles = isset($_POST['roles']) ? array_filter($_POST['roles']) : array();
    $users = isset($_POST['users']) ? array_filter($_POST['users']) : array();

    if(empty($departments)){
      echo json_encode(['status'=>400, 'message'=>'Please select '.$_SESSION['Departments']]);
      exit();
    }

    $update = false;

    foreach($departments as $department){
      $source = array_key_exists($department, $sources) ? json_encode($sources[$department]) : json_encode(['All']);
      $sub_source = array_key_exists($department, $sub_sources) ? json_encode($sub_sources[$department]) : json_encode(['All']);
      $country = array_key_exists($department, $countries) ? json_encode($countries[$department]) : json_encode(['All']);
      $state = array_key_exists($department, $states) ? json_encode($states[$department]) : json_encode(['All']);
      $city = array_key_exists($department, $cities) ? json_encode($cities[$department]) : json_encode(['All']);
      $role = array_key_exists($department, $roles) ? json_encode($roles[$department]) : json_encode(['All']);
      $user = array_key_exists($department, $users) ? json_encode($users[$department]) : json_encode(['All']);
      $day = array_key_exists($department, $days) ? $days[$department] : 0;
      $hour = array_key_exists($department, $hours) ? $hours[$department] : 0;

      $update = $conn->query("INSERT INTO Assignment_Rules (`Name`, `Description`, `Category`, `Department_ID`, `Source`, `Sub_Source`, `Country`, `State`, `City`, `Day`, `Hour`, `Role`, `User`) VALUES ('$name', '$description', '$category', '$department', '$source', '$sub_source', '$country', '$state', '$city', '$day', '$hour', '$role', '$user')");
    
    }

    if($update){
      echo json_encode(['status'=>200, 'message'=>'Rule created successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }


  }else{
    echo json_encode(['status'=>403, 'message'=>'All fields are required!']);
  }
