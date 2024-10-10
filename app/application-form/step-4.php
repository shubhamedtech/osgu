<?php
if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $inserted_id = intval($_POST['inserted_id']);

  $step = $conn->query("SELECT Step FROM Students WHERE ID = $inserted_id");
  $step = mysqli_fetch_array($step);
  $step = $step['Step'];

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG");
  $photo_folder = '../../uploads/photo/';
  $aadhar_folder = '../../uploads/aadhar/';
  $signature_folder = '../../uploads/signature/';
  $migration_folder = '../../uploads/migration/';
  $affidavit_folder = '../../uploads/affidavit/';
  $other_certificate_folder = '../../uploads/other_certificates/';

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

  if ($update) {
    $id = base64_encode('W1Ebt1IhGN3ZOLplom9I' . $inserted_id);
    if ($step < 4) {
      $conn->query("UPDATE Students SET Step = 4 WHERE ID = $inserted_id");
    }
    echo json_encode(['status' => 200, 'message' => 'Step 4 details saved successfully!', 'print_id' => $id]);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
