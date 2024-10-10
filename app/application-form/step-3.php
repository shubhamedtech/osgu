<?php
if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG");
  $high_academics_folder = '../../uploads/marksheet/high_school/';
  $inter_academics_folder = '../../uploads/marksheet/intermediate/';
  $ug_academics_folder = '../../uploads/marksheet/under_graduate/';
  $pg_academics_folder = '../../uploads/marksheet/post_graduate/';
  $other_academics_folder = '../../uploads/marksheet/other/';

  $inserted_id = intval($_POST['inserted_id']);

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  $step = $conn->query("SELECT Step, Sub_Courses.Eligibility FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.ID = $inserted_id");
  $step = mysqli_fetch_array($step);
  $eligibility = $step['Eligibility'];
  $step = $step['Step'];

  $eligibility = !empty($eligibility) ? json_decode($eligibility, true) : [];

  if(count($eligibility)==0){
    exit(json_encode(['status' => 400, 'message' => 'Please configure eligibility criteria.']));
  }

  
  $high_subject = mysqli_real_escape_string($conn, $_POST['high_subject']);
  $high_subject = strtoupper(strtolower($high_subject));
  $high_year = mysqli_real_escape_string($conn, $_POST['high_year']);
  $high_board = mysqli_real_escape_string($conn, $_POST['high_board']);
  $high_board = strtoupper(strtolower($high_board));
  $high_obtained = array_key_exists('high_obtained', $_POST) ? mysqli_real_escape_string($conn, $_POST['high_obtained']) : NULL;
  $high_max = array_key_exists('high_max', $_POST) ? mysqli_real_escape_string($conn, $_POST['high_max']) : NULL;
  $high_total = mysqli_real_escape_string($conn, $_POST['high_total']);
  $high_total = strtoupper(strtolower($high_total));

  if(in_array('High School', $eligibility) && empty($high_total)){
    echo json_encode(['status' => 400, 'message' => 'High School details are required!']);
    exit();
  }

  if (!empty($high_total)) {
    if (isset($_FILES["high_marksheet"]["tmp_name"]) && $_FILES["high_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["high_marksheet"]['tmp_name'])) > 0) {
      foreach ($_FILES["high_marksheet"]["tmp_name"] as $key => $tmp_name) {
        $high_marksheet = mysqli_real_escape_string($conn, $_FILES["high_marksheet"]["name"][$key]);
        $tmp_name = $_FILES["high_marksheet"]["tmp_name"][$key];
        $high_marksheet_extension = pathinfo($high_marksheet, PATHINFO_EXTENSION);
        $high_marksheet_name = $inserted_id . "_High_Marksheet_" . $key . "." . $high_marksheet_extension;
        if (in_array($high_marksheet_extension, $allowed_file_extensions)) {
          if (file_exists($high_academics_folder . $high_marksheet_name)) {
            unlink($high_academics_folder . $high_marksheet_name);
          }
          if (move_uploaded_file($tmp_name, $high_academics_folder . $high_marksheet_name)) {
            $high_marksheets[] = str_replace('../..', '', $high_academics_folder) . $high_marksheet_name;
          } else {
            echo json_encode(['status' => 503, 'message' => 'Unable to upload High School marksheet!']);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => 'High School Marksheet should be image!']);
          exit();
        }
      }
      $high_marksheet = implode("|", $high_marksheets);
    } else {
      $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'High School'");
      if ($check->num_rows == 0) {
        echo json_encode(['status' => 400, 'message' => 'High School Marksheet is required!']);
        exit();
      } else {
        $high_marksheet = mysqli_fetch_assoc($check);
        $high_marksheet = $high_marksheet['Location'];
      }
    }

    $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'High School'");
    if ($check->num_rows > 0) {
      $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$high_year', Subject = '$high_subject', `Board/Institute` = '$high_board', `Marks_Obtained` = '$high_obtained', `Max_Marks` = '$high_max', `Total_Marks` = '$high_total' WHERE Student_ID = $inserted_id AND Type = 'High School'");
    } else {
      $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`) VALUES ($inserted_id, 'High School', '$high_year', '$high_subject', '$high_board', '$high_obtained', '$high_max', '$high_total')");
    }
    if (!$update_details) {
      echo json_encode(['status' => 400, 'message' => 'Unable to update high school details.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'High School'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$high_marksheet' WHERE Student_ID = $inserted_id AND Type = 'High School'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'High School', '$high_marksheet')");
    }
  }

  $inter_subject = mysqli_real_escape_string($conn, $_POST['inter_subject']);
  $inter_subject = strtoupper(strtolower($inter_subject));
  $inter_year = mysqli_real_escape_string($conn, $_POST['inter_year']);
  $inter_board = mysqli_real_escape_string($conn, $_POST['inter_board']);
  $inter_board = strtoupper(strtolower($inter_board));
  $inter_obtained = array_key_exists('inter_obtained', $_POST) ? mysqli_real_escape_string($conn, $_POST['inter_obtained']) : NULL;
  $inter_max = array_key_exists('inter_max', $_POST) ? mysqli_real_escape_string($conn, $_POST['inter_max']) : NULL;
  $inter_total = mysqli_real_escape_string($conn, $_POST['inter_total']);
  $inter_total = strtoupper(strtolower($inter_total));

  if (in_array('Intermediate', $eligibility) && empty($inter_total)) {
    echo json_encode(['status' => 400, 'message' => 'Intermediate details are required!']);
    exit();
  }

  if (!empty($inter_total)) {
    if (isset($_FILES["inter_marksheet"]["tmp_name"]) && $_FILES["inter_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["inter_marksheet"]['tmp_name'])) > 0) {
      foreach ($_FILES["inter_marksheet"]["tmp_name"] as $key => $tmp_name) {
        $inter_marksheet = mysqli_real_escape_string($conn, $_FILES["inter_marksheet"]["name"][$key]);
        $tmp_name = $_FILES["inter_marksheet"]["tmp_name"][$key];
        $inter_marksheet_extension = pathinfo($inter_marksheet, PATHINFO_EXTENSION);
        $inter_marksheet_name = $inserted_id . "_Inter_Marksheet_" . $key . "." . $inter_marksheet_extension;
        if (in_array($inter_marksheet_extension, $allowed_file_extensions)) {
          if (file_exists($inter_academics_folder . $inter_marksheet_name)) {
            unlink($inter_academics_folder . $inter_marksheet_name);
          }
          if (move_uploaded_file($tmp_name, $inter_academics_folder . $inter_marksheet_name)) {
            $inter_marksheets[] = str_replace('../..', '', $inter_academics_folder) . $inter_marksheet_name;
          } else {
            echo json_encode(['status' => 503, 'message' => 'Unable to upload Intermediate marksheet!']);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => 'Intermediate Marksheet should be image!']);
          exit();
        }
      }
      $inter_marksheet = implode("|", $inter_marksheets);
    } else {
      $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
      if ($check->num_rows == 0) {
        echo json_encode(['status' => 400, 'message' => 'Intermediate Marksheet is required!']);
        exit();
      } else {
        $inter_marksheet = mysqli_fetch_assoc($check);
        $inter_marksheet = $inter_marksheet['Location'];
      }
    }

    $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
    if ($check->num_rows > 0) {
      $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$inter_year', Subject = '$inter_subject', `Board/Institute` = '$inter_board', `Marks_Obtained` = '$inter_obtained', `Max_Marks` = '$inter_max', `Total_Marks` = '$inter_total' WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
    } else {
      $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`) VALUES ($inserted_id, 'Intermediate', '$inter_year', '$inter_subject', '$inter_board', '$inter_obtained', '$inter_max', '$inter_total')");
    }
    if (!$update_details) {
      echo json_encode(['status' => 400, 'message' => 'Unable to update Intermediate details.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$inter_marksheet' WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Intermediate', '$inter_marksheet')");
    }
  }

  $ug_subject = mysqli_real_escape_string($conn, $_POST['ug_subject']);
  $ug_subject = strtoupper(strtolower($ug_subject));
  $ug_year = mysqli_real_escape_string($conn, $_POST['ug_year']);
  $ug_board = mysqli_real_escape_string($conn, $_POST['ug_board']);
  $ug_board = strtoupper(strtolower($ug_board));
  $ug_obtained = array_key_exists('ug_obtained', $_POST) ? mysqli_real_escape_string($conn, $_POST['ug_obtained']) : NULL;
  $ug_max = array_key_exists('ug_max', $_POST) ? mysqli_real_escape_string($conn, $_POST['ug_max']) : NULL;
  $ug_total = mysqli_real_escape_string($conn, $_POST['ug_total']);
  $ug_total = strtoupper(strtolower($ug_total));

  if (in_array('UG', $eligibility) && empty($ug_total)) {
    echo json_encode(['status' => 400, 'message' => 'Graduation details are required!']);
    exit();
  }

  if (!empty($ug_total)) {
    if (isset($_FILES["ug_marksheet"]["tmp_name"]) && $_FILES["ug_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["ug_marksheet"]['tmp_name'])) > 0) {
      foreach ($_FILES["ug_marksheet"]["tmp_name"] as $key => $tmp_name) {
        $ug_marksheet = mysqli_real_escape_string($conn, $_FILES["ug_marksheet"]["name"][$key]);
        $tmp_name = $_FILES["ug_marksheet"]["tmp_name"][$key];
        $ug_marksheet_extension = pathinfo($ug_marksheet, PATHINFO_EXTENSION);
        $ug_marksheet_name = $inserted_id . "_UG_Marksheet_" . $key . "." . $ug_marksheet_extension;
        if (in_array($ug_marksheet_extension, $allowed_file_extensions)) {
          if (file_exists($ug_academics_folder . $ug_marksheet_name)) {
            unlink($ug_academics_folder . $ug_marksheet_name);
          }
          if (move_uploaded_file($tmp_name, $ug_academics_folder . $ug_marksheet_name)) {
            $ug_marksheets[] = str_replace('../..', '', $ug_academics_folder) . $ug_marksheet_name;
          } else {
            echo json_encode(['status' => 503, 'message' => 'Unable to upload UG marksheet!']);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => 'UG Marksheet should be image!']);
          exit();
        }
      }
      $ug_marksheet = implode("|", $ug_marksheets);
    } else {
      $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'UG'");
      if ($check->num_rows == 0) {
        echo json_encode(['status' => 400, 'message' => 'UG Marksheet is required!']);
        exit();
      } else {
        $ug_marksheet = mysqli_fetch_assoc($check);
        $ug_marksheet = $ug_marksheet['Location'];
      }
    }

    $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'UG'");
    if ($check->num_rows > 0) {
      $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$ug_year', Subject = '$ug_subject', `Board/Institute` = '$ug_board', `Marks_Obtained` = '$ug_obtained', `Max_Marks` = '$ug_max', `Total_Marks` = '$ug_total' WHERE Student_ID = $inserted_id AND Type = 'UG'");
    } else {
      $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`) VALUES ($inserted_id, 'UG', '$ug_year', '$ug_subject', '$ug_board', '$ug_obtained', '$ug_max', '$ug_total')");
    }
    if (!$update_details) {
      echo json_encode(['status' => 400, 'message' => 'Unable to update UG details.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'UG'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$ug_marksheet' WHERE Student_ID = $inserted_id AND Type = 'UG'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'UG', '$ug_marksheet')");
    }
  }

  $pg_subject = mysqli_real_escape_string($conn, $_POST['pg_subject']);
  $pg_subject = strtoupper(strtolower($pg_subject));
  $pg_year = mysqli_real_escape_string($conn, $_POST['pg_year']);
  $pg_board = mysqli_real_escape_string($conn, $_POST['pg_board']);
  $pg_board = strtoupper(strtolower($pg_board));
  $pg_obtained = array_key_exists('pg_obtained', $_POST) ? mysqli_real_escape_string($conn, $_POST['pg_obtained']) : NULL;
  $pg_max = array_key_exists('pg_max', $_POST) ? mysqli_real_escape_string($conn, $_POST['pg_max']) : NULL;
  $pg_total = mysqli_real_escape_string($conn, $_POST['pg_total']);
  $pg_total = strtoupper(strtolower($pg_total));

  if (in_array('PG', $eligibility) && empty($pg_total)) {
    echo json_encode(['status' => 400, 'message' => 'Post Graduate details are required!']);
    exit();
  }

  if (!empty($pg_total)) {
    if (isset($_FILES["pg_marksheet"]["tmp_name"]) && $_FILES["pg_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["pg_marksheet"]['tmp_name'])) > 0) {
      foreach ($_FILES["pg_marksheet"]["tmp_name"] as $key => $tmp_name) {
        $pg_marksheet = mysqli_real_escape_string($conn, $_FILES["pg_marksheet"]["name"][$key]);
        $tmp_name = $_FILES["pg_marksheet"]["tmp_name"][$key];
        $pg_marksheet_extension = pathinfo($pg_marksheet, PATHINFO_EXTENSION);
        $pg_marksheet_name = $inserted_id . "_PG_Marksheet_" . $key . "." . $pg_marksheet_extension;
        if (in_array($pg_marksheet_extension, $allowed_file_extensions)) {
          if (file_exists($pg_academics_folder . $pg_marksheet_name)) {
            unlink($pg_academics_folder . $pg_marksheet_name);
          }
          if (move_uploaded_file($tmp_name, $pg_academics_folder . $pg_marksheet_name)) {
            $pg_marksheets[] = str_replace('../..', '', $pg_academics_folder) . $pg_marksheet_name;
          } else {
            echo json_encode(['status' => 503, 'message' => 'Unable to upload PG marksheet!']);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => 'PG Marksheet should be image!']);
          exit();
        }
      }
      $pg_marksheet = implode("|", $pg_marksheets);
    } else {
      $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'PG'");
      if ($check->num_rows == 0) {
        echo json_encode(['status' => 400, 'message' => 'PG Marksheet is required!']);
        exit();
      } else {
        $pg_marksheet = mysqli_fetch_assoc($check);
        $pg_marksheet = $pg_marksheet['Location'];
      }
    }

    $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'PG'");
    if ($check->num_rows > 0) {
      $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$pg_year', Subject = '$pg_subject', `Board/Institute` = '$pg_board', `Marks_Obtained` = '$pg_obtained', `Max_Marks` = '$pg_max', `Total_Marks` = '$pg_total' WHERE Student_ID = $inserted_id AND Type = 'PG'");
    } else {
      $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`) VALUES ($inserted_id, 'PG', '$pg_year', '$pg_subject', '$pg_board', '$pg_obtained', '$pg_max', '$pg_total')");
    }
    if (!$update_details) {
      echo json_encode(['status' => 400, 'message' => 'Unable to update PG details.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'PG'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$pg_marksheet' WHERE Student_ID = $inserted_id AND Type = 'PG'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'PG', '$pg_marksheet')");
    }
  }

  $other_subject = mysqli_real_escape_string($conn, $_POST['other_subject']);
  $other_subject = strtoupper(strtolower($other_subject));
  $other_year = mysqli_real_escape_string($conn, $_POST['other_year']);
  $other_board = mysqli_real_escape_string($conn, $_POST['other_board']);
  $other_board = strtoupper(strtolower($other_board));
  $other_obtained = array_key_exists('other_obtained', $_POST) ? mysqli_real_escape_string($conn, $_POST['other_obtained']) : NULL;
  $other_max = array_key_exists('other_max', $_POST) ? mysqli_real_escape_string($conn, $_POST['other_max']) : NULL;
  $other_total = mysqli_real_escape_string($conn, $_POST['other_total']);
  $other_total = strtoupper(strtolower($other_total));

  if (in_array('Other', $eligibility) && empty($other_total)) {
    echo json_encode(['status' => 400, 'message' => 'Other details are required!']);
    exit();
  }

  if (!empty($other_total)) {
    if (isset($_FILES["other_marksheet"]["tmp_name"]) && $_FILES["other_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["other_marksheet"]['tmp_name'])) > 0) {
      foreach ($_FILES["other_marksheet"]["tmp_name"] as $key => $tmp_name) {
        $other_marksheet = mysqli_real_escape_string($conn, $_FILES["other_marksheet"]["name"][$key]);
        $tmp_name = $_FILES["other_marksheet"]["tmp_name"][$key];
        $other_marksheet_extension = pathinfo($other_marksheet, PATHINFO_EXTENSION);
        $other_marksheet_name = $inserted_id . "_other_Marksheet_" . $key . "." . $other_marksheet_extension;
        if (in_array($other_marksheet_extension, $allowed_file_extensions)) {
          if (file_exists($other_academics_folder . $other_marksheet_name)) {
            unlink($other_academics_folder . $other_marksheet_name);
          }
          if (move_uploaded_file($tmp_name, $other_academics_folder . $other_marksheet_name)) {
            $other_marksheets[] = str_replace('../..', '', $other_academics_folder) . $other_marksheet_name;
          } else {
            echo json_encode(['status' => 503, 'message' => 'Unable to upload Other marksheet!']);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => 'Other Marksheet should be image!']);
          exit();
        }
      }
      $other_marksheet = implode("|", $other_marksheets);
    } else {
      $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other'");
      if ($check->num_rows == 0) {
        echo json_encode(['status' => 400, 'message' => 'Other Marksheet is required!']);
        exit();
      } else {
        $other_marksheet = mysqli_fetch_assoc($check);
        $other_marksheet = $other_marksheet['Location'];
      }
    }

    $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'Other'");
    if ($check->num_rows > 0) {
      $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$other_year', Subject = '$other_subject', `Board/Institute` = '$other_board', `Marks_Obtained` = '$other_obtained', `Max_Marks` = '$other_max', `Total_Marks` = '$other_total' WHERE Student_ID = $inserted_id AND Type = 'Other'");
    } else {
      $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`) VALUES ($inserted_id, 'Other', '$other_year', '$other_subject', '$other_board', '$other_obtained', '$other_max', '$other_total')");
    }
    if (!$update_details) {
      echo json_encode(['status' => 400, 'message' => 'Unable to update Other details.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$other_marksheet' WHERE Student_ID = $inserted_id AND Type = 'Other'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Other', '$other_marksheet')");
    }
  }

  if ($update) {
    if ($step < 3) {
      $conn->query("UPDATE Students SET Step = 3 WHERE ID = $inserted_id");
    }
    echo json_encode(['status' => 200, 'message' => 'Step 3 details saved successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
