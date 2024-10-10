<?php
  require ('../../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $header[] = array('Lot', 'Center Code', 'Session', 'Name', 'Father Name', 'Student ID', 'Course', 'Contact', 'Address');

  $xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('ID Card Sample.xlsx');
