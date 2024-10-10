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
    $orderby = "ORDER BY Students.ID ASC";
  }

  $studentIds = array();
  $examSession = $conn->query("SELECT Admission_Session FROM Exam_Sessions WHERE ID = " . $_SESSION['active_rr_session_id']);
  $examSession = $examSession->fetch_assoc();
  $examSessions = json_decode($examSession['Admission_Session'], true);
  foreach ($examSessions as $value) {
    if($value['for']=='Re-Registrations'){
      $condition = "";
      if (!empty($value['admissionType'])) {
        $condition .= " AND Students.Admission_Type_ID = " . $value['admissionType'];
      }
    
      if ($value['table'] == 'Fresh') {
        $condition .= " AND Students.Admission_Session_ID = " . $value['session'] . " AND Students.Adm_Duration IN (" . implode(",", $value['duration']) . ")";
        $students = $conn->query("SELECT Students.ID FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND (Students.Adm_Duration+1) <= Sub_Courses.Min_Duration AND Enrollment_No IS NOT NULL $condition");
      } elseif ($value['table'] == 'Re-Reg') {
        $condition .= " AND Students.Admission_Session_ID = " . $value['session'] . " AND Re_Registrations.Duration IN (" . implode(",", $value['duration']) . ")";
        $students = $conn->query("SELECT Student_ID as ID FROM Re_Registrations LEFT JOIN Students ON Re_Registrations.Student_ID = Students.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Re_Registrations.University_ID = " . $_SESSION['university_id'] . " AND (Re_Registrations.Duration+1) <= Sub_Courses.Min_Duration $condition");
      }

      while ($student = $students->fetch_assoc()) {
        $studentIds[] = $student['ID'];
      }
    }
  }
  
  if (empty($studentIds)) {
    exit(json_encode(array(
      "draw" => 0,
      "iTotalRecords" => 0,
      "iTotalDisplayRecords" => 0,
      "aaData" => []
    )));
  }

  ## Search 
  $searchQuery = " ";
  if ($searchValue != '') {
    $searchQuery = " AND (RIGHT(CONCAT('000000', Students.ID), 6) like '%" . $searchValue . "%' OR Students.ID like '%" . $searchValue . "%' OR Students.Unique_ID like '%" . $searchValue . "%' OR Students.First_Name like '%" . $searchValue . "%' OR Students.Middle_Name like '%" . $searchValue . "%' OR Students.Last_Name like '%" . $searchValue . "%' OR Admission_Sessions.Name like '%" . $searchValue . "%' OR Sub_Courses.Short_Name like '%" . $searchValue . "%' OR Students.Enrollment_No like '%" . $searchValue . "%')";
  }

  $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
  $role_query = str_replace('{{ column }}', 'Added_For', $role_query);

  $exclude = " AND Students.ID NOT IN (SELECT Student_ID FROM Re_Registrations WHERE Exam_Session_ID = " . $_SESSION['active_rr_session_id'] . ")";

  ## Total number of records without filtering
  $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Students.ID IN (" . implode(",", $studentIds) . ") $role_query $exclude");
  $records = mysqli_fetch_assoc($all_count);
  $totalRecords = $records['allcount'];

  ## Total number of record with filtering
  $filter_count = $conn->query("SELECT COUNT(Students.ID) as filtered FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Students.ID IN (" . implode(",", $studentIds) . ") $searchQuery $role_query $exclude");
  $records = mysqli_fetch_assoc($filter_count);
  $totalRecordwithFilter = $records['filtered'];

  ## Fetch records
  $result_record = "SELECT Students.ID, (Students.Duration+1) as Duration, CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name) as First_Name, Students.Enrollment_No, Students.Unique_ID, Admission_Sessions.Name as Admission_Session_ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name,')') as Course_ID, Students.Added_For FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Students.ID IN (" . implode(",", $studentIds) . ") $role_query $searchQuery $exclude $orderby LIMIT " . $row . "," . $rowperpage;
  $empRecords = mysqli_query($conn, $result_record);
  $data = array();

  while ($row = mysqli_fetch_assoc($empRecords)) {

    // Added_For
    if ($_SESSION['Role'] == 'Center') {
      $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . "");
    } else {
      $user = $conn->query("SELECT ID, Code, Name FROM Users WHERE ID = " . $row['Added_For'] . " AND Role = 'Center'");
      if ($user->num_rows == 0) {
        $user = $conn->query("SELECT Users.ID, Code, Name FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Center WHERE `Sub_Center` = " . $row['Added_For']);
      }
    }

    $user = mysqli_fetch_array($user);

    // RM
    $rm['Name'] = "";
    if (!empty($user)) {
      // RM
      $rm = $conn->query("SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'] . " WHERE Alloted_Center_To_Counsellor.Code = " . $user['ID'] . " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id']);
      if ($rm->num_rows > 0) {
        $rm = mysqli_fetch_array($rm);
      } else {
        $rm = $user;
      }
    }

    $data[] = array(
      "ID" => $row['ID'],
      "First_Name" => $row["First_Name"],
      "Unique_ID" => $row["Unique_ID"],
      "Enrollment_No" => $row["Enrollment_No"],
      "Admission_Session_ID" => $row['Admission_Session_ID'],
      "Course_ID" => $row["Course_ID"],
      "Duration" => $row['Duration'],
      "Added_For" => $user['Name'] . ' (' . $user['Code'] . ')'
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
