<?php
  if(isset($_GET['role'])){
    include '../../../includes/db-config.php';

    $role = mysqli_real_escape_string($conn, $_GET['role']);
    $university_id = intval($_GET['university_id']);

    if($role!='Center'){
      $join = "";
      $role_query = "";
  
      if($role!='Administrator'){
        $join = " LEFT JOIN University_User ON Users.ID = University_User.User_ID";
        $role_query = " AND University_ID = $university_id";
      }
  
      $options = "";
      $users = $conn->query("SELECT ID, CONCAT(Name, ' (', Code, ')') as Name FROM Users $join WHERE Role = '$role' $role_query");
      while ($user = $users->fetch_assoc()){
        $options .= '<option value="'.$user['ID'].'">'.$user['Name'].'</option>';
      }
    }else{
      $users = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Code = Users.ID WHERE Alloted_Center_To_Counsellor.University_ID = $university_id");
      while ($user = $users->fetch_assoc()){
        $options .= '<option value="'.$user['ID'].'">'.$user['Name'].'</option>';
      }
    }

    echo $options;
  }
