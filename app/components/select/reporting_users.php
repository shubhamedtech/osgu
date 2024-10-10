<?php

  if(isset($_POST['value']) && $_POST['department']){
    include '../../../includes/db-config.php';
    session_start();

    echo $value = mysqli_real_escape_string($conn, $_POST['value']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    if(empty($value) || empty($department)){
      echo 'missing';
      exit();
    }

    if($value!='Manager'){
      $get_all_users = $conn->query("SELECT `User_ID` FROM Users_Departments WHERE `Department_ID` = $department");
      if($get_all_users->num_rows>0){
        while($gau = $get_all_users->fetch_assoc()){
          $users[] = $gau['User_ID'];
        }
        $users = implode(',',$users);
      }else{
        echo 'add';
        exit();
      }
    }
    
    // Role Query
    if($value=='Manager'){
      $query = " AND Role = 'Administrator'";
    }elseif($value=='Asst. Manager'){
      $query = " AND Role = 'Manager' AND ID IN ($users)";
    }elseif($value=='Team Lead'){
      $query = " AND Role IN ('Asst. Manager', 'Manager') AND ID IN ($users)";
    }elseif($value=='Counsellor'){
      $query = " AND Role = 'Team Lead' AND ID IN ($users)";
    }

    $get_users = $conn->query("SELECT ID, Name, Employee_ID FROM Users WHERE ID IS NOT NULL $query");
    if($get_users->num_rows>0){
      while($gu = $get_users->fetch_assoc()){ ?>
        <option value="<?php echo $gu['ID']; ?>"><?php echo $gu['Name']." (".$gu['Employee_ID'].")"; ?></option>
      <?php }
    }else{
      echo 'add';
      exit();
    }
  }
