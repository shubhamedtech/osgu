<?php
## Database configuration
require '../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Lead_Status.Updated_At DESC";
}

if ($_SESSION['current_stage'] == 0) {
  $stage_query = '';
} elseif ($_SESSION['current_stage'] == -1) {
  $stage_query = " AND Lead_Status.User_ID = " . $_SESSION['ID'];
} else {
  $stage_query = " AND Lead_Status.Stage_ID = " . $_SESSION['current_stage'] . "";
}

$lead_filter_query = "";
if (isset($_SESSION['lead_filter_query'])) {
  $lead_filter_query = $_SESSION['lead_filter_query'];
}

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Leads.Name LIKE '%" . $searchValue . "%' OR Leads.Email LIKE '%" . $searchValue . "%' OR Leads.Alternate_Email LIKE '%" . $searchValue . "%' OR Leads.Mobile LIKE '%" . $searchValue . "%' OR Leads.Alternate_Mobile LIKE '%" . $searchValue . "%' OR Stages.Name LIKE '%" . $searchValue . "%' OR Reasons.Name LIKE '%" . $searchValue . "%' OR Sources.Name LIKE '%" . $searchValue . "%' OR Sub_Sources.Name LIKE '%" . $searchValue . "%' OR Universities.Name like '%" . $searchValue . "%' OR Courses.Name LIKE '%" . $searchValue . "%' OR Sub_Courses.Name LIKE '%" . $searchValue . "%' OR Users.Name LIKE '%" . $searchValue . "%' OR Users.Code LIKE '%" . $searchValue . "%')";
}

if (isset($_SESSION['university_id'])) {
  $department_query = " AND Lead_Status.University_ID = " . $_SESSION['university_id'] . "";
} else {
  $department_query = '';
}

## Role Query
$role_query = str_replace("{{ table }}", "Lead_Status", $_SESSION['RoleQuery']);
$role_query = str_replace("{{ column }}", "User_ID", $role_query);

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(Leads.ID) as allcount FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID = Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Leads.ID IS NOT NULL $department_query $stage_query $lead_filter_query $role_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Leads.ID) as filtered FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID = Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Leads.ID IS NOT NULL $searchQuery $department_query $stage_query $lead_filter_query $role_query");
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
          Lead_Status.University_ID as Department,
          Lead_Status.Unique_ID,
          Universities.Name as Universities,
          Courses.Name as Courses,
          Sub_Courses.Name as Sub_Courses,
          Stages.Name as Stages,
          Reasons.Name as Reasons,
          Sources.Name as Sources,
          Sub_Sources.Name as Sub_Sources,
          Users.Name as Users,
          Users.Code,
          Lead_Status.Admission,
          Lead_Status.Created_At,
          Lead_Status.Updated_At
          FROM Leads
          LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID
          LEFT JOIN Universities ON Lead_Status.University_ID = Universities.ID
          LEFT JOIN Courses ON Lead_Status.Course_ID = Courses.ID
          LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID = Sub_Courses.ID
          LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID
          LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID
          LEFT JOIN Sources ON Leads.Source_ID = Sources.ID
          LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID
          LEFT JOIN Users ON Lead_Status.User_ID = Users.ID
          WHERE Leads.ID IS NOT NULL 
          $searchQuery $department_query $stage_query $lead_filter_query $role_query $orderby
          LIMIT " . $row . "," . $rowperpage;
$lead_records = mysqli_query($conn, $leads);
$data = array();
while ($row = mysqli_fetch_assoc($lead_records)) {
  $data[] = array(
    "Unique_ID" => !empty($row['Unique_ID']) ? $row['Unique_ID'] : "",
    "Name" => $row["Name"],
    "Email" => !empty($row["Email"]) ? $row["Email"] : "",
    "Alternate_Email" => !empty($row["Alternate_Email"]) ? $row["Alternate_Email"] : "",
    "Mobile" => !empty($row["Mobile"]) ? $row["Mobile"] : "",
    "Alternate_Mobile" => !empty($row["Alternate_Mobile"]) ? $row["Alternate_Mobile"] : "",
    "Department" => $row["Department"],
    "Universities" => $row['Universities'],
    "Courses" => !empty($row['Courses']) ? $row['Courses'] : "",
    "Sub_Courses" => !empty($row['Sub_Courses']) ? $row['Sub_Courses'] : "",
    "Stages" => !empty($row['Stages']) ? $row['Stages'] : "",
    "Reasons" => !empty($row['Reasons']) ? $row['Reasons'] : "",
    "Sources" => !empty($row['Sources']) ? $row['Sources'] : "",
    "Sub_Sources" => !empty($row['Sub_Sources']) ? $row['Sub_Sources'] : "",
    "Users" => $row['Users'] . " (" . $row['Code'] . ")",
    "Created_At" => date('D, d M Y, h:i A', strtotime($row["Created_At"])),
    "Updated_At" => date('D, d M Y, h:i A', strtotime($row["Updated_At"])),
    "Admission" => $row['Admission'],
    "ID" => base64_encode("W1Ebt1IhGN3ZOLplom9I" . $row['ID'] . "W1Ebt1IhGN3ZOLplom9I"),
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
