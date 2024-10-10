<?php
## Database configuration
include '../../filestobeincluded/db_config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Lead_Status.Updated_At DESC";
}

if($_SESSION['current_stage']==0){
  $stage_query = '';
}elseif($_SESSION['current_stage']==-1){
  $stage_query = " AND Lead_Status.User_ID = ".$_SESSION['ID'];
}else{
  $stage_query = " AND Lead_Status.Stage_ID = ".$_SESSION['current_stage']."";
}

$lead_filter_query = "";
if(isset($_SESSION['lead_filter_query'])){
  $lead_filter_query = $_SESSION['lead_filter_query'];
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Leads.Name LIKE '%".$searchValue."%' OR Leads.Email LIKE '%".$searchValue."%' OR Leads.Alternate_Email LIKE '%".$searchValue."%' OR Leads.Mobile LIKE '%".$searchValue."%' OR Leads.Alternate_Mobile LIKE '%".$searchValue."%' OR Stages.Name LIKE '%".$searchValue."%' OR Reasons.Name LIKE '%".$searchValue."%' OR Sources.Name LIKE '%".$searchValue."%' OR Sub_Sources.Name LIKE '%".$searchValue."%' OR Departments.Name like '%".$searchValue."%' OR Categories.Name LIKE '%".$searchValue."%' OR Sub_Categories.Name LIKE '%".$searchValue."%' OR Users.Name LIKE '%".$searchValue."%' OR Users.Employee_ID LIKE '%".$searchValue."%')";
}

if(isset($_SESSION['Assigned_Department'])){
  $all_departments = implode(',',$_SESSION['Assigned_Department']);
  $department_query = " AND Lead_Status.Department_ID IN (".$all_departments.")";
}else{
  $department_query = '';
}

## Role Query
$role_query = str_replace("{{ table }}", "Lead_Status", $_SESSION['RoleQuery']);

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(Leads.ID) as allcount FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Departments ON Lead_Status.Department_ID = Departments.ID LEFT JOIN Categories ON Lead_Status.Category_ID = Categories.ID LEFT JOIN Sub_Categories ON Lead_Status.Sub_Category_ID = Sub_Categories.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Leads.ID IS NOT NULL $department_query $stage_query $lead_filter_query $role_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Leads.ID) as filtered FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Departments ON Lead_Status.Department_ID = Departments.ID LEFT JOIN Categories ON Lead_Status.Category_ID = Categories.ID LEFT JOIN Sub_Categories ON Lead_Status.Sub_Category_ID = Sub_Categories.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Leads.ID IS NOT NULL $searchQuery $department_query $stage_query $lead_filter_query $role_query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$leads = "SELECT 
          Lead_Status.ID,
          Leads.Name,
          Leads.Email,
          Leads.Alternate_Email,
          Leads.Mobile,
          Leads.Alternate_Mobile,
          Lead_Status.Department_ID as Department,
          Departments.Name as Departments,
          Categories.Name as Categories,
          Sub_Categories.Name as Sub_Categories,
          Stages.Name as Stages,
          Reasons.Name as Reasons,
          Sources.Name as Sources,
          Sub_Sources.Name as Sub_Sources,
          Users.Name as Users,
          Users.Employee_ID,
          Lead_Status.Created_At,
          Lead_Status.Updated_At
          FROM Leads
          LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID
          LEFT JOIN Departments ON Lead_Status.Department_ID = Departments.ID
          LEFT JOIN Categories ON Lead_Status.Category_ID = Categories.ID
          LEFT JOIN Sub_Categories ON Lead_Status.Sub_Category_ID = Sub_Categories.ID
          LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID
          LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID
          LEFT JOIN Sources ON Leads.Source_ID = Sources.ID
          LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID
          LEFT JOIN Users ON Lead_Status.User_ID = Users.ID
          WHERE Leads.ID IS NOT NULL 
          $searchQuery $department_query $stage_query $lead_filter_query $role_query $orderby
          LIMIT ".$row.",".$rowperpage;
$lead_records = mysqli_query($conn, $leads);
$data = array();
while ($row = mysqli_fetch_assoc($lead_records)) {
    $data[] = array( 
      "Name" => $row["Name"],
      "Email" => !empty($row["Email"]) ? $row["Email"] : "",
      "Alternate_Email" => !empty($row["Alternate_Email"]) ? $row["Alternate_Email"] : "",
      "Mobile" => !empty($row["Mobile"]) ? $row["Mobile"] : "",
      "Alternate_Mobile" => !empty($row["Alternate_Mobile"]) ? $row["Alternate_Mobile"] : "",
      "Department" => $row["Department"],
      "Departments" => $row['Departments'],
      "Categories" => !empty($row['Categories']) ? $row['Categories'] : "",
      "Sub_Categories" => !empty($row['Sub_Categories']) ? $row['Sub_Categories'] : "",
      "Stages" => !empty($row['Stages']) ? $row['Stages'] : "",
      "Reasons" => !empty($row['Reasons']) ? $row['Reasons'] : "",
      "Sources" => !empty($row['Sources']) ? $row['Sources'] : "",
      "Sub_Sources" => !empty($row['Sub_Sources']) ? $row['Sub_Sources'] : "",
      "Users" => $row['Users']." (".$row['Employee_ID'].")",
      "Created_At" => date('D, d M Y, h:i A', strtotime($row["Created_At"])),
      "Updated_At" => date('D, d M Y, h:i A', strtotime($row["Updated_At"])),
      "ID" => base64_encode("UZJkrI5snMyURJgpMWbM".$row['ID']."UZJkrI5snMyURJgpMWbM"),
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
