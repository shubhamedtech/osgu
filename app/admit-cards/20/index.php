<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['id'])) {
  require '../../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $student = $conn->query("SELECT Students.ID, Students.Sub_Course_ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Father_Name, Students.Enrollment_No, Students.Duration, Students.Contact, Students.Unique_ID, Students.DOB, Students.Address, CONCAT(Courses.Short_Name, ' - ', Sub_Courses.Name) as Short_Name, Admission_Sessions.Name as Session FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.ID = $id AND Students.University_ID = " . $_SESSION['university_id'] . "");
  if ($student->num_rows == 0) {
    header('Location: /dashboard');
  }

  $student = $student->fetch_assoc();

  $file_extensions = array('.png', '.jpg', '.jpeg');

  $photo = "";
  $document = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = " . $student['ID'] . " AND `Type` = 'Photo'");
  if ($document->num_rows > 0) {
    $photo = $document->fetch_assoc();
    $photo = "../../.." . $photo['Location'];
  }
  $student_photo = base64_encode(file_get_contents($photo));
  $i = 0;
  $end = 3;
  while ($i < $end) {
    $data1 = base64_decode($student_photo);
    $filename1 = $student['ID'] . "_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
    file_put_contents($filename1, $data1); //we save our new images to the path above
    $i++;
  }

  require_once('../../../extras/qrcode/qrlib.php');
  require_once('../../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../../extras/vendor/setasign/fpdi/src/autoload.php');

  $pdf = new Fpdi();

  $pdf->SetTitle('Admit Card');

  $pageCount = $pdf->setSourceFile('admit-card.pdf');

  $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  $pdf->SetMargins(0, 0, 0);
  $pdf->SetAutoPageBreak(true, 1);

  $pdf->AddFont('Hondo', '', 'hondo.php');
  $pdf->SetFont('Hondo', '', 12);

  $pdf->SetXY(40, 45.5);
  $pdf->Write(1, $student['Enrollment_No']);

  $student_id = empty($student['Unique_ID']) ? $student['ID'] : $student['Unique_ID'];
  $pdf->SetXY(159, 45.5);
  $pdf->Write(1, $student_id);

  $student_name = array($student['First_Name'], $student['Middle_Name'], $student['Last_Name']);
  $student_name = array_filter($student_name);
  $pdf->SetXY(48, 57);
  $pdf->Write(1, ucwords(strtolower(implode(" ", $student_name))));

  $pdf->SetXY(48, 62.8);
  $pdf->Write(1, ucwords(strtolower($student['Father_Name'])));

  $pdf->SetXY(48, 68.3);
  $pdf->Write(1, $student['Short_Name']);

  $pdf->SetXY(48, 73.8);
  $pdf->Write(1, $student['Duration']);

  $pdf->SetXY(48, 79.3);
  $pdf->Write(1, $student['Session']);

  // Syllabus
  $pdf->SetFont('Hondo', '', 10.5);
  $y = 115;
  $counter = 1;
  $syllabi = $conn->query("SELECT Syllabi.*, Date_Sheets.Exam_Date FROM Syllabi LEFT JOIN Date_Sheets ON Syllabi.ID = Date_Sheets.Syllabus_ID WHERE Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND Semester = " . $student['Duration'] . " AND Date_Sheets.Syllabus_ID IS NOT NULL ORDER BY Code ASC");
  while ($syllabus = $syllabi->fetch_assoc()) {
    $pdf->SetXY(16, $y);
    $pdf->Write(1, $counter++);
    $pdf->SetXY(26, $y);
    $pdf->Write(1, $syllabus['Code']);
    if (strlen($syllabus['Name']) > 32) {
      $pdf->SetXY(50, $y);
      $pdf->Write(1, substr($syllabus['Name'], 0, 32));
    } else {
      $pdf->SetXY(50, $y);
      $pdf->Write(1, substr($syllabus['Name'], 0, 32));
    }
    $pdf->SetXY(124, $y);
    $pdf->Write(1, date("d-m-Y", strtotime($syllabus['Exam_Date'])));
    $y += 10;
  }

  $pdf->SetY(52);
  $pdf->SetX(143.5);
  $pdf->SetLineWidth(.3);
  $pdf->Cell(28, 33.7, '', 1, 1, 'C');

  if (filetype($photo) === 'file' && file_exists($photo)) {
    try {
      $filename = $student['ID'] . "_Photo" . $file_extensions[0];
      $image = $filename;
      $pdf->Image($image, 94, 30.2, 26.7, 32.6);
      $photo = $image;
    } catch (Exception $e) {
      try {
        $filename = $student['ID'] . "_Photo" . $file_extensions[1];
        $image = $filename;
        $pdf->Image($image, 144.2, 52.5, 26.7, 32.6);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $student['ID'] . "_Photo" . $file_extensions[2];
          $image = $filename;
          $pdf->Image($image, 94, 30.2, 26.7, 32.6);
          $photo = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  $pdf->Image('sign.png', 154, 250.2, 30, 19);

  $i = 0;
  $end = 3;
  while ($i < $end) {
    // Delete Photos
    if (!empty($student_photo)) {
      $filename = $student['ID'] . "_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      unlink($filename);
    }
    $i++;
  }

  $pdf->Output('I', 'Admit Card.pdf');
}
