<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $unique_id = $id;
  $student = $conn->query("SELECT Unique_ID FROM Students WHERE ID = $id");
  if ($student->num_rows > 0) {
    $unique_id = $student->fetch_assoc();
    $unique_id = $unique_id['Unique_ID'];
  }

  $documents = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id");

  $archive_file_name = $unique_id . ".zip";
  $zip_obj = new ZipArchive;
  if ($zip_obj->open($archive_file_name, ZipArchive::CREATE) === TRUE) {
    while ($document = $documents->fetch_assoc()) {
      $files = explode("|", $document['Location']);
      foreach ($files as $file) {
        if (file_exists("../.." . $file)) {
          $file_name = explode("/", $file);
          $file_name = end($file_name);
          $file_name = str_replace($id, $unique_id, $file_name);
          $zip_obj->addFile("../.." . $file, $file_name);
        }
      }
    }
    $zip_obj->close();
  }

  header("Content-type: application/zip");
  header("Content-Disposition: attachment; filename=$archive_file_name");
  header("Content-length: " . filesize($archive_file_name));
  header("Pragma: no-cache");
  header("Expires: 0");
  readfile("$archive_file_name");
  unlink($archive_file_name);
}
