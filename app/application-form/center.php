<?php
  if(isset($_GET['university_id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_GET['university_id']);

    $center_query = "";
    if($_SESSION['Role']!="Administrator"){
      $check_has_unique_center_code = $conn->query("SELECT Center_Suffix FROM Universities WHERE ID = ".$_SESSION['university_id']." AND Has_Unique_Center = 1");
      if($check_has_unique_center_code->num_rows>0){
        $center_suffix = mysqli_fetch_assoc($check_has_unique_center_code);
        $center_suffix = $center_suffix['Center_Suffix'];
        $center_query = " AND Code LIKE '$center_suffix%' AND Is_Unique = 1";
      }else{
        $center_query = " AND Is_Unique = 0";
      }
    }

    $center_ids = array();
    $table = 'Alloted_Center_To_Counsellor';
    $university_query = "";
    if($_SESSION['Role']=='University Head' || $_SESSION['Role']=='Administrator' || $_SESSION['Role']=='Administrator'){
      $university_query = " AND University_ID = ".$_SESSION['university_id'];
      $table = 'Alloted_Center_To_Counsellor';
    }elseif($_SESSION['Role']=='Counsellor'){
      $university_query = " AND University_ID = ".$_SESSION['university_id']." AND Counsellor_ID =".$_SESSION['ID'];
      $table = 'Alloted_Center_To_Counsellor';
    }elseif($_SESSION['Role']=='Sub-Counsellor'){
      $university_query = " AND University_ID = ".$_SESSION['university_id']." AND Sub_Counsellor_ID =".$_SESSION['ID'];
      $table = 'Alloted_Center_To_SubCounsellor';
    }elseif($_SESSION['Role']=='Center'){
      $university_query = " AND University_ID = ".$_SESSION['university_id']." AND Code = ".$_SESSION['ID'];
      $table = 'Alloted_Center_To_Counsellor';
    }elseif($_SESSION['Role']=='Sub-Center'){
      $centers = $conn->query("SELECT `ID`, CONCAT(`Name`, ' (', `Code`, ')') as Name FROM Users WHERE Role = 'Sub-Center' AND ID = ".$_SESSION['ID']." ORDER BY Name ASC");
      while($center = $centers->fetch_assoc()){ ?>
        <option value="<?php echo $center['ID']?>"><?php echo $center['Name'] ?></option>
      <?php }
      exit();
    }
    $alloted_centers = $conn->query("SELECT Code FROM $table WHERE ID IS NOT NULL $university_query GROUP BY Code");
    while($alloted_center = $alloted_centers->fetch_assoc()){
      $center_ids[] = $alloted_center['Code'];
    }

    $center_ids = implode(',',$center_ids);

    if(empty($center_ids)){
      echo '<option value="">Please allot center</option>';
      exit();
    }
    
    $centers = $conn->query("SELECT `ID`, CONCAT(`Name`, ' (', `Code`, ')') as Name FROM Users WHERE Role = 'Center' AND ID IN ($center_ids) $center_query ORDER BY Code ASC");
    while($center = $centers->fetch_assoc()){ ?>
      <option value="<?php echo $center['ID']?>"><?php echo $center['Name'] ?></option>
    <?php }
  }
