<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $val = 0;
  $condition = "";
  if ($_SESSION['Role'] != 'Administrator') {
    $condition = " WHERE `User_ID` = " . $_SESSION['ID'];
  }
  if ($_SESSION['Role'] == 'Center') {
    $universities = $conn->query("SELECT Universities.ID as University_ID, Universities.Is_B2C, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') AS Name, Universities.Has_Unique_Center, Universities.Has_Unique_StudentID, Universities.Logo, Universities.Is_Vocational, Universities.Has_LMS FROM Alloted_Center_To_Counsellor LEFT JOIN Universities ON Alloted_Center_To_Counsellor.University_ID = Universities.ID WHERE Alloted_Center_To_Counsellor.Code = " . $_SESSION['ID'] . "");
  } else {
    $universities = $conn->query("SELECT University_User.University_ID, Universities.Is_B2C, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') AS Name, Universities.Has_Unique_Center, Universities.Has_Unique_StudentID, Universities.Logo, Universities.Is_Vocational, Universities.Has_LMS FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID $condition");
  }
  while ($university = $universities->fetch_assoc()) {
    if ($university['University_ID'] == $id) {
      $_SESSION['university_id'] = $university['University_ID'];
      $_SESSION['university_name'] = $university['Name'];
      $_SESSION['unique_center'] = $university['Has_Unique_Center'];
      $_SESSION['student_id'] = $university['Has_Unique_StudentID'];
      $_SESSION['is_vocational'] = $university['Is_Vocational'];
      $_SESSION['university_logo'] = $university['Logo'];
      $_SESSION['has_lms'] = $university['Has_LMS'];
      $_SESSION['crm'] = $university['Is_B2C'];
      $val = 1;
      break;
    }
  }

  // RolesQuery
  $role_query = " AND {{ table }}.{{ column }} = " . $_SESSION['ID'];

  if ($_SESSION['Role'] === 'Administrator' || $_SESSION['Role'] === 'University Head' || $_SESSION['Role'] == 'Operations' || $_SESSION['Role'] == 'Accountant') {
    $center_list = array();
    $sub_center_list = array();
    $get_all_center = $conn->query("SELECT DISTINCT(Code) FROM Alloted_Center_To_Counsellor WHERE University_ID = '" . $_SESSION['university_id'] . "'");
    if ($get_all_center->num_rows > 0) {
      while ($gac = $get_all_center->fetch_assoc()) {
        $center_list[] = $gac['Code'];
        $center_lists = "(" . implode(",", $center_list) . ")";
        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND {{ table }}.{{ column }} IN $all_lists";
          } else {
            $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
          }
        }
      }
    }
  } elseif ($_SESSION['Role'] == 'Counsellor') {
    $center_list = array();
    $sub_center_list = array();
    $get_all_center = $conn->query("SELECT DISTINCT(Code) FROM Alloted_Center_To_Counsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Counsellor_ID = '" . $_SESSION['ID'] . "'");
    if ($get_all_center->num_rows > 0) {
      while ($gac = $get_all_center->fetch_assoc()) {
        $center_list[] = $gac['Code'];
        $center_lists = "(" . implode(",", $center_list) . ")";
        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND {{ table }}.{{ column }} IN $all_lists";
          } else {
            $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
          }
        }
      }
    }
  } elseif ($_SESSION['Role'] === 'Sub-Counsellor') {
    $center_list = array();
    $sub_center_list = array();
    $get_all_center = $conn->query("SELECT DISTINCT(Code) FROM Alloted_Center_To_SubCounsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Sub_Counsellor_ID = '" . $_SESSION['ID'] . "'");
    if ($get_all_center->num_rows > 0) {
      while ($gac = $get_all_center->fetch_assoc()) {
        $center_list[] = $gac['Code'];
        $center_lists = "('" . implode("','", ($center_list)) . "')";
        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND {{ table }}.{{ column }} IN $all_lists";
          } else {
            $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
          }
        }
      }
    }
  } elseif ($_SESSION['Role'] === 'Center') {
    $center_list[] = $_SESSION['ID'];
    $get_sub_center_list = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = '" . $_SESSION['ID'] . "'");
    if ($get_sub_center_list->num_rows > 0) {
      while ($gscl = $get_sub_center_list->fetch_assoc()) {
        $sub_center_list[] = $gscl['User_ID'];
      }
      $all_list = array_merge($center_list, $sub_center_list);
      $all_lists = "(" . implode(",", ($all_list)) . ")";
      $role_query = " AND {{ table }}.{{ column }} IN $all_lists";
    }
  } elseif ($_SESSION['Role'] === 'Sub-Center') {
    $role_query = " AND {{ table }}.{{ column }} = '" . $_SESSION['ID'] . "'";
  }

  $_SESSION['RoleQuery'] = $role_query;

  $gateway = $conn->query("SELECT * FROM Payment_Gateways WHERE University_ID = " . $_SESSION['university_id']);
  if ($gateway->num_rows > 0) {
    $gateway = $gateway->fetch_assoc();
    $_SESSION['gateway'] = $gateway['Type'];
    $_SESSION['access_key'] = $gateway['Access_Key'];
    $_SESSION['secret_key'] = $gateway['Secret_Key'];
  } else {
    unset($_SESSION['gateway']);
    unset($_SESSION['access_key']);
    unset($_SESSION['secret_key']);
  }

  if ($val == 1) {
    echo json_encode(['status' => '200', 'message' => 'University updated successfully!']);
  } else {
    echo json_encode(['status' => 403, 'message' => 'University not found!']);
  }
}
