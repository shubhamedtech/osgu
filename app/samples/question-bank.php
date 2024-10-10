<?php
$delimiter = ",";
$filename = "Question Bank Sample.csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

$fields = array('Question', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Answer', 'Marks');

fputcsv($f, $fields, $delimiter);
fseek($f, 0);

// Set headers to download file rather than displayed 
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer 
fpassthru($f);
