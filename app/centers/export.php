<?php
  session_start();
  require '../../includes/db-config.php';
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
  include '../../includes/helpers.php';

  $searchValue = "";
  if(isset($_GET['search'])){
    $searchValue = mysqli_real_escape_string($conn,$_GET['search']); // Search value
  }
  if(isset($columnSortOrder)){
    $orderby = "ORDER BY $columnName $columnSortOrder";
  }else{
    $orderby = "ORDER BY Users.ID DESC";
  }
  
  // Session Query
  $session_query = "";
  $current_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']." AND Current_Status = 1");
  if($current_session->num_rows>0){
    $current_session = mysqli_fetch_assoc($current_session);
    $session_query = " AND Admission_Session_ID = ".$current_session['ID'];
  }

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
  if($_SESSION['Role']=='University Head' || $_SESSION['Role']=='Administrator' || $_SESSION['Role']=='Operations'){
    $university_query = " AND University_ID = ".$_SESSION['university_id'];
    $table = 'Alloted_Center_To_Counsellor';
  }elseif($_SESSION['Role']=='Counsellor'){
    $university_query = " AND University_ID = ".$_SESSION['university_id']." AND Counsellor_ID =".$_SESSION['ID'];
    $table = 'Alloted_Center_To_Counsellor';
  }elseif($_SESSION['Role']=='Sub-Counsellor'){
    $university_query = " AND University_ID = ".$_SESSION['university_id']." AND Sub_Counsellor_ID =".$_SESSION['ID'];
    $table = 'Alloted_Center_To_SubCounsellor';
  }
  $alloted_centers = $conn->query("SELECT Code FROM $table WHERE ID IS NOT NULL $university_query GROUP BY Code");
  while($alloted_center = $alloted_centers->fetch_assoc()){
    $center_ids[] = $alloted_center['Code'];
  }

  $center_ids = implode(',',$center_ids);

  ## Search 
  $searchQuery = " ";
  if($searchValue != ''){
    $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
  }

  ## Fetch records
  $result_record = "SELECT `ID`, `Name`, `Contact_Name`, `Code`, `Status`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users WHERE Role = 'Center' AND ID IN ($center_ids) $center_query $searchQuery $orderby";
  $empRecords = mysqli_query($conn, $result_record);

  $header = array('Code', 'Name', 'Contact Name', 'Admissions', 'RM', 'Status');

  // Fee Structure
  $center_fee_structures = array();
  $fee_structures = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = ".$_SESSION['university_id']." AND (Sharing = 1 OR Is_Constant = 0)");
  while($fee_structure = $fee_structures->fetch_assoc()){
    $center_fee_structures[$fee_structure['ID']] = $fee_structure['Name'];
  }

  $header = array_merge($header, $center_fee_structures);

  $final_data[] = $header;

  while ($row = mysqli_fetch_assoc($empRecords)) {

    // RM
    $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID WHERE Alloted_Center_To_Counsellor.Code = ".$row['ID']." AND Alloted_Center_To_Counsellor.University_ID = ".$_SESSION['university_id']."");
    $rm = mysqli_fetch_array($rm);

    // Admission Count
    $alloted_centers = array($row['ID']);
    $added_for = $alloted_centers;
    $sub_centers = $conn->query("SELECT ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'Sub-Center' AND Reporting = ".$row['ID']."");
    if($sub_centers->num_rows>0){
      while($sub_center = $sub_centers->fetch_assoc()){
        $alloted_sub_centers[] = $sub_center['ID'];
      }
      $added_for = array_filter(array_merge($alloted_centers, $alloted_sub_centers));
    }

    $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For IN (".implode(',', $added_for).") AND Step = 4 $session_query");
    $admissions = mysqli_fetch_assoc($admissions);

    $data = array( 
      $row['Code'],
      $row['Name'],
      $row['Contact_Name'],
      (int)$admissions['Applications'],
      $rm['Name'],
      $row["Status"]==1 ? 'Active' : 'Inactive'
    );

    // Center Fees
    foreach($center_fee_structures as $id => $center_fee_structure){
      $variables = $conn->query("SELECT Fee FROM Fee_Variables WHERE Code = ".$row['ID']." AND University_ID = ".$_SESSION['university_id']." AND Fee_Structure_ID = $id");
      $variable = $variables->fetch_assoc();
      array_push($data, (int)$variable['Fee']);
    }

    $final_data[] = $data;
  }

  $xlsx = SimpleXLSXGen::fromArray( $final_data )->downloadAs('Centers.xlsx');
