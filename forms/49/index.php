<?php
  ini_set('display_errors', 1);
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if(isset($_GET['student_id'])){
  require '../../includes/db-config.php';
  session_start();

  if($_SESSION['university_id']!=49){
    header('Location: /');
  }

  $id = mysqli_real_escape_string($conn, $_GET['student_id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  $student = $conn->query("SELECT Students.*, Courses.Name as Course,
Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as `Session`,
Admission_Types.Name as Type FROM Students LEFT JOIN Courses ON
Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON
Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions
ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN
Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID
WHERE Students.ID = $id");
  $student = mysqli_fetch_assoc($student);
  $address = json_decode($student['Address'], true);


  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  $pdf = new Fpdi();

  $pdf->SetTitle('Application Form');
  $pageCount = $pdf->setSourceFile('svu-form.pdf');
  $pdf->SetFont('Arial','B',11);

  // Tick Image
  $check = '../../assets/img/form/checked.png';

  // Extensions
  $file_extensions = array('.png', '.jpg', '.jpeg');

  //this folder will have there images.
  $path = "photos/";

  // Photo
  $student_photo = "";
  $photo = "";
  $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
  if($photo->num_rows>0){
    $photo = mysqli_fetch_assoc($photo);
    $photo = "../..".$photo['Location'];
    $student_photo = base64_encode(file_get_contents($photo));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data1 = base64_decode($student_photo);
      
      $filename1 = $id."_Photo" . $file_extensions[$i];
      // print_r($filename1);
      // exit();
      //$file_extensions loops through the file extensions
      file_put_contents($filename1, $data1); //we save our new images to the path above
      $i++;
    }
  }else{
    $photo = "";
  }

  // Signature
  $student_signature = "";
  $signature = "";
  $signature = $conn->query("SELECT Location FROM Student_Documents
  WHERE Student_ID = $id AND Type = 'Student Signature'");
  if($signature->num_rows>0){
    $signature = mysqli_fetch_assoc($signature);
    $signature = "../..".$signature['Location'];
    $student_signature = base64_encode(file_get_contents($signature));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data2 = base64_decode($student_signature);
      $filename2= $id."_Student_Signature" . $file_extensions[$i];
      //$file_extensions loops through the file extensions
      file_put_contents($filename2, $data2); //we save our new images to the path above
      $i++;
    }
  }else{
    $signature = "";
  }

  // Page 1
  $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  // Session
  $pdf->SetXY(115.5, 58);
  $pdf->Write(1, "23");
  
   // Session
  $pdf->SetXY(130.5, 58);
  $pdf->Write(1, "24");

  $pdf->SetFont('Arial','', 11);

  // Enrollment No.
  $enrollment_no = str_split($student['Enrollment_No']);
  $pdf->SetXY(62, 85);
  $pdf->Write(1, $student['Enrollment_No']);

  // Programme
  $pdf->SetXY(43, 70);
  $pdf->Write(1, $student['Course']);
  
    // Programme
  $pdf->SetXY(69, 75);
  $pdf->Write(1, $student['Sub_Course']);

  //Modes
  //$pdf->SetXY(48, 81);
  //$pdf->Write(1, "Regular");

  // Photo
  if (filetype($photo) === 'file' && file_exists($photo)) {
    try {
      $filename = $id."_Photo" . $file_extensions[0];
      $image = $filename;
      $pdf->Image($image, 166, 18, 31.5, 32);
      $photo = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Photo" . $file_extensions[1];
        $image = $filename;
        $pdf->Image($image, 166, 18, 31.5, 32);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Photo" . $file_extensions[2];
          $image = $filename;
          $pdf->Image($image, 166, 18, 31.5, 32);
          $photo = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Signature
  if (filetype($signature) === 'file' && file_exists($signature)) {
    try {
      $filename = $id."_Student_Signature" . $file_extensions[0];
      $image = $filename;
      $pdf->Image($image, 166, 51, 30.2, 8.3);
      $student_signature = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Student_Signature" . $file_extensions[1];
        $image = $filename;
        $pdf->Image($image, 166, 51, 30.2, 8.3);
        $student_signature = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Signature" . $file_extensions[2];
          $image = $filename;
          $pdf->Image($image, 166, 51, 30.2, 8.3);
          $student_signature = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Student Name
  $student_name = str_split(str_replace('  ', ' ',
  $student['First_Name']." ".$student['Middle_Name']."
  ".$student['Last_Name']));
    $x = 17;
  foreach ($student_name as $name){
    $pdf->SetXY($x, 100);
    $pdf->Write(1, $name);
    $x += 5.5;
  }

  // Father Name
  $father_name = str_split($student['Father_Name']);
  $x = 17;
  foreach ($father_name as $name){
    $pdf->SetXY($x, 113);
    $pdf->Write(1, $name);
    $x += 5.5;
  }

  // Mother Name
  $mother_name = str_split($student['Mother_Name']);
  $x = 17;
  foreach ($mother_name as $name){
    $pdf->SetXY($x, 127);
    $pdf->Write(1, $name);
    $x += 5.5;
  }

  // DOB
  $dob = str_split($student['DOB']);
  // Day
  $pdf->SetXY(16, 140);
  $pdf->Write(1, $dob[8]);
  $pdf->SetXY(22, 140);
  $pdf->Write(1, $dob[9]);
  // Month
  $pdf->SetXY(28, 140);
  $pdf->Write(1, $dob[5]);
  $pdf->SetXY(34, 140);
  $pdf->Write(1, $dob[6]);
  // Year
  $pdf->SetXY(40, 140);
  $pdf->Write(1, $dob[0]);
  $pdf->SetXY(46, 140);
  $pdf->Write(1, $dob[1]);
  $pdf->SetXY(52, 140);
  $pdf->Write(1, $dob[2]);
  $pdf->SetXY(58, 140);
  $pdf->Write(1, $dob[3]);

  // Adhar
  $pdf->SetXY(40, 251);
  $pdf->Write(1, $student['Aadhar_Number']);

  // Gender
  $gender = $student['Gender'] == "Male" ? "Male" : "Female";
  $pdf->Image($check, 94, 139, 3, 3);
  
  // Category
  $pdf->SetXY(131, 138);
  $pdf->Write(1, $student['Category']);

  // Email Contact
  $pdf->SetXY(37, 246);
  $pdf->Write(1, $student['Email']);
  
  // Mobile
  $dob = str_split($student['Contact']);
  $pdf->SetXY(18, 242);
  $pdf->Write(1, $dob[0]);
  $pdf->SetXY(26, 242);
  $pdf->Write(1, $dob[1]);
  $pdf->SetXY(32, 242);
  $pdf->Write(1, $dob[2]);
  $pdf->SetXY(39, 242);
  $pdf->Write(1, $dob[3]);
  $pdf->SetXY(46, 242);
  $pdf->Write(1, $dob[4]);
  $pdf->SetXY(52, 242);
  $pdf->Write(1, $dob[5]);
  $pdf->SetXY(58, 242);
  $pdf->Write(1, $dob[6]);
  $pdf->SetXY(65, 242);
  $pdf->Write(1, $dob[7]);
  $pdf->SetXY(72, 242);
  $pdf->Write(1, $dob[8]);
  $pdf->SetXY(80, 242);
  $pdf->Write(1, $dob[9]);
  
  
    // Academics
  $academis = array('High School', 'Intermediate', 'Under Graduation',
    'Post Graduation', 'Other');
    $y = '190';
  foreach($academis as $academic){
    $x = '48';

    // Details
    $type = $academic == 'Under Graduation' ? 'UG' : ($academic ==
    'Post Graduation' ? 'PG' : $academic);
    $data = $conn->query("SELECT * FROM Student_Academics WHERE
    Student_ID = $id AND Type = '$type'");
    if($data->num_rows>0){

      $data = mysqli_fetch_assoc($data);
  $pdf->SetFont('Arial','',9);
       $pdf->SetXY($x, $y);
      $pdf->Write(1, $academic);
      //Board
      //$x += 2;
      //$pdf->SetXY($x, $y);
      //$pdf->Write(1, !empty($data['Board/Institute']) ?
      //substr($data['Board/Institute'],0,28) : ''); 

      //$x += 33;
      //$pdf->SetXY($x, $y);
      //$pdf->Write(1, !empty($data['Board/Institute']) ?
      //substr($data['Board/Institute'],0,28) : '');

      $x += 53;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, !empty($data['Year']) ? $data['Year'] : '');

      
      $x += 63;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');
      
      $x += 7;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, !empty($data['Subject']) ? $data['Subject'] : '');

      //$x += 15;
      //$pdf->SetXY($x, $y);
      //$pdf->Write(1, !empty($data['Total_Marks']) ? $data['Total_Marks'] : '');

      // Roll No
      //$x += 48;
      //$pdf->SetXY($x, $y);
      //$pdf->Write(1, !empty($data['Marks_Obtained']) ? $data['Marks_Obtained'] : '');

 
    }
    $y += 8;
  }
  
  // Page 2
  $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);
  
	// Country
  $pdf->SetXY(154, 218);
  $pdf->Write(1, $student['Nationality']);

  // Permanent Address
  	$AdressArray = explode(',', $address['present_address']);
    if (isset($AdressArray[0])) {
        $pdf->SetXY(23, 25);
        $pdf->Write(1, $AdressArray[0]);
    }
    if (isset($AdressArray[1])) {
        $pdf->SetXY(23, 30);
        $pdf->Write(1, $AdressArray[1]);
    }
    if (isset($AdressArray[2])) {
        $pdf->SetXY(23, 30);
        $pdf->Write(1, $AdressArray[2]);
    }


  // District
  $pdf->SetFont('Arial','',10);
  $pdf->SetXY(33, 36);
  $pdf->Write(1, $address['present_district']);

  // State
  $pdf->SetFont('Arial','',10);
  $pdf->SetXY(70, 36);
  $pdf->Write(1, $address['present_state']);

   // Pincode
   $permanent_pincode = str_split($address['present_pincode']);
   $x = 52;
   for($i=0; $i<count($permanent_pincode); $i++){
     $pdf->SetXY($x, 44);
     $pdf->Write(1, $permanent_pincode[$i]);
     $x += 6.5;
   }




  // Page 3
  // $pageId = $pdf->importPage(3, PdfReader\PageBoundaries::MEDIA_BOX);
  // $pdf->addPage();
  // $pdf->useImportedPage($pageId, 0, 0, 210);

  // // Date
  // $pdf->SetXY(100.5, 190.5);
  // $pdf->Write(1, date('d-m-Y'));


  $i = 0;
  $end = 3;
  while ($i < $end) {
    // Delete Photos
    if(!empty($student_photo)){
      $filename = $id."_Photo" . $file_extensions[$i];
      //$file_extensions loops through the file extensions
      unlink($filename);
    }

    // Delete Signatures
    if(!empty($student_signature)){
      $filename= $id."_Student_Signature" . $file_extensions[$i];
      //$file_extensions loops through the file extensions
      unlink($filename);
    }
    $i++;
  }

  $pdf->Output('I', 'Application Form.pdf');
}
