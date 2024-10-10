<?php
  use setasign\Fpdi\Fpdi;
  use setasign\Fpdi\PdfReader;

  if(isset($_GET['student_id'])){
    require '../../includes/db-config.php';

    $student_id = mysqli_real_escape_string($conn, $_GET['student_id']);
    $student_id = base64_decode($student_id);

    $student = $conn->query("SELECT * FROM Himalayan_ID_Cards WHERE ID = $student_id");
    if($student->num_rows==0){
      header('Location: /id-cards/himalayan/');
    }
    
    $student = $student->fetch_assoc();

    $file_extensions = array('.png', '.jpg', '.jpeg');

    $photo = "images/".$student['Photo'];
    $student_photo = base64_encode(file_get_contents($photo));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data1 = base64_decode($student_photo); 
      $filename1 = $student['ID']."_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      file_put_contents($filename1, $data1); //we save our new images to the path above
      $i++;
    }

    require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

    $pdf = new Fpdi('L','mm', array(113,140));

    $pdf->SetTitle('ID Card');

    $pageCount = $pdf->setSourceFile('id-card.pdf');

    $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 140);

    $pdf->SetAutoPageBreak(true, 5);  
    
    $pdf->Image('bg.png', 8,32,125,69);

    $pdf->SetFont('Arial','BU',13);
    $pdf->SetTextColor(40,22,111);

    $pdf->SetXY(50, 34);
    $pdf->Write(1, 'Session: '.$student['Session']);

    $pdf->SetFont('Arial','',12);

    $pdf->SetXY(10, 44);
    $pdf->Write(1, 'Name              : '.$student['Name']);

    $pdf->SetXY(10, 52);
    $pdf->Write(1, 'F./H. Name     : '.$student['Father_Name']);

    $pdf->SetXY(10, 60);
    $pdf->Write(1, 'Student ID No : '.$student['Student_ID']);

    $pdf->SetXY(10, 68);
    $pdf->Write(1, 'Course            : '.$student['Course']);

    $pdf->SetXY(10, 76);
    $pdf->Write(1, 'Contact           : '.$student['Contact']);

    $pdf->SetXY(10, 84);
    $pdf->Write(1, 'Address          : '.substr($student['Address'],0,35));
    $pdf->SetXY(10, 90);
    $pdf->Write(1, '                         '.substr($student['Address'],35,37));

    $pdf->SetY(40.8);
    $pdf->SetX(102);
    $pdf->SetLineWidth(.3);
    $pdf->Cell(30,35,'',1,1,'C');

    if (filetype($photo) === 'file' && file_exists($photo)) {
      try {
        $filename = $student['ID']."_Photo" . $file_extensions[0];
        $image = $filename;
        $pdf->Image($image, 102.3, 41, 29.4, 34.6);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $student['ID']."_Photo" . $file_extensions[1];
          $image = $filename;
          $pdf->Image($image, 102.3, 41, 29.4, 34.6);
          $photo = $image;
        } catch (Exception $e) {
          try {
            $filename = $student['ID']."_Photo" . $file_extensions[2];
            $image = $filename;
            $pdf->Image($image, 102.3, 41, 29.4, 34.6);
            $photo = $image;
          } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
          }
        }
      }
    }

    $i = 0;
    $end = 3;
    while ($i < $end) {
      // Delete Photos
      if(!empty($student_photo)){
        $filename = $student['ID']."_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
        unlink($filename);
      }
      $i++;
    }
    
    
    $pdf->Output('I', 'ID Card.pdf');
  }
