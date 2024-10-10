<?php
## Database configuration
include '../../includes/db-config.php';
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
  $orderby = "ORDER BY Follow_Ups.ID DESC";
}

if ($_SESSION['current_followup'] == 0) {
  $followup_query = " AND DATE(`At`) = CURDATE() AND Follow_Ups.Status = 0";
} elseif ($_SESSION['current_followup'] == 1) {
  $followup_query = " AND DATE(`At`) = DATE(NOW() + INTERVAL 1 DAY) AND Follow_Ups.Status = 0";
} elseif ($_SESSION['current_followup'] == 2) {
  $followup_query = " AND DATE(`At`) >= DATE(NOW() + INTERVAL 2 DAY) AND Follow_Ups.Status = 0";
} elseif ($_SESSION['current_followup'] == 3) {
  $followup_query = " AND DATE(`At`) = NOW() - INTERVAL 3 MONTH AND Follow_Ups.Status = 0";
}

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Leads.Name LIKE '%" . $searchValue . "%' or Leads.Email LIKE '%" . $searchValue . "%' OR Leads.Mobile LIKE '%" . $searchValue . "%' OR Leads.Alternate_Mobile LIKE '%" . $searchValue . "%' OR Stages.Name LIKE '%" . $searchValue . "%' OR Reasons.Name LIKE '%" . $searchValue . "%' OR Sources.Name LIKE '%" . $searchValue . "%' OR Sub_Sources.Name LIKE '%" . $searchValue . "%' OR Universities.Name like '%" . $searchValue . "%' OR Courses.Name LIKE '%" . $searchValue . "%' OR Sub_Courses.Name LIKE '%" . $searchValue . "%' OR Users.Name LIKE '%" . $searchValue . "%' OR Users.Code LIKE '%" . $searchValue . "%')";
}

if (isset($_SESSION['departmentid'])) {
  $department_query = " AND Lead_Status.University_ID = " . $_SESSION['departmentid'] . "";
} else {
  $department_query = '';
}

## Role Query
$role_query = str_replace("{{ table }}", "Follow_Ups", $_SESSION['RoleQuery']);
$role_query = str_replace("{{ column }}", "User_ID", $role_query);

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(Follow_Ups.ID) as allcount FROM Follow_Ups LEFT JOIN Leads ON Follow_Ups.Lead_ID = Leads.ID LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID AND Lead_Status.University_ID = Follow_Ups.University_ID WHERE Lead_Status.User_ID = Follow_Ups.User_ID $department_query $followup_query $role_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Follow_Ups.ID) as filtered FROM Follow_Ups LEFT JOIN Leads ON Follow_Ups.Lead_ID = Leads.ID LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID AND Lead_Status.University_ID = Follow_Ups.University_ID LEFT JOIN Universities ON Lead_Status.University_ID = Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID LEFT JOIN Sources ON Leads.Source_ID = Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID = Users.ID WHERE Lead_Status.User_ID = Follow_Ups.User_ID $searchQuery $department_query $followup_query $role_query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$leads = "SELECT 
          Follow_Ups.At,
          Follow_Ups.Remark,
          Lead_Status.ID,
          Leads.Name,
          Leads.Email,
          Leads.Mobile,
          Leads.Alternate_Mobile,
          Lead_Status.University_ID as Department,
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
          Leads.Created_At,
          Lead_Status.Updated_At
          FROM Follow_Ups
          LEFT JOIN Leads ON Follow_Ups.Lead_ID = Leads.ID
          LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID AND Lead_Status.University_ID = Follow_Ups.University_ID
          LEFT JOIN Universities ON Lead_Status.University_ID = Universities.ID
          LEFT JOIN Courses ON Lead_Status.Course_ID = Courses.ID
          LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID = Sub_Courses.ID
          LEFT JOIN Stages ON Lead_Status.Stage_ID = Stages.ID
          LEFT JOIN Reasons ON Lead_Status.Reason_ID = Reasons.ID
          LEFT JOIN Sources ON Leads.Source_ID = Sources.ID
          LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID = Sub_Sources.ID
          LEFT JOIN Users ON Lead_Status.User_ID = Users.ID
          WHERE Lead_Status.User_ID = Follow_Ups.User_ID
          $searchQuery $department_query $followup_query $role_query $orderby
          LIMIT " . $row . "," . $rowperpage;
$lead_records = mysqli_query($conn, $leads);
$data = array();
while ($row = mysqli_fetch_assoc($lead_records)) {
  $data[] = array(
    "At" => date("M d, Y H:i:s", strtotime($row["At"])),
    "Formated_Date" => date("M d, Y h:i A", strtotime($row["At"])),
    "Remark" => $row["Remark"],
    "Name" => $row["Name"],
    "Email" => !empty($row["Email"]) ? $row["Email"] : "",
    "Alternate_Email" => !empty($row["Alternate_Email"]) ? $row["Alternate_Email"] : "",
    "Mobile" => !empty($row["Mobile"]) ? $row["Mobile"] : "",
    "Alternate_Mobile" => !empty($row["Alternate_Mobile"]) ? $row["Alternate_Mobile"] : "",
    "Department" => $row['Department'],
    "Universities" => $row['Universities'],
    "Courses" => !empty($row["Courses"]) ? $row['Courses'] : "",
    "Sub_Courses" => !empty($row["Sub_Courses"]) ? $row['Sub_Courses'] : "",
    "Stages" => !empty($row["Stages"]) ? $row['Stages'] : "",
    "Reasons" => !empty($row["Reasons"]) ? $row['Reasons'] : "",
    "Sources" => !empty($row["Sources"]) ? $row['Sources'] : "",
    "Sub_Sources" => !empty($row["Sub_Sources"]) ? $row['Sub_Sources'] : "",
    "Users" => $row['Users'] . " (" . $row['Code'] . ")",
    "Admission" => $row['Admission'],
    "Created_At" => $row["Created_At"],
    "Updated_At" => $row["Updated_At"],
    "ID"  => base64_encode("W1Ebt1IhGN3ZOLplom9I" . $row['ID'] . "W1Ebt1IhGN3ZOLplom9I"),
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
