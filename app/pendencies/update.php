<?php
if (isset($_POST['id'])) {
  session_start();
  require '../../includes/db-config.php';

  $inserted_id = intval($_POST['id']);

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG");
  $photo_folder = '../../uploads/photo/';
  $aadhar_folder = '../../uploads/aadhar/';
  $signature_folder = '../../uploads/signature/';
  $migration_folder = '../../uploads/migration/';
  $affidavit_folder = '../../uploads/affidavit/';
  $other_certificate_folder = '../../uploads/other_certificates/';
  $high_academics_folder = '../../uploads/marksheet/high_school/';
  $inter_academics_folder = '../../uploads/marksheet/intermediate/';
  $ug_academics_folder = '../../uploads/marksheet/under_graduate/';
  $pg_academics_folder = '../../uploads/marksheet/post_graduate/';
  $other_academics_folder = '../../uploads/marksheet/other/';

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  // Photo
  if (isset($_FILES["photo"]['tmp_name']) && $_FILES["photo"]['tmp_name'] != '') {
    $photo = mysqli_real_escape_string($conn, $_FILES["photo"]['name']);
    $tmp_name = $_FILES["photo"]["tmp_name"];
    $photo_extension = pathinfo($photo, PATHINFO_EXTENSION);
    $photo = $inserted_id . "." . $photo_extension;
    if (in_array($photo_extension, $allowed_file_extensions)) {
      if (!move_uploaded_file($tmp_name, $photo_folder . $photo)) {
        echo json_encode(['status' => 503, 'message' => 'Unable to upload photo!']);
        exit();
      } else {
        $photo = str_replace('../..', '', $photo_folder) . $photo;
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Photo'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$photo' WHERE Student_ID = $inserted_id AND Type = 'Photo'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Photo', '$photo')");
        }
      }
    } else {
      echo json_encode(['status' => 302, 'message' => 'Photo should be image!']);
      exit();
    }
  } else {
    $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Photo'");
    if ($check->num_rows == 0) {
      echo json_encode(['status' => 400, 'message' => 'Photo is required!']);
      exit();
    } else {
      $update = true;
    }
  }

  // Aadhar
  if (isset($_FILES["aadhar"]["tmp_name"]) && $_FILES["aadhar"]['tmp_name'] != '' && count(array_filter($_FILES["aadhar"]['tmp_name'])) > 0) {
    foreach ($_FILES["aadhar"]["tmp_name"] as $key => $tmp_name) {
      $aadhar = mysqli_real_escape_string($conn, $_FILES["aadhar"]["name"][$key]);
      $tmp_name = $_FILES["aadhar"]["tmp_name"][$key];
      $aadhar_extension = pathinfo($aadhar, PATHINFO_EXTENSION);
      $aadhar_name = $inserted_id . "_Aadhar_" . $key . "." . $aadhar_extension;
      if (in_array($aadhar_extension, $allowed_file_extensions)) {
        if (file_exists($aadhar_folder . $aadhar_name)) {
          unlink($aadhar_folder . $aadhar_name);
        }
        if (move_uploaded_file($tmp_name, $aadhar_folder . $aadhar_name)) {
          $aadhars[] = str_replace('../..', '', $aadhar_folder) . $aadhar_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload Aadhar!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Aadhar should be image!']);
        exit();
      }
    }
    $aadhar = implode("|", $aadhars);
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Aadhar'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$aadhar' WHERE Student_ID = $inserted_id AND Type = 'Aadhar'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Aadhar', '$aadhar')");
    }
  }

  // Student's Signature
  if (isset($_FILES["student_signature"]['tmp_name']) && $_FILES["student_signature"]['tmp_name'] != '') {
    $student_signature = mysqli_real_escape_string($conn, $_FILES["student_signature"]['name']);
    $tmp_name = $_FILES["student_signature"]["tmp_name"];
    $student_signature_extension = pathinfo($student_signature, PATHINFO_EXTENSION);
    $student_signature = $inserted_id . "_Student_Signature." . $student_signature_extension;
    if (in_array($student_signature_extension, $allowed_file_extensions)) {
      if (!move_uploaded_file($tmp_name, $signature_folder . $student_signature)) {
        echo json_encode(['status' => 503, 'message' => 'Unable to upload Student Signature!']);
        exit();
      } else {
        $student_signature = str_replace('../..', '', $signature_folder) . $student_signature;
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Student Signature'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$student_signature' WHERE Student_ID = $inserted_id AND Type = 'Student Signature'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Student Signature', '$student_signature')");
        }
      }
    } else {
      echo json_encode(['status' => 302, 'message' => 'Student Signature should be image!']);
      exit();
    }
  }

  // Parent's Signature
  if (isset($_FILES["parent_signature"]['tmp_name']) && $_FILES["parent_signature"]['tmp_name'] != '') {
    $parent_signature = mysqli_real_escape_string($conn, $_FILES["parent_signature"]['name']);
    $tmp_name = $_FILES["parent_signature"]["tmp_name"];
    $parent_signature_extension = pathinfo($parent_signature, PATHINFO_EXTENSION);
    $parent_signature = $inserted_id . "_Parent_Signature." . $parent_signature_extension;
    if (in_array($parent_signature_extension, $allowed_file_extensions)) {
      if (!move_uploaded_file($tmp_name, $signature_folder . $parent_signature)) {
        echo json_encode(['status' => 503, 'message' => 'Unable to upload Parent Signature!']);
        exit();
      } else {
        $parent_signature = str_replace('../..', '', $signature_folder) . $parent_signature;
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Parent Signature'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$parent_signature' WHERE Student_ID = $inserted_id AND Type = 'Parent Signature'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Parent Signature', '$parent_signature')");
        }
      }
    } else {
      echo json_encode(['status' => 302, 'message' => 'Parent Signature should be image!']);
      exit();
    }
  }

  // Migration
  if (isset($_FILES["migration"]["tmp_name"]) && $_FILES["migration"]['tmp_name'] != '' && count(array_filter($_FILES["migration"]['tmp_name'])) > 0) {
    foreach ($_FILES["migration"]["tmp_name"] as $key => $tmp_name) {
      $migration = mysqli_real_escape_string($conn, $_FILES["migration"]["name"][$key]);
      $tmp_name = $_FILES["migration"]["tmp_name"][$key];
      $migration_extension = pathinfo($migration, PATHINFO_EXTENSION);
      $migration_name = $inserted_id . "_Migration_" . $key . "." . $migration_extension;
      if (in_array($migration_extension, $allowed_file_extensions)) {
        if (file_exists($migration_folder . $migration_name)) {
          unlink($migration_folder . $migration_name);
        }
        if (move_uploaded_file($tmp_name, $migration_folder . $migration_name)) {
          $migrations[] = str_replace('../..', '', $migration_folder) . $migration_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload Migration!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Migration should be image!']);
        exit();
      }
    }
    $migration = implode("|", $migrations);
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Migration'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$migration' WHERE Student_ID = $inserted_id AND Type = 'Migration'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Migration', '$migration')");
    }
  }

  // Affidavit
  if (isset($_FILES["affidavit"]["tmp_name"]) && $_FILES["affidavit"]['tmp_name'] != '' && count(array_filter($_FILES["affidavit"]['tmp_name'])) > 0) {
    foreach ($_FILES["affidavit"]["tmp_name"] as $key => $tmp_name) {
      $affidavit = mysqli_real_escape_string($conn, $_FILES["affidavit"]["name"][$key]);
      $tmp_name = $_FILES["affidavit"]["tmp_name"][$key];
      $affidavit_extension = pathinfo($affidavit, PATHINFO_EXTENSION);
      $affidavit_name = $inserted_id . "_Affidavit_" . $key . "." . $affidavit_extension;
      if (in_array($affidavit_extension, $allowed_file_extensions)) {
        if (file_exists($affidavit_folder . $affidavit_name)) {
          unlink($affidavit_folder . $affidavit_name);
        }
        if (move_uploaded_file($tmp_name, $affidavit_folder . $affidavit_name)) {
          $affidavits[] = str_replace('../..', '', $affidavit_folder) . $affidavit_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload affidavit!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Affidavit should be image!']);
        exit();
      }
    }
    $affidavit = implode("|", $affidavits);
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Affidavit'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$affidavit' WHERE Student_ID = $inserted_id AND Type = 'Affidavit'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Affidavit', '$affidavit')");
    }
  }

  // Other Certificates
  if (isset($_FILES["other_certificate"]["tmp_name"]) && $_FILES["other_certificate"]['tmp_name'] != '' && count(array_filter($_FILES["other_certificate"]['tmp_name'])) > 0) {
    foreach ($_FILES["other_certificate"]["tmp_name"] as $key => $tmp_name) {
      $other_certificate = mysqli_real_escape_string($conn, $_FILES["other_certificate"]["name"][$key]);
      $tmp_name = $_FILES["other_certificate"]["tmp_name"][$key];
      $other_certificate_extension = pathinfo($other_certificate, PATHINFO_EXTENSION);
      $other_certificate_name = $inserted_id . "_other_certificate_" . $key . "." . $other_certificate_extension;
      if (in_array($other_certificate_extension, $allowed_file_extensions)) {
        if (file_exists($other_certificate_folder . $other_certificate_name)) {
          unlink($other_certificate_folder . $other_certificate_name);
        }
        if (move_uploaded_file($tmp_name, $other_certificate_folder . $other_certificate_name)) {
          $other_certificates[] = str_replace('../..', '', $other_certificate_folder) . $other_certificate_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload other_certificate!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'other_certificate should be image!']);
        exit();
      }
    }
    $other_certificate = implode("|", $other_certificates);
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other Certificate'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$other_certificate' WHERE Student_ID = $inserted_id AND Type = 'Other Certificate'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Other Certificate', '$other_certificate')");
    }
  }

  // High School
  if (isset($_FILES["high_school_marksheet"]["tmp_name"]) && $_FILES["high_school_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["high_school_marksheet"]['tmp_name'])) > 0) {
    foreach ($_FILES["high_school_marksheet"]["tmp_name"] as $key => $tmp_name) {
      $high_marksheet = mysqli_real_escape_string($conn, $_FILES["high_school_marksheet"]["name"][$key]);
      $tmp_name = $_FILES["high_school_marksheet"]["tmp_name"][$key];
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
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'High School'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$high_marksheet' WHERE Student_ID = $inserted_id AND Type = 'High School'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'High School', '$high_marksheet')");
    }
  }

  // Intermediate
  if (isset($_FILES["intermediate_marksheet"]["tmp_name"]) && $_FILES["intermediate_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["intermediate_marksheet"]['tmp_name'])) > 0) {
    foreach ($_FILES["intermediate_marksheet"]["tmp_name"] as $key => $tmp_name) {
      $inter_marksheet = mysqli_real_escape_string($conn, $_FILES["intermediate_marksheet"]["name"][$key]);
      $tmp_name = $_FILES["intermediate_marksheet"]["tmp_name"][$key];
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
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$inter_marksheet' WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Intermediate', '$inter_marksheet')");
    }
  }

  // UG
  if (isset($_FILES["graduation_marksheet"]["tmp_name"]) && $_FILES["graduation_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["graduation_marksheet"]['tmp_name'])) > 0) {
    foreach ($_FILES["graduation_marksheet"]["tmp_name"] as $key => $tmp_name) {
      $ug_marksheet = mysqli_real_escape_string($conn, $_FILES["graduation_marksheet"]["name"][$key]);
      $tmp_name = $_FILES["graduation_marksheet"]["tmp_name"][$key];
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
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'UG'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$ug_marksheet' WHERE Student_ID = $inserted_id AND Type = 'UG'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'UG', '$ug_marksheet')");
    }
  }

  // PG
  if (isset($_FILES["post_graduation_marksheet"]["tmp_name"]) && $_FILES["post_graduation_marksheet"]['tmp_name'] != '' && count(array_filter($_FILES["post_graduation_marksheet"]['tmp_name'])) > 0) {
    foreach ($_FILES["post_graduation_marksheet"]["tmp_name"] as $key => $tmp_name) {
      $pg_marksheet = mysqli_real_escape_string($conn, $_FILES["post_graduation_marksheet"]["name"][$key]);
      $tmp_name = $_FILES["post_graduation_marksheet"]["tmp_name"][$key];
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
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'PG'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$pg_marksheet' WHERE Student_ID = $inserted_id AND Type = 'PG'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'PG', '$pg_marksheet')");
    }
  }

  // Other
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
    $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other'");
    if ($check->num_rows > 0) {
      $update = $conn->query("UPDATE Student_Documents SET Location = '$other_marksheet' WHERE Student_ID = $inserted_id AND Type = 'Other'");
    } else {
      $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Other', '$other_marksheet')");
    }
  }



  if ($update) {
    $conn->query("UPDATE Student_Pendencies SET Status = 2, Approved_By = " . $_SESSION['ID'] . " WHERE Student_ID = $inserted_id AND Status = 0");
    echo json_encode(['status' => 200, 'message' => 'Documents updated successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
