<?php
  use setasign\Fpdi\Fpdi;

  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
  
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    
    $pdf = new Fpdi();

    $pdf->SetTitle('Export Documents for '.$id);

    $documents = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type NOT IN ('Photo', 'Student Signature', 'Parent Signature')");
    while($document = $documents->fetch_assoc()){
      $files = explode("|", $document['Location']);
      foreach($files as $file){
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        
        $pdf->image("../..".$file, 10, 10, 190, 270);
      }
    }

    $pdf->Output('I', $id.'_Documents.pdf');
  }
