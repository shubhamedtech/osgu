<?php
if(isset($_FILES['lead_file'])){
  require ('../../excel/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
  require('../../spreadsheet/SpreadsheetReader.php');
  require('../../filestobeincluded/db_config.php');
  session_start();

  $export_data = array();

  $mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  if(in_array($_FILES["lead_file"]["type"],$mimes)){
    
    // Upload File
    $uploadFilePath = '../../uploads/for_import/'.basename($_FILES['lead_file']['name']);
    move_uploaded_file($_FILES['lead_file']['tmp_name'], $uploadFilePath);

    // Read File
    $reader = new SpreadsheetReader($uploadFilePath);

    // Sheet Count
    $totalSheet = count($reader->sheets());

    /* For Loop for all sheets */
    for($i=0; $i<$totalSheet; $i++){

      $reader->ChangeSheet($i);

      foreach ($reader as $row)
      {
        // Code Start Here
        $id = mysqli_real_escape_string($conn, $row[0]);
        $name = mysqli_real_escape_string($conn, $row[1]);
        $email = mysqli_real_escape_string($conn, $row[2]);
        $mobile = mysqli_real_escape_string($conn, $row[3]);
        $alternate_mobile = mysqli_real_escape_string($conn, $row[4]);
        if(empty($alternate_mobile)){
          $alternate_mobile = NULL;
        }
        $department_name = mysqli_real_escape_string($conn, $row[5]);
        $category_name = mysqli_real_escape_string($conn, $row[6]);
        $sub_category_name = mysqli_real_escape_string($conn, $row[7]);
        $stage_name = mysqli_real_escape_string($conn, $row[8]);
        $reason_name = mysqli_real_escape_string($conn, $row[9]);
        $source_name = mysqli_real_escape_string($conn, $row[10]);
        $sub_source_name = mysqli_real_escape_string($conn, $row[11]);
        $city_name = mysqli_real_escape_string($conn, $row[12]);
        $state_name = mysqli_real_escape_string($conn, $row[13]);
        $country_name = mysqli_real_escape_string($conn, $row[14]);
        $extra = mysqli_real_escape_string($conn, $row[15]);
        $user_name = mysqli_real_escape_string($conn, $row[16]);

        // Data Check
        if(empty($id) || empty($name) || empty($mobile) || empty($source_name) || empty($department_name) || empty($category_name) || empty($stage_name) || empty($reason_name) || empty($user_name)){
          $error = "Data missing!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        if($id=='ID'){
          $export_data[] = ['ID', 'Full Name', 'Email', 'Mobile', 'Alternate Mobile (optional)', $_SESSION['Departments'], $_SESSION['Categories'], $_SESSION['Sub-Categories'].' (optional)', 'Stage', 'Reason', 'Source', 'Sub-Source (optional)', 'City', 'State', 'Country', 'Remark (if any)', 'Employee ID', 'Message'];
          continue;
        }

        // Department Check
        $department = $conn->query("SELECT ID FROM Departments WHERE Name LIKE '$department_name'");
        if($department->num_rows>0){
          $department = mysqli_fetch_assoc($department);
          $department = $department['ID'];
          $department_query = " AND Lead_Status.Department_ID = $department";
        }else{
          $error = $_SESSION['Departments']." not found!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        // Email Check
        if(!empty($email)){
          if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error = "Email format not correct!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
            continue;
          }
          $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') $department_query");
          if($check->num_rows>0){
            $error = "Email already exists!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
            continue;
          }
        }

        // Mobile Check
        if(!empty($mobile)){
          $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '%$mobile%' OR Alternate_Mobile LIKE '%$mobile%') $department_query");
          if($check->num_rows>0){
            $error = "Mobile already exists!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
            continue;
          }
        }
    
        // Alternate Mobile Check
        if(!empty($alternate_mobile)){
          $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '%$alternate_mobile%' OR Alternate_Mobile LIKE '%$alternate_mobile%') $department_query");
          if($check->num_rows>0){
            $error = "Alternate Mobile already exists!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
            continue;
          }
        }

        // Category Check
        $category = $conn->query("SELECT ID FROM Categories WHERE Name LIKE '$category_name'");
        if($category->num_rows>0){
          $category = mysqli_fetch_assoc($category);
          $category = $category['ID'];
        }else{
          $error = $_SESSION['Categories']." not found!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        // Sub-Category Check
        if(!empty($sub_category_name)){
          $sub_category = $conn->query("SELECT ID FROM Sub_Categories WHERE Name LIKE '$sub_category_name'");
          if($sub_category->num_rows>0){
            $sub_category = mysqli_fetch_assoc($sub_category);
            $sub_category = $sub_category['ID'];
          }else{
            $error = $_SESSION['Sub-Categories']." not found!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
            continue;
          }
        }else{
          $sub_category = 'NULL';
        }

        // Stage Check
        $stage = $conn->query("SELECT ID FROM Stages WHERE Name LIKE '$stage_name'");
        if($stage->num_rows>0){
          $stage = mysqli_fetch_assoc($stage);
          $stage = $stage['ID'];
        }else{
          $error = "Stage not found!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        // Reason Check
        $reason = $conn->query("SELECT ID FROM Reasons WHERE Name LIKE '$reason_name' AND Stage_ID = $stage");
        if($reason->num_rows>0){
          $reason = mysqli_fetch_assoc($reason);
          $reason = $reason['ID'];
        }else{
          $error = "Reason not found!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        // Source Check
        $source = $conn->query("SELECT ID FROM Sources WHERE Name LIKE '$source_name'");
        if($source->num_rows>0){
          $source = mysqli_fetch_assoc($source);
          $source = $source['ID'];
        }else{
          $error = "Source not found!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        // SubSource Check
        if(!empty($sub_source_name)){
          $sub_source = $conn->query("SELECT ID FROM Sub_Sources WHERE Name LIKE '$sub_source_name'");
          if($sub_source->num_rows>0){
            $sub_source = mysqli_fetch_assoc($sub_source);
            $sub_source = $sub_source['ID'];
          }else{
            $error = "Sub-Source not found!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
            continue;
          }
        }else{
          $sub_source = 'NULL';
        }

        // City Check
        if(!empty($city_name)){
          $city = $conn->query("SELECT ID FROM Cities WHERE Name LIKE '$city_name'");
          if($city->num_rows>0){
            $city = mysqli_fetch_assoc($city);
            $city = $city['ID'];
          }else{
            $city = 'NULL';
          }
        }else{
          $city = 'NULL';
        }

        // State Check
        if(!empty($state_name)){
          $state = $conn->query("SELECT ID FROM States WHERE Name LIKE '$state_name'");
          if($state->num_rows>0){
            $state = mysqli_fetch_assoc($state);
            $state = $state['ID'];
          }else{
            $state = 'NULL';
          }
        }else{
          $state = 'NULL';
        }

        // Country Check
        if(!empty($country_name)){
          $country = $conn->query("SELECT ID FROM Countries WHERE Name LIKE '$country_name' OR Short_Name LIKE '$country_name'");
          if($country->num_rows>0){
            $country = mysqli_fetch_assoc($country);
            $country = $country['ID'];
          }else{
            $country = 'NULL';
          }
        }else{
          $country = 'NULL';
        }

        // User Check
        $user = $conn->query("SELECT ID FROM Users WHERE Employee_ID LIKE '$user_name'");
        if($user->num_rows>0){
          $user = mysqli_fetch_assoc($user);
          $user = $user['ID'];
        }else{
          $error = "User not found!";
          $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          continue;
        }

        
        $conn->query('SET foreign_key_checks = 0');
        $check = $conn->query("SELECT ID FROM Leads WHERE Email LIKE '$email' OR (Mobile LIKE '$mobile' OR Alternate_Mobile LIKE '$mobile')");
        if($check->num_rows>0){
          $lead  = mysqli_fetch_assoc($check);
          $add_lead = true;
          $id = $lead['ID'];
        }else{
          $add_lead = $conn->query("INSERT INTO Leads (`Name`, `Email`, `Mobile`, `Alternate_Mobile`, `Source_ID`, `Sub_Source_ID`, `Country_ID`, `State_ID`, `City_ID`, `Extra`, `Created_By`) VALUES ('$name', '$email', '$mobile', '$alternate_mobile', $source, $sub_source, $country, $state, $city, '$extra', '".$_SESSION['ID']."')");
          if($add_lead){
            $id = $conn->insert_id;
          }else{
            // $error = "Something went wrong!";
            $error = 'Something went wrong!';
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          }
        }
    
        if($add_lead){
          $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `Department_ID`, `Category_ID`, `Sub_Category_ID`, `Stage_ID`, `Reason_ID`, `User_ID`) VALUES ($id, $department, $category, $sub_category, $stage, $reason, $user)");
          if($add_lead_status){
            $error = "Lead added successfully!";
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          }else{
            // $error = "Something went wrong!";
            $error = 'Something went wrong!';
            $export_data[] = [$id, $name, $email, $mobile, $alternate_mobile, $department_name, $category_name, $sub_category_name, $stage_name, $reason_name, $source_name, $sub_source_name, $city_name, $state_name, $country_name, $extra, $user_name, $error];
          }
        }
        
      }
    }
    $xlsx = SimpleXLSXGen::fromArray( $export_data )->saveAs('Lead.xlsx');
    unlink($uploadFilePath);
    echo json_encode(['status'=>200, 'file'=>'Lead.xlsx']);
  }else{
    echo json_encode(['status'=>400, 'message'=>'File type not supported!']);
  }
}
