<?php
  // ini_set('error_reporting', E_ALL );
  // ini_set('display_errors', 1 );
  session_start();
  require '../../includes/db-config.php';
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  if(isset($columnSortOrder)){
    $orderby = "ORDER BY $columnName $columnSortOrder";
  }else{
    $orderby = "ORDER BY Users.ID ASC";
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
  
  ## Search 
  $searchQuery = " ";
  if(isset($_GET['search'])){
    $searchValue = mysqli_real_escape_string($conn,$_GET['search']); // Search value
    if(!empty($searchValue)){
      $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
    }
  }
  
  
  ## Fetch records
  $result_record = "SELECT ID, `Name`, Short_Name, Contact_Name, `CanCreateSubCenter`, `Email`, `Mobile`, `Alternate_Mobile`, `Code`, `Address`, `City`, `District`, `State`, `Pincode`, `Status`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users WHERE Role = 'Center' $center_query $searchQuery $orderby";
  $empRecords = mysqli_query($conn, $result_record);
  $data[] = array('Code', 'Name', 'Short Name', 'Contact Name', 'Email', 'Mobile', 'Alternate Mobile', 'Address', 'City', 'District', 'State', 'Pincode', 'Password', 'Sub Center Access', 'Status', 'Universities');
  
  while ($row = mysqli_fetch_assoc($empRecords)) {
    $alloted_universities = $conn->query("SELECT GROUP_CONCAT(CONCAT(Universities.Short_Name, '(', Universities.Vertical, ')')) as Alloted_Universities FROM Alloted_Center_To_Counsellor LEFT JOIN Universities ON Alloted_Center_To_Counsellor.University_ID = Universities.ID WHERE Alloted_Center_To_Counsellor.Code = ".$row['ID']."");
    $alloted_universities = mysqli_fetch_assoc($alloted_universities);

    $data[] = array(
      $row['Code'],
      $row['Name'],
      $row['Short_Name'],
      $row['Contact_Name'],
      $row['Email'],
      $row['Mobile'],
      $row['Alternate_Mobile'],
      $row['Address'],
      $row['City'],
      $row['District'],
      $row['State'],
      $row['Pincode'],
      $row['password'],
      $row['CanCreateSubCenter']==1 ? 'Yes':'No',
      $row["Status"]==1 ? 'Active' : 'Inactive',
      $alloted_universities['Alloted_Universities']
    );
  }
  

  $xlsx = SimpleXLSXGen::fromArray( $data )->downloadAs('Center Master.xlsx');
