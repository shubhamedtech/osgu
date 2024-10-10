<?php
ini_set('display_errors', 1);
session_start();
require '../../includes/db-config.php';
require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

$search_value = "";
if (isset($_GET['search'])) {
  $search_value = mysqli_real_escape_string($conn, $_GET['search']); // Search value
}

if (isset($_SESSION['current_session'])) {
  if ($_SESSION['current_session'] == 'All') {
    $session_query = '';
  } else {
    $session_query = "AND Admission_Sessions.Name like '%" . $_SESSION['current_session'] . "%'";
  }
} else {
  $get_current_session = $conn->query("SELECT Name FROM Admission_Sessions WHERE Current_Status = 1 AND University_ID = '" . $_SESSION['university_id'] . "'");
  if ($get_current_session->num_rows > 0) {
    $gsc = mysqli_fetch_assoc($get_current_session);
    $session_query = "AND Admission_Sessions.Name like '%" . $gsc['Name'] . "%'";
  } else {
    $session_query = '';
  }
}

$role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'Added_For', $role_query);

$step_query = "";

if (in_array($_SESSION['Role'], ['Sub-Center', 'Center'])) {
  $header = array('Student_ID', 'Enrollment_No', 'Step', 'Added On', 'Processed By Center', 'Document Verified', 'Payment Verified', 'Processed To University', 'Student Name', 'Father Name', 'Mother Name', 'Adm Type', 'Session', 'Duration', 'Mode', 'Course', 'Sub Course', 'Short Name', 'Email', 'Contact', 'Aadhar Number', 'DOB', 'Gender', 'Nationality', 'Code', 'Center Name', 'RM', 'Export Documents');
} else {
  $header = array('Student_ID', 'Enrollment_No', 'OA_Number', 'Step', 'Added On', 'Processed By Center', 'Document Verified', 'Payment Verified', 'Processed To University', 'Student Name', 'Father Name', 'Mother Name', 'Adm Type', 'Session', 'Duration', 'Mode', 'Course', 'Sub Course', 'Short Name', 'Email', 'Contact', 'Alternate Email', 'Alternate Contact', 'Aadhar Number', 'DOB', 'Employement Status', 'Gender', 'Category', 'Address', 'City', 'District', 'State', 'Pincode', 'Nationality', 'High School', 'Subject', 'Year', 'Board/Institute', 'Marks Obtained', 'Maximum Marks', 'Total Marks', 'Intermediate', 'Subject', 'Year', 'Board/Institute', 'Marks Obtained', 'Maximum Marks', 'Total Marks', 'UG', 'Subject', 'Year', 'Board/Institute', 'Marks Obtained', 'Maximum Marks', 'Total Marks', 'PG', 'Subject', 'Year', 'Board/Institute', 'Marks Obtained', 'Maximum Marks', 'Total Marks', 'Other', 'Subject', 'Year', 'Board/Institute', 'Marks Obtained', 'Maximum Marks', 'Total Marks', 'Code', 'Center Name', 'RM', 'Export Documents');
}

if ($_SESSION['Role'] != 'Sub-Center') {
  $fee_structures = $conn->query("SELECT ID, Name, Sharing FROM Fee_Structures WHERE University_ID = " . $_SESSION['university_id'] . " ORDER BY Fee_Applicable_ID");
  while ($fee_structure = $fee_structures->fetch_assoc()) {
    if ($fee_structure['Sharing'] == 1) {
      array_push($header, $fee_structure['Name'] . " Without Sharing");
      array_push($header, $fee_structure['Name'] . " %");
      array_push($header, $fee_structure['Name']);
    } else {
      array_push($header, $fee_structure['Name']);
    }
  }
  array_push($header, "Total");
} else {
  unset($header[4]);
  unset($header[5]);
  unset($header[6]);
  unset($header[7]);
  unset($header[24]);
  unset($header[25]);
  unset($header[26]);
}

## Search 
$searchQuery = " ";
if ($search_value != '') {
  if (!empty(strpos($search_value, "="))) {
    $search = explode("=", $search_value);
    $searchBy = trim($search[0]);
    $values = array_key_exists(1, $search) && !empty($search[1]) ? explode(" ", $search[1]) : array();
    $values = array_filter($values);
    if (!empty($values)) {
      $student_id_column = $_SESSION['student_id'] == 1 ? 'Students.Unique_ID' : "RIGHT(CONCAT('000000', Students.ID), 6)";
      $column = strcasecmp($searchBy, 'student id') == 0 ?  $student_id_column : (strcasecmp($searchBy, 'enrollment') == 0 ? 'Students.Enrollment_No' : (strcasecmp($searchBy, 'oa number') == 0 ? 'OA_Number' : ''));
      if (!empty($column)) {
        $values = "'" . implode("','", $values) . "'";
        $searchQuery = " AND $column IN ($values)";
      }
    }
  } elseif (strcasecmp($searchValue, 'completed') == 0) {
    $searchQuery = " AND Step = 4 ";
  } else {
    $searchQuery = " AND (Students.ID like '%" . $search_value . "%' OR Students.First_Name like '%" . $search_value . "%' OR Students.Middle_Name like '%" . $search_value . "%' OR Students.Last_Name like '%" . $search_value . "%' OR Admission_Sessions.Name like '%" . $search_value . "%' OR Admission_Types.Name like '%" . $search_value . "%' OR Students.Step like '%" . $search_value . "%' OR Students.Father_Name like '%" . $search_value . "%' OR Students.Email like '%" . $search_value . "%' OR Students.Contact like '%" . $search_value . "%' OR Sub_Courses.Short_Name like '%" . $search_value . "%')";
  }
}

$filterQueryUser = "";
if (isset($_SESSION['filterByUser'])) {
  $filterQueryUser = $_SESSION['filterByUser'];
}

$filterByDepartment = "";
if (isset($_SESSION['filterByDepartment'])) {
  $filterByDepartment = $_SESSION['filterByDepartment'];
}

$filterByDate = "";
if (isset($_SESSION['filterByDate'])) {
  $filterByDate = $_SESSION['filterByDate'];
}

$filterBySubCourse = "";
if (isset($_SESSION['filterBySubCourses'])) {
  $filterBySubCourse = $_SESSION['filterBySubCourses'];
}

$filterByStatus = "";
if (isset($_SESSION['filterByStatus'])) {
  $filterByStatus = $_SESSION['filterByStatus'];
}

$searchQuery .= $filterByDepartment . $filterQueryUser . $filterByDate . $filterBySubCourse . $filterByStatus;

## Fetch records
$result_record = "SELECT Students.ID, Students.Unique_ID, Students.Enrollment_No, Students.OA_Number, Students.Step, Students.Created_At, Students.Process_By_Center, Students.Document_Verified, Students.Payment_Received, Students.Processed_To_University, TRIM(CONCAT(Students.First_Name, IF(Students.Middle_Name!='', CONCAT(' ', Students.Middle_Name), ''), ' ', Students.Last_Name)) as Name, Students.Father_Name, Students.Mother_Name, Admission_Types.`Name` as Adm_Type, Admission_Sessions.`Name` as Session, Students.Duration, Modes.`Name` as Mode, Courses.`Name` as Course, Sub_Courses.`Name` AS Sub_Course, Sub_Courses.Short_Name as Short_Name, Students.Email, Students.Contact, Students.Alternate_Email, Students.Alternate_Contact, Students.Aadhar_Number, Students.DOB, Students.Employement_Status, Students.Gender, Students.Category, REPLACE(JSON_EXTRACT(Students.Address, '$.present_address'), '\"', '') as Address, REPLACE(JSON_EXTRACT(Students.Address, '$.present_city'), '\"', '') as City, REPLACE(JSON_EXTRACT(Students.Address, '$.present_district'), '\"', '') as District, REPLACE(JSON_EXTRACT(Students.Address, '$.present_state'), '\"', '') as State, REPLACE(JSON_EXTRACT(Students.Address, '$.present_pincode'), '\"', '') as Pincode, Students.Nationality, Students.Added_For, Students.Course_ID, Students.Sub_Course_ID FROM Students LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Modes ON Students.Mode_ID = Modes.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $searchQuery $role_query $step_query $session_query ORDER BY ID DESC";
$empRecords = mysqli_query($conn, $result_record);
$data[] = $header;

while ($row = mysqli_fetch_row($empRecords)) {

  // Added_For
  if ($_SESSION['Role'] == 'Center') {
    $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row[35] . "");
  } else {
    $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row[35] . " AND Role = 'Center'");
    if ($user->num_rows == 0) {
      $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row[35]);
    }
  }

  if ($user->num_rows > 0) {
    $user = mysqli_fetch_array($user);
  } else {
    $user['Name'] = "";
    $user['Code'] = "";
    $user['ID'] = 0;
  }

  // RM
  $rm['Name'] = "";
  if (!empty($user['Name'])) {
    // RM
    $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'] . " WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id']);
    if ($rm->num_rows > 0) {
      $rm = mysqli_fetch_array($rm);
    } else {
      $rm = $user;
    }
  }

  // Academics
  if (!in_array($_SESSION['Role'], ['Sub-Center', 'Center'])) {
    $courses = array('High School', 'Intermediate', 'UG', 'PG', 'Other');
    foreach ($courses as $course) {
      $academics = $conn->query("SELECT Type, Subject, `Year`, `Board/Institute`, Marks_Obtained, Max_Marks, Total_Marks FROM Student_Academics WHERE Student_ID = $row[0] AND Type = '$course'");
      if ($academics->num_rows > 0) {
        $academic = mysqli_fetch_row($academics);
      } else {
        $academic = array($course, '', '', '', '', '', '');
      }
      $row = array_merge($row, $academic);
    }
  }

  if ($_SESSION['Role'] != 'Sub-Center') {
    array_push($row, $user['Code']);
    array_push($row, $user['Name']);
    array_push($row, $rm['Name']);
  }
  $row[5] = date("d-m-Y H:i A", strtotime($row[5]));
  $row[6] = !empty($row[6]) ? date("d-m-Y H:i A", strtotime($row[6])) : "";
  $row[7] = !empty($row[7]) ? date("d-m-Y H:i A", strtotime($row[7])) : "";
  $row[8] = !empty($row[8]) ? date("d-m-Y H:i A", strtotime($row[8])) : "";
  $row[9] = !empty($row[9]) ? date("d-m-Y H:i A", strtotime($row[9])) : "";
  $encode = base64_encode($row[0] . "W1Ebt1IhGN3ZOLplom9I");
  array_push($row, '<i><a href="https://' . $_SERVER['HTTP_HOST'] . '/app/applications/zip?id=' . $encode . '">Click Here</a></i>');
  if ($_SESSION['Role'] != 'Sub-Center') {
    $student_fee = $conn->query("SELECT Fee FROM Student_Ledgers WHERE Student_ID = $row[0] LIMIT 1");
    if ($student_fee->num_rows > 0) {
      $student_fee = $student_fee->fetch_assoc();
      $student_fee = json_decode($student_fee['Fee'], true);
      $fee_structures = $conn->query("SELECT ID, Sharing FROM Fee_Structures WHERE University_ID = " . $_SESSION['university_id'] . " ORDER BY Fee_Applicable_ID");
      while ($fee_structure = $fee_structures->fetch_assoc()) {
        if ($fee_structure['Sharing'] == 1) {
          $constant = $conn->query("SELECT Fee FROM Fee_Constant WHERE Fee_Structure_ID = " . $fee_structure['ID'] . " AND Course_ID = " . $row[36] . " AND Sub_Course_ID = " . $row[37]);
          $constant = $constant->fetch_assoc();
          array_push($row, $constant['Fee']);
          $sharing = $conn->query("SELECT Fee FROM Fee_Variables WHERE Fee_Structure_ID = " . $fee_structure['ID'] . " AND Code = " . $user['ID'] . " AND University_ID = " . $_SESSION['university_id'] . "");
          if ($sharing->num_rows > 0) {
            $sharing = $sharing->fetch_assoc();
            array_push($row, $sharing['Fee']);
          } else {
            array_push($row, 0);
          }
          array_push($row, $student_fee[$fee_structure['ID']]);
        } else {
          //array_push($row, $student_fee[$fee_structure['ID']]);
        }
      }
      //array_push($row, array_sum($student_fee));
    }
  }
  unset($row[35]);
  unset($row[36]);
  unset($row[37]);

  if ($_SESSION['Role'] == 'Center') {
    unset($row[3]);
    unset($row[22]);
    unset($row[23]);
    unset($row[26]);
    unset($row[28]);
    unset($row[29]);
    unset($row[30]);
    unset($row[31]);
    unset($row[32]);
    unset($row[33]);
  }

  if ($_SESSION['Role'] == 'Sub-Center') {
    unset($row[3]);
    unset($row[22]);
    unset($row[23]);
    unset($row[26]);
    unset($row[28]);
    unset($row[29]);
    unset($row[30]);
    unset($row[31]);
    unset($row[32]);
    unset($row[33]);
    unset($row[6]);
    unset($row[7]);
    unset($row[8]);
    unset($row[9]);
    unset($row[70]);
    unset($row[71]);
    unset($row[72]);
  }

  if (!empty($row[1])) {
    $row[0] = $row[1];
  } else {
    $row[0] = '<b>' . sprintf("%'.06d\n", $row[0]) . '</b>';
  }

  unset($row[1]);


  $data[] = $row;
}

$xlsx = SimpleXLSXGen::fromArray($data)->downloadAs('Students.xlsx');
