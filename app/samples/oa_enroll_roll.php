<?php
  session_start();
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $header[] = array('Student ID', 'OA Number', 'Enrollment No', 'Roll No');

  if($_SESSION['has_lms']){
    $header[0] = array_merge($header[0], ['ID Card', 'Admit Card', 'Exam']);
  }

  $xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('OA Enrollment & Roll No Sample.xlsx');
