<?php
  ini_set('display_errors', 0);
  if(isset($_GET['id'])){
    require '../../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    $result = $conn->query("SELECT TRIM(CONCAT(Students.First_Name,' ',Students.Middle_Name,' ',Students.Last_Name))AS First_Name,Students.Father_Name,Students.Unique_ID,Students.Enrollment_No,Courses.Short_Name as Course,Course_Types.`Name` as Course_Type,Sub_Courses.`Name` as Sub_Course,Sub_Courses.Min_Duration,Admission_Sessions.Name as Admission_Session,Results.*FROM Results LEFT JOIN Students ON Results.Student_ID=Students.ID LEFT JOIN Courses ON Students.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID=Course_Types.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Results.Student_ID = $id ORDER BY Results.P_Code");
    if($result->num_rows==0){
      exit(json_encode(['status'=>false, 'message'=>'Result not exists!']));
    }

    $result = $result->fetch_assoc();
    $date = date("d-m-Y", strtotime($result['Published_On']));
    $student_id = $result['Unique_ID'];

    $courseType = explode("-", $result['Course_Type']);
    $result['Course_Type'] = trim($courseType[1]);

    $course = explode("-", $result['Course']);
    $result['Course'] = trim($course[0]);

    $remark = $result['Remarks'];

    function numberToRomanRepresentation($number) {
      $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
      $returnValue = '';
      while ($number > 0) {
        foreach ($map as $roman => $int) {
          if($number >= $int) {
            $number -= $int;
            $returnValue .= $roman;
            break;
          }
        }
      }
      return $returnValue;
    }

    function percentage($max, $obtained){
      if($obtained=='Ab'){
        return 'Ab';
      }else{
        $percentage = ($obtained / $max)*100;
        return number_format((float)$percentage, 2, '.', '');
      }
    }

    function gradePoint($percentage){
      if($percentage=='Ab'){
        return 0;
      }elseif($percentage>=90){
        return 10;
      }elseif($percentage>=80){
        return 9;
      }elseif($percentage>=70){
        return 8;
      }elseif($percentage>=60){
        return 7;
      }elseif($percentage>=50){
        return 6;
      }elseif($percentage>=40){
        return 5;
      }elseif($percentage<40){
        return 0;
      }
    }

    function credit($percentage, $credit){
      if($percentage=='Ab'){
        return 0;
      }elseif($percentage<40){
        return 0;
      }else{
        return $credit;
      }
    }

    include '../../../extras/vendor/setasign/fpdf/fpdf.php';
    include '../../../extras/vendor/setasign/fpdf/exfpdf.php';
    include '../../../extras/vendor/setasign/fpdf/easyTable.php';

    $pdf=new exFPDF();
    $pdf->AddPage(); 
    $pdf->SetTitle('Result');
    $pdf->SetFont('arial','',10);

    $table1 = new easyTable($pdf, 1);
    $table1->easyCell('STATEMENT OF MARKS', 'font-size:18; font-style:B; font-color:#000; align:C');
    $table1->printRow();
    $table1->endTable(3);
    $table2 = new easyTable($pdf, '{137, 73}', 'border:1; border-color:#000;');
    $table2->easyCell('School: Vocational Studies', 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->easyCell('Department: '.$result['Course_Type'], 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->printRow();

    // Batch
    $batchStart = 2000+((-1)*(int)filter_var($result['Admission_Session'], FILTER_SANITIZE_NUMBER_INT));
    $batchEnd = $batchStart+($result['Min_Duration']/2);
    $batch = $batchStart.' - '.$batchEnd;

    $table2->easyCell('Program: '.$result['Course'].' '.$result['Sub_Course'], 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->easyCell('Batch: '.$batch, 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->printRow();

    $table2->easyCell('Name: '.ucwords(strtolower($result['First_Name'])), 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->easyCell('Semester: '.numberToRomanRepresentation($result['Sem']), 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->printRow();

    $table2->easyCell('Enrollment No: '.$result['Enrollment_No'], 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->easyCell('Examination held: '.$result['Exam_Session'], 'font-size:10; font-style:B; font-color:#000; align:L');
    $table2->printRow();
    $table2->endTable(5);

    $table3 = new easyTable($pdf, '{26, 44, 15, 22, 22, 22, 22, 15, 23, 17}', 'border:1; border-color:#000;');
    $table3->easyCell('Subject Code', 'font-size:10; font-style:B; font-color:#000; align:C; rowspan:2; valign:T');
    $table3->easyCell('Subject Name', 'font-size:10; font-style:B; font-color:#000; align:C; rowspan:2; valign:T');
    $table3->easyCell('Credit', 'font-size:10; font-style:B; font-color:#000; align:C; rowspan:2; valign:T');
    $table3->easyCell('Internal', 'font-size:10; font-style:B; font-color:#000; align:C; colspan:2;');
    $table3->easyCell('External', 'font-size:10; font-style:B; font-color:#000; align:C; colspan:2;');
    $table3->easyCell('Total Marks', 'font-size:10; font-style:B; font-color:#000; align:C; rowspan:2; valign:T');
    $table3->easyCell("Grade Point\n(GP)\n(out of 10)", 'font-size:10; font-style:B; font-color:#000; align:C; rowspan:2; valign:T');
    $table3->easyCell('Earned Credit (EC)', 'font-size:10; font-style:B; font-color:#000; align:C; rowspan:2; valign:T');
    $table3->printRow();

    $table3->easyCell('Min/Max', 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
    $table3->easyCell('Marks Obtained', 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
    $table3->easyCell('Min/Max', 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
    $table3->easyCell('Marks Obtained', 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
    $table3->printRow();

    $totalCredit = array();
    $totalEarnedCredit = array();
    $totalMax = array();
    $totalObtained = array();
    $totalInternalMin = array();
    $totalInternalMax = array();
    $totalInternalObtained = array();
    $totalExternalMin = array();
    $totalExternalMax = array();
    $totalExternalObtained = array();
    $result = $conn->query("SELECT Results.* FROM Results WHERE Results.Student_ID = $id ORDER BY Results.P_Code");
    while($row = $result->fetch_assoc()){

      $internalMax = explode("/", $row['IA_Max']);
      $externalMax = explode("/", $row['EA_Max']);
      $obtained = $row['IA_MO']=='Ab' && $row['EA_MO']=='Ab' ? 'Ab' : (int)$row['IA_MO']+(int)$row['EA_MO'];
      $percentage = percentage($row['Total_Max'], $obtained);
      $gradePoint = gradePoint($percentage);
      $credit = credit($percentage, $row['Credits']);

      $totalCredit[] = $row['Credits'];
      $totalEarnedCredit[] = $credit;
      $totalMax[] = $row['Total_Max'];
      $totalObtained[] = is_int($obtained) ? $obtained : 0;
      $totalInternalMin[] = $internalMax[0];
      $totalInternalMax[] = $internalMax[1];
      $totalInternalObtained[] = $row['IA_MO']=='Ab' ? 0 : $row['IA_MO'];
      $totalExternalMin[] = $externalMax[0];
      $totalExternalMax[] = $externalMax[1];
      $totalExternalObtained[] = $row['EA_MO']=='Ab' ? 0 : $row['EA_MO'];

      $table3->easyCell($row['Subject_Code'], 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($row['Subject_Name'], 'font-size:10; font-style:B; font-color:#000; align:L; valign:T');
      $table3->easyCell($row['Credits'], 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($row['IA_Max'], 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($row['IA_MO'], 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($row['EA_Max'], 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($row['EA_MO'], 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($obtained, 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($gradePoint, 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->easyCell($credit, 'font-size:10; font-style:B; font-color:#000; align:C; valign:T');
      $table3->printRow();
    }

    $table3->easyCell(' ', 'font-size:10; font-style:B; min-height:20; paddingY:2.5; font-color:#000; align:C; valign:T; colspan:10');
    $table3->printRow();

    $finalGrade = gradePoint(percentage(array_sum($totalInternalMax)+array_sum($totalExternalMax), array_sum($totalInternalObtained)+array_sum($totalExternalObtained)));

    $table3->easyCell('Total', 'font-size:10; font-style:B; font-color:#000; align:C; valign:T; colspan:2');
    $table3->easyCell(array_sum($totalCredit), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell(array_sum($totalInternalMin).'/'.array_sum($totalInternalMax), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell(array_sum($totalInternalObtained), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell(array_sum($totalExternalMin).'/'.array_sum($totalExternalMax), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell(array_sum($totalExternalObtained), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell(array_sum($totalInternalObtained)+array_sum($totalExternalObtained), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell($finalGrade, 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->easyCell(array_sum($totalEarnedCredit), 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table3->printRow();
    $table3->endTable(5);
    $sgpa = number_format((float)array_sum($totalEarnedCredit)*$finalGrade/array_sum($totalCredit), 2, '.', '');
    

    $table4 = new easyTable($pdf, '{98.5, 111.5}', 'border:1; border-color:#000;');
    $table4->easyCell('SGPA - '.$sgpa, 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table4->easyCell('CGPA - NA', 'font-size:10; font-style:B; font-color:#000; align:C; valign:T;');
    $table4->printRow();
    $table4->endTable(5);

    $table5 = new easyTable($pdf, 1);
    $table5->easyCell('Result: '.$remark, 'font-size:10; font-style:B; font-color:#000; align:L');
    $table5->printRow();

    $table5->easyCell('Date of Issue: '.$date, 'font-size:10; font-style:B; font-color:#000; align:L');
    $table5->printRow();

    $table5->endTable(3);
    
    $pdf->Output('I', $student_id.'_Result.pdf');
  }
