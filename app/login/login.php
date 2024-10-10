<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (isset($_POST['username']) && isset($_POST['password'])) {
  require '../../includes/db-config.php';
  session_start();

  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  if (empty($username) || empty($password)) {
    echo json_encode(['status' => 403, 'message' => 'Fields cannot be empty!']);
    session_destroy();
    exit();
  }
  $check = $conn->query("SELECT * FROM Users WHERE Code = '$username' AND Password = AES_ENCRYPT('$password','60ZpqkOnqn0UQQ2MYTlJ') $logged_in_users $not_able_to_logged_in_users");
  if ($check->num_rows > 0) {
    $user_details = mysqli_fetch_assoc($check);
    if ($user_details['Status'] == 1) {
      foreach ($user_details as $key => $user_detail) {
        $_SESSION[$key] = $user_detail;
      }
      if ($_SESSION['Role'] == 'Center') {
        $all_universities = array();
        $counter = 1;
        $universities = $conn->query("SELECT Alloted_Center_To_Counsellor.University_ID, Universities.Is_B2C, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') AS Name, Universities.Has_Unique_Center, Universities.Has_Unique_StudentID, Universities.Has_LMS, Universities.Logo FROM Alloted_Center_To_Counsellor LEFT JOIN Universities ON Alloted_Center_To_Counsellor.University_ID = Universities.ID WHERE `Code` = " . $_SESSION['ID']);
        while ($dt = $universities->fetch_assoc()) {
          $all_universities[] = $dt['University_ID'];
          if ($counter == 1) {
            $_SESSION['university_id'] = $dt['University_ID'];
            $_SESSION['university_name'] = $dt['Name'];
            $_SESSION['unique_center'] = $dt['Has_Unique_Center'];
            $_SESSION['student_id'] = $dt['Has_Unique_StudentID'];
            $_SESSION['university_logo'] = $dt['Logo'];
            $_SESSION['has_lms'] = $dt['Has_LMS'];
            $_SESSION['crm'] = $dt['Is_B2C'];
          }
          $counter++;
        }
        $_SESSION['Alloted_Universities'] = array_filter(array_unique($all_universities));
      } elseif ($_SESSION['Role'] != 'Administrator') {
        $all_universities = array();
        $counter = 1;
        $universities = $conn->query("SELECT University_User.University_ID, Universities.Is_B2C, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') AS Name, Universities.Has_Unique_Center, Universities.Has_Unique_StudentID, Universities.Has_LMS, Universities.Logo FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID WHERE `User_ID` = " . $_SESSION['ID']);
        while ($dt = $universities->fetch_assoc()) {
          $all_universities[] = $dt['University_ID'];
          if ($counter == 1) {
            $_SESSION['university_id'] = $dt['University_ID'];
            $_SESSION['university_name'] = $dt['Name'];
            $_SESSION['unique_center'] = $dt['Has_Unique_Center'];
            $_SESSION['student_id'] = $dt['Has_Unique_StudentID'];
            $_SESSION['university_logo'] = $dt['Logo'];
            $_SESSION['has_lms'] = $dt['Has_LMS'];
            $_SESSION['crm'] = $dt['Is_B2C'];
          }
          $counter++;
        }
        $_SESSION['Alloted_Universities'] = array_filter(array_unique($all_universities));
      }

      if (!array_key_exists('university_id', $_SESSION) && !in_array($_SESSION['Role'], ['Administrator'])) {
        session_destroy();
        exit(json_encode(['status' => 400, 'message' => 'Please allot University!']));
      }

      // University Query
      $query = $_SESSION['Role'] != "Administrator" ? " AND University_ID = " . $_SESSION['university_id'] : "";
      $_SESSION['UniversityQuery'] = $query;

      $setting_names = $conn->query("SELECT * FROM Custom_User_Names");
      while ($sn = $setting_names->fetch_assoc()) {
        $_SESSION[$sn['Name']] = $sn['Rename_As_Singular'];
        $_SESSION[$sn['Name'] . '-Outer'] = $sn['Rename_As'];
      }


      // RolesQuery
      $role_query = " AND {{ table }}.{{ column }} = " . $_SESSION['ID'];

      if ($_SESSION['Role'] === 'Administrator' || $_SESSION['Role'] == 'Operations' || $_SESSION['Role'] == 'Accountant') {
        $role_query = " AND {{ table }}.{{ column }} IS NOT NULL";
      } elseif ($_SESSION['Role'] === 'University Head') {
        $center_list = array($_SESSION['ID']);
        $counsellor_list = array($_SESSION['ID']);
        $counsellors = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = " . $_SESSION['ID'] . " AND University_ID = " . $_SESSION['university_id']);
        while ($counsellor = $counsellors->fetch_assoc()) {
          $counsellor_list[] = $counsellor['User_ID'];
        }
        $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_Counsellor WHERE University_ID = " . $_SESSION['university_id'] . " AND Counsellor_ID IN (" . implode(",", $counsellor_list) . ")");
        if ($get_all_center->num_rows > 0) {
          while ($gac = $get_all_center->fetch_assoc()) {
            $center_list[] = $gac['Code'];
          }
          $center_lists = "(" . implode(",", $center_list) . ")";
          if ($_SESSION['unique_center'] == 1) {
            $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
          } else {
            $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
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
      } elseif ($_SESSION['Role'] == 'Counsellor') {
        $center_list = array($_SESSION['ID']);
        $sub_center_list = array();
        $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_Counsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Counsellor_ID = '" . $_SESSION['ID'] . "'");
        if ($get_all_center->num_rows > 0) {
          while ($gac = $get_all_center->fetch_assoc()) {
            $center_list[] = $gac['Code'];
          }
          $center_lists = "(" . implode(",", $center_list) . ")";
          if ($_SESSION['unique_center'] == 1) {
            $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
          } else {
            $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
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
      } elseif ($_SESSION['Role'] === 'Sub-Counsellor') {
        $center_list = array($_SESSION['ID']);
        $sub_center_list = array();
        $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_SubCounsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Sub_Counsellor_ID = '" . $_SESSION['ID'] . "'");
        if ($get_all_center->num_rows > 0) {
          while ($gac = $get_all_center->fetch_assoc()) {
            $center_list[] = $gac['Code'];
          }
          $center_lists = "('" . implode("','", ($center_list)) . "')";
          if ($_SESSION['unique_center'] == 1) {
            $role_query = " AND {{ table }}.{{ column }} IN $center_lists";
          } else {
            $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
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
      } elseif ($_SESSION['Role'] === 'Center') {
        $center_list[] = $_SESSION['ID'];
        $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = '" . $_SESSION['ID'] . "'");
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

      // Payment Gateway
      if ($_SESSION['Role'] != 'Administrator') {
        $gateway = $conn->query("SELECT * FROM Payment_Gateways WHERE University_ID = " . $_SESSION['university_id']);
        if ($gateway->num_rows > 0) {
          $gateway = $gateway->fetch_assoc();
          $_SESSION['gateway'] = $gateway['Type'];
          $_SESSION['access_key'] = $gateway['Access_Key'];
          $_SESSION['secret_key'] = $gateway['Secret_Key'];
        }
      }

      $_SESSION['RoleQuery'] = $role_query;

      echo json_encode(['status' => 200, 'message' => 'Welcome ' . $user_details['Name'], 'url' => '/admissions/applications']);
    } else {
      echo json_encode(['status' => 403, 'message' => 'Access denied! Please contact administrator.']);
      session_destroy();
    }
  } else {
    $university_ids = array();
    $has_lmses = $conn->query("SELECT ID FROM Universities WHERE Has_LMS = 1");
    while ($has_lms = $has_lmses->fetch_assoc()) {
      $university_ids[] = $has_lms['ID'];
    }

    if (empty($university_ids)) {
      echo json_encode(['status' => 400, 'message' => 'Invalid credentials!']);
      session_destroy();
      exit();
    }

    $student = $conn->query("SELECT Students.*, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Course_Types.Name as Course_Type, Admission_Sessions.Name as Admission_Session, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.Enrollment_No='$username' AND Students.Enrollment_No = '$password' AND Students.Step = 4 AND Students.Status = 1 AND Students.University_ID IN (" . implode(",", $university_ids) . ")");
    //$student = $conn->query("SELECT Students.*, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Course_Types.Name as Course_Type, Admission_Sessions.Name as Admission_Session, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.Enrollment_No LIKE '$username' AND UPPER(DATE_FORMAT(Students.DOB, '%d%b%Y')) = '$password' AND Students.Step = 4 AND Students.Status = 1 AND Students.University_ID IN (" . implode(",", $university_ids) . ")");
    
      
    if ($student->num_rows > 0) {
      $student = mysqli_fetch_assoc($student);
      $_SESSION['Role'] = 'Student';
      foreach ($student as $key => $user_detail) {
        $_SESSION[$key] = $user_detail;
      }

      $all_universities = array();
      $counter = 1;
      $universities = $conn->query("SELECT ID as University_ID, Universities.Is_B2C, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') AS Name, Universities.Has_Unique_Center, Universities.Has_Unique_StudentID, Universities.Has_LMS, Universities.Logo FROM Universities WHERE ID = " . $_SESSION['University_ID'] . "");
      while ($dt = $universities->fetch_assoc()) {
        $all_universities[] = $dt['University_ID'];
        if ($counter == 1) {
          $_SESSION['university_id'] = $dt['University_ID'];
          $_SESSION['university_name'] = $dt['Name'];
          $_SESSION['unique_center'] = $dt['Has_Unique_Center'];
          $_SESSION['university_logo'] = $dt['Logo'];
          $_SESSION['student_id'] = $dt['Has_Unique_StudentID'];
          $_SESSION['has_lms'] = $dt['Has_LMS'];
          $_SESSION['crm'] = $dt['Is_B2C'];
        }
        $counter++;
      }
      $_SESSION['Alloted_Universities'] = array_filter(array_unique($all_universities));
      $student_name = array($student['First_Name'], $student['Middle_Name'], $student['Last_Name']);
      $_SESSION['Name'] = implode(' ', $student_name);

      $allowed = array();
      $pages = $conn->query("SELECT Pages.Name FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID AND Page_Access.University_ID = " . $_SESSION['university_id'] . " WHERE Student = 1 AND Pages.Type = 'LMS'");
      while ($page = $pages->fetch_assoc()) {
        $allowed[] = $page['Name'];
      }
      $_SESSION['LMS_Permissions'] = $allowed;

      $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = " . $_SESSION['ID'] . " AND Type = 'Photo'");
      if ($photo->num_rows > 0) {
        $photo = $photo->fetch_assoc();
        $_SESSION['Photo'] = $photo['Location'];
      }

      echo json_encode(['status' => 200, 'message' => 'Welcome ' . implode(" ", $student_name), 'url' => '/dashboard']);
    } else {
      echo json_encode(['status' => 400, 'message' => 'Invalid credentials!']);
      session_destroy();
    }
  }
} else {
  echo json_encode(['status' => 403, 'message' => 'Forbidden']);
  session_destroy();
}
