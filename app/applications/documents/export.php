<?php
//ini_set('display_errors', 1);
ini_set('memory_limit', '256M');
use setasign\Fpdi\Fpdi;

if (isset($_POST['download'])) {

  require_once('../../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../../extras/vendor/setasign/fpdi/src/autoload.php');
  require '../../../includes/db-config.php';

  function Zip($source, $destination)
  {
    if (!extension_loaded('zip') || !file_exists($source)) {
      return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
      return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) {
      $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

      foreach ($files as $file) {
        $file = str_replace('\\', '/', $file);

        // Ignore "." and ".." folders
        if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
          continue;

        $file = realpath($file);

        if (is_dir($file) === true) {
          $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
        } else if (is_file($file) === true) {
          $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
        }
      }
    } else if (is_file($source) === true) {
      $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
  }

  function rrmdir($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . "/" . $object) == "dir")
            rrmdir($dir . "/" . $object);
          else unlink($dir . "/" . $object);
        }
      }
      reset($objects);
      rmdir($dir);
    }
  }

  $documentTypes = is_array($_POST['download']) ? $_POST['download'] : array();
  $file_extensions = array('.png', '.jpg', '.jpeg');  //array of extensions to recreate 
  session_start();

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

  ## Search 
  $searchValue = isset($_GET['searchValue']) ? $_GET['searchValue'] : "";
  $searchQuery = " ";
  if ($searchValue != '') {
    if (!empty(strpos($searchValue, "="))) {
      $search = explode("=", $searchValue);
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
      $searchQuery = " AND (RIGHT(CONCAT('000000', Students.ID), 6) like '%" . $searchValue . "%' OR Students.ID like '%" . $searchValue . "%' OR Students.Unique_ID like '%" . $searchValue . "%' OR Students.First_Name like '%" . $searchValue . "%' OR Students.Middle_Name like '%" . $searchValue . "%' OR Students.Last_Name like '%" . $searchValue . "%' OR Admission_Sessions.Name like '%" . $searchValue . "%' OR Admission_Types.Name like '%" . $searchValue . "%' OR Students.Step like '%" . $searchValue . "%' OR Students.Father_Name like '%" . $searchValue . "%' OR Students.Email like '%" . $searchValue . "%' OR Students.Contact like '%" . $searchValue . "%' OR Sub_Courses.Short_Name like '%" . $searchValue . "%' OR Students.Enrollment_No like '%" . $searchValue . "%' OR Students.OA_Number like '%" . $searchValue . "%')";
    }
  }

  $filterByDepartment = "";
  if (isset($_SESSION['filterByDepartment'])) {
    $filterByDepartment = $_SESSION['filterByDepartment'];
  }

  $filterBySubCourse = "";
  if (isset($_SESSION['filterBySubCourses'])) {
    $filterBySubCourse = $_SESSION['filterBySubCourses'];
  }

  $filterByStatus = "";
  if (isset($_SESSION['filterByStatus'])) {
    $filterByStatus = $_SESSION['filterByStatus'];
  }

  $filterQueryUser = "";
  if (isset($_SESSION['filterByUser'])) {
    $filterQueryUser = $_SESSION['filterByUser'];
  }

  $filterByDate = "";
  if (isset($_SESSION['filterByDate'])) {
    $filterByDate = $_SESSION['filterByDate'];
  }

  $searchQuery .= $filterByDepartment . $filterQueryUser . $filterByDate . $filterBySubCourse . $filterByStatus;

  $path = $_SESSION['ID'];
  rrmdir($path);
  if (!file_exists($path)) {
    mkdir($path, 0777, true);
  }

  $documentTypes = "'" . implode("','", $documentTypes) . "'";

  $students = $conn->query("SELECT Students.ID, Unique_ID FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $role_query $searchQuery $session_query");
  while ($student = $students->fetch_assoc()) {
    $fileName = !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['ID'];
    $student_id = !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['ID'];
    $documents = $conn->query("SELECT Location, `Type` FROM Student_Documents WHERE Student_ID = " . $student['ID'] . " AND Type IN ($documentTypes)");
    if (isset($_POST['pdf'])) {
      $pdf = new Fpdi();

      while ($document = $documents->fetch_assoc()) {
        $column = $document['Type'];
        $files = explode("|", $document['Location']);
        foreach ($files as $file) {
          $file = "../../.." . $file;
          list($file_width, $file_height) = getimagesize($file);

          $encoded_file = base64_encode(file_get_contents($file));

          // Recreate
          $i = 0;
          $end = 3;
          $new_file = $student_id . $column . uniqid();
          while ($i < $end) {
            $decoded_file = base64_decode($encoded_file);
            $file_with_extension[] = $new_file . $file_extensions[$i];
            file_put_contents($path . '/' . $new_file . $file_extensions[$i], $decoded_file);
            $i++;
          }

          $width = ($file_width / 2.02) > 200 ? 200 : $file_width / 2.02;
          $height = ($file_height / 2.02) > 270 ? 270 : $file_height / 2.02;

          if ($column == 'Photo') {
            $width = 35;
            $height = 45;
          } elseif (in_array($column, ['Student Signature', 'Parent Signature'])) {
            $width = 35;
            $height = 15;
          }

          // PDF Configuration
          $pdf->SetMargins(5, 5, 5, 5);
          $pdf->AddPage('P', 'A4');

          // Apply on PDF
          try {
            $filename = $path . '/' . $new_file . $file_extensions[0];
            $pdf->Image($filename, 5, 5, $width, $height);
          } catch (Exception $e) {
            try {
              $filename = $path . '/' . $new_file . $file_extensions[1];
              $pdf->Image($filename, 5, 5, $width, $height);
            } catch (Exception $e) {
              try {
                $filename = $path . '/' . $new_file . $file_extensions[2];
                $pdf->Image($filename, 5, 5, $width, $height);
              } catch (Exception $e) {
              }
            }
          }

          if (in_array($column, ['Student Signature', 'Parent Signature'])) {
            $pdf->Rect(5, 5, $width, $height, 'D');
          }

          foreach ($file_with_extension as $file_ext) {
            if (file_exists($path . '/' . $file_ext)) {
              unlink($path . '/' . $file_ext);
            }
          }
          $file_with_extension = array();
        }
      }
      $pdf->output('F', $_SESSION['ID'] . '/' . $fileName . ".pdf", true);
    } elseif (isset($_POST['zip'])) {
      mkdir($path . '/' . $student_id, 0777, true);
      $destination = $path . '/' . $student_id . '/';
      while ($document = $documents->fetch_assoc()) {
        $files = explode("|", $document['Location']);
        foreach ($files as $file) {
          if (file_exists("../../.." . $file)) {
            $file_name = explode("/", $file);
            $file_name = end($file_name);
            $file_name = str_replace($student['ID'], $student_id, $file_name);

            $source = "../../.." . $file;
            $copyTo = $destination . $file_name;

            copy($source, $copyTo);
          }
        }
      }
    }
  }

  $archive_file_name = $_SESSION['ID'] . "_Documents_" . date("d_m_Y_H_i_s") . ".zip";
  Zip($path, $archive_file_name);
  header("Content-type: application/zip");
  header("Content-Disposition: attachment; filename=$archive_file_name");
  header("Content-length: " . filesize($archive_file_name));
  header("Pragma: no-cache");
  header("Expires: 0");
  readfile("$archive_file_name");
  unlink($archive_file_name);
}
