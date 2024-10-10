<?php
$delimiter = ",";
$filename = "Pendency Sample.csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');


$documents = array('Photo', 'Student Signature', 'Parent Signature', 'Aadhar', 'Affidavit', 'Migration', 'Other Certificate', 'High School' => 'High School Marksheet', 'Intermediate' => 'Intermediate Marksheet', 'UG' => 'Graduation Marksheet', 'PG' => 'Post Graduation Marksheet', 'Other' => 'Other Marksheet');

$fields = array('Student ID');

foreach ($documents as $document) {
  $fields[] = $document . ' Remark';
}

fputcsv($f, $fields, $delimiter);

fseek($f, 0);

// Set headers to download file rather than displayed 
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer 
fpassthru($f);
