<?php

  require '../../includes/db-config.php';
  session_start();

  if(isset($_SESSION['Assigned_Department'])){
    $all_departments = implode(',',$_SESSION['Assigned_Department']);
    $department_query = " AND Lead_Status.Department_ID IN (".$all_departments.")";
  }else{
    $department_query = '';
  }

  // Role Query
  $role_query = str_replace("{{ table }}", "Lead_Status", $_SESSION['RoleQuery']);

  $lead_filter_query = "";
  if(isset($_SESSION['lead_filter_query'])){
    $lead_filter_query = $_SESSION['lead_filter_query'];
  }

  if(isset($_GET['id']) && isset($_GET['search'])){
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $searchValue = mysqli_real_escape_string($conn, $_GET['search']);

    ## Search 
    $searchQuery = " ";
    if($searchValue != ''){
      $searchQuery = " AND (Leads.Name LIKE '%".$searchValue."%' or Leads.Email LIKE '%".$searchValue."%' OR Leads.Mobile LIKE '%".$searchValue."%' OR Leads.Alternate_Mobile LIKE '%".$searchValue."%' OR Stages.Name LIKE '%".$searchValue."%' OR Reasons.Name LIKE '%".$searchValue."%' OR Sources.Name LIKE '%".$searchValue."%' OR Sub_Sources.Name LIKE '%".$searchValue."%' OR Departments.Name like '%".$searchValue."%' OR Categories.Name LIKE '%".$searchValue."%' OR Sub_Categories.Name LIKE '%".$searchValue."%' OR Users.Name LIKE '%".$searchValue."%' OR Users.Employee_ID LIKE '%".$searchValue."%')";
    }

    $all_count=$conn->query("SELECT COUNT(Leads.ID) as filtered FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Departments ON Lead_Status.Department_ID = Departments.ID LEFT JOIN Categories ON Lead_Status.Category_ID = Categories.ID LEFT JOIN Sub_Categories ON Lead_Status.Sub_Category_ID = Sub_Categories.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Lead_Status.Stage_ID = $id $searchQuery $department_query $lead_filter_query $role_query");
    $records = mysqli_fetch_assoc($all_count);
    echo $records['filtered'];
  }elseif(isset($_GET['user'])){
    $user = mysqli_real_escape_string($conn, $_GET['user']);
    $all_count=$conn->query("SELECT COUNT(Leads.ID) as filtered FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Categories ON Lead_Status.Category_ID = Categories.ID LEFT JOIN Sub_Categories ON Lead_Status.Sub_Category_ID = Sub_Categories.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Lead_Status.User_ID = $user $department_query $lead_filter_query $role_query");
    $records = mysqli_fetch_assoc($all_count);
    echo $records['filtered'];
  }
  else{
    $all_count=$conn->query("SELECT COUNT(Leads.ID) as filtered FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Categories ON Lead_Status.Category_ID = Categories.ID LEFT JOIN Sub_Categories ON Lead_Status.Sub_Category_ID = Sub_Categories.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Leads.ID IS NOT NULL $department_query $lead_filter_query $role_query");
    $records = mysqli_fetch_assoc($all_count);
    echo $records['filtered'];
  }
