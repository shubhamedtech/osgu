<?php
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $header[] = array('Scheme', 'Course', 'Sub-Course', 'Semester', 'Subject Code', 'Subject Name', 'Type (Theory/Practical)', 'Credit','Minimum Marks', 'Maximum Marks');

  $xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('Subjects Sample.xlsx');
