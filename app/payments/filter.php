<?php
if (isset($_POST['id']) && isset($_POST['by']) && isset($_POST['page'])) {
  session_start();
  require '../../includes/db-config.php';

  $by = $_POST['by'];
  $id = intval($_POST['id']);
  $page = intval($_POST['page']);

  if ($by == 'users') {
    $user = $conn->query("SELECT Role FROM Users WHERE ID = $id");
    $user = $user->fetch_assoc();

    $role = $user['Role'];
    $role_query = " AND Payments.Added_By = $id";
    if ($role == 'Counsellor') {
      $center_list = array($id);
      $sub_center_list = array();

      $sub_counsellors = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (" . implode(",", $center_list) . ") AND University_ID = " . $_SESSION['university_id']);
      while ($sub_counsellor = $sub_counsellors->fetch_assoc()) {
        $center_list[] = $sub_counsellor['User_ID'];
      }

      $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_Counsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Counsellor_ID = $id");
      if ($get_all_center->num_rows > 0) {
        while ($gac = $get_all_center->fetch_assoc()) {
          $center_list[] = $gac['Code'];
        }
        $center_lists = "(" . implode(",", $center_list) . ")";
        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND Payments.Added_By IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND Payments.Added_By IN $all_lists";
          } else {
            $role_query = " AND Payments.Added_By IN $center_lists";
          }
        }
      }
    } elseif ($role === 'Sub-Counsellor') {
      $center_list = array($id);
      $sub_center_list = array();
      $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_SubCounsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Sub_Counsellor_ID = $id");
      if ($get_all_center->num_rows > 0) {
        while ($gac = $get_all_center->fetch_assoc()) {
          $center_list[] = $gac['Code'];
        }
        $center_lists = "('" . implode("','", ($center_list)) . "')";
        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND Payments.Added_By IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND Payments.Added_By IN $all_lists";
          } else {
            $role_query = " AND Payments.Added_By IN $center_lists";
          }
        }
      }
    } elseif ($role === 'Center') {
      $center_list[] = $id;
      $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = $id AND University_ID = " . $_SESSION['university_id']);
      if ($get_sub_center_list->num_rows > 0) {
        while ($gscl = $get_sub_center_list->fetch_assoc()) {
          $sub_center_list[] = $gscl['User_ID'];
        }
        $all_list = array_merge($center_list, $sub_center_list);
        $all_lists = "(" . implode(",", ($all_list)) . ")";
        $role_query = " AND Payments.Added_By IN $all_lists";
      }
    }
    $_SESSION['filterByUser'] = $role_query;
  } elseif ($by == 'date') {
    $startDate = $page == 1 ? date("Y-m-d 00:00:00", strtotime($_POST['startDate'])) : date("Y-m-d", strtotime($_POST['startDate']));
    $endDate = $page == 1 ? date("Y-m-d 23:59:59", strtotime($_POST['endDate'])) : date("Y-m-d", strtotime($_POST['endDate']));

    $_SESSION['filterByDate'] = $page == 1 ? " AND Payments.Created_At BETWEEN '$startDate' AND '$endDate'" : " AND Payments.Transaction_Date BETWEEN '$startDate' AND '$endDate'";
  }

  echo json_encode(['status' => true]);
}
