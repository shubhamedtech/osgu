<?php

// String Encryption
function stringToSecret(string $string = NULL)
{
  if (!$string) {
    return NULL;
  }
  $length = strlen($string);
  $visibleCount = (int) round($length / 6);
  $hiddenCount = $length - ($visibleCount * 2);
  return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
}

function uuidGenerator($table, $conn)
{
  $all_key = array();
  $get_key = $conn->query("SELECT Api_Key FROM $table");
  while ($gk = $get_key->fetch_assoc()) {
    $all_key[] = $gk['Api_Key'];
  }

  $data = $data ?? random_bytes(16);
  assert(strlen($data) == 16);
  // Set version to 0100
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  // Set bits 6-7 to 10
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  // Output the 36 character UUID.
  $generated_key = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  if (in_array($generated_key, $all_key)) {
    uuidGenerator($table, $conn);
  } else {
    return $generated_key;
  }
}

function generateStudentLedger($conn, $student_id)
{
  $check = $conn->query("SELECT ID FROM Student_Ledgers WHERE Student_ID = $student_id");
  if ($check->num_rows > 0) {
    $conn->query("DELETE FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  }

  $student_fee = array();
  $student_fee_without_sharing = array();

  $student = $conn->query("SELECT Users.Role, Admission_Type_ID, Students.University_ID, Students.Adm_Duration,  Students.Duration, Students.Course_ID, Sub_Course_ID, Sub_Courses.Min_Duration, Added_For, Students.Created_At, Universities.Is_Vocational, Admission_Sessions.Name as Session FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Universities ON Students.University_ID = Universities.ID LEFT JOIN  Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Users ON Students.Added_For = Users.ID  WHERE Students.ID = $student_id");
  $student = mysqli_fetch_assoc($student);
  $reporting = $conn->query("SELECT Center_SubCenter.Center FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Sub_Center = Users.ID WHERE Users.Role = 'Sub-Center' AND Sub_Center = " . $student['Added_For'] . "");
  if ($reporting->num_rows > 0) {
    $reporting = mysqli_fetch_assoc($reporting);
    $student['Added_For'] = $reporting['Center'];
  }

  $structures = array();
  $fee_structures = $conn->query("SELECT ID, Fee_Applicable_ID FROM Fee_Structures WHERE University_ID = " . $student['University_ID'] . " AND Status = 1 ORDER BY Fee_Applicable_ID");
  while ($fee_structure = $fee_structures->fetch_assoc()) {
    $structures[$fee_structure['ID']] = $fee_structure['Fee_Applicable_ID'];
  }

  // for ($i = $student['Duration']; $i <= $student['Min_Duration']; $i++) {
  //   foreach ($structures as $id => $applicable) {
  //     $fee_structure = $conn->query("SELECT ID, Name, Sharing,Is_Constant FROM Fee_Structures WHERE ID = $id");
  //     $fee_structure = $fee_structure->fetch_assoc();

      // Constant Fee with Sharing
      // if ($fee_structure['Sharing'] == 1 && $fee_structure['Is_Constant'] == 1) {
      //   if ($student['Is_Vocational'] == 1 && $fee_structure['Name'] == 'Course Fee') {
      //     $fee = $conn->query("SELECT Fee, 'Applicable_In' FROM Center_Sub_Courses WHERE University_ID = " . $student['University_ID'] . " AND `User_ID` = " . $student['Added_For'] . " AND `Course_ID` = " . $student['Course_ID'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . "");
      //   } else {
      //     $fee = $conn->query("SELECT Fee, Applicable_In FROM Fee_Constant WHERE Fee_Structure_ID = $id AND Course_ID = " . $student['Course_ID'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . "");
      //   }
      //   if ($fee->num_rows == 0) {
      //     echo json_encode(['status' => 203, 'message' => 'Fee for this course is not configured yet!']);
      //     exit();
      //   }
      //   $fee = $fee->fetch_assoc();

      //   if ($fee['Applicable_In'] == 'Applicable_In') {
      //     $fee['Applicable_In'] = '{"1": [1, 2, 3, 4, 5, 6]}';
      //     $sharing = 100;
      //   } else {
      //     $sharing = $conn->query("SELECT Fee FROM Fee_Variables WHERE Fee_Structure_ID = $id AND Code = " . $student['Added_For'] . " AND University_ID = " . $student['University_ID'] . "");
      //     if ($sharing->num_rows > 0) {
      //       $sharing = mysqli_fetch_assoc($sharing);
      //       $sharing = !empty($sharing['Fee']) ? $sharing['Fee'] : 0;
      //     } else {
      //       $sharing = 100;
      //     }
      //   }

      //   $applicability = json_decode($fee['Applicable_In'], true);

      //   $applicability_type = array_keys($applicability);

      //   $constant_fee = in_array($applicable, [1, 2]) && in_array($i, $applicability[$applicable]) ? $fee['Fee'] : (!in_array($applicable, [1, 2]) ? $fee['Fee'] : 0);

      //   // All
      //   if ($applicability_type[0] == 1) {
      //     $student_fee[$fee_structure['ID']] = !empty($fee['Fee']) ? round(($constant_fee / 100) * $sharing) : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = !empty($fee['Fee']) ? $constant_fee : 0;
      //   }

      //   // On Selected Duration
      //   if ($applicability_type[0] == 2) {
      //     $student_fee[$fee_structure['ID']] = in_array($i, $applicability[2]) ? round(($constant_fee / 100) * $sharing) : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $constant_fee : 0;
      //   }

      //   // On Admission Type
      //   if ($applicability_type[0] == 3) {
      //     $student_fee[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? round(($constant_fee / 100) * $sharing) : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $constant_fee : 0;
      //   }

      //   // On New Admission Punch
      //   if ($applicability_type[0] == 4) {
      //     $student_fee[$fee_structure['ID']] = $i == $student['Duration'] ? round(($constant_fee / 100) * $sharing) : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = $i == $student['Duration'] ? $constant_fee : 0;
      //   }
      // }

      // Constant Fee without Sharing
      // if ($fee_structure['Sharing'] == 0 && $fee_structure['Is_Constant'] == 1) {
      //   $fee = $conn->query("SELECT Fee, Applicable_In FROM Fee_Constant WHERE Fee_Structure_ID = $id AND Course_ID = " . $student['Course_ID'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . "");
      //   if ($fee->num_rows == 0) {
      //     echo json_encode(['status' => 203, 'message' => 'Fee for this cours e is not configured yet!']);
      //     exit();
      //   }
      //   $fee = $fee->fetch_assoc();

      //   $applicability = json_decode($fee['Applicable_In'], true);

      //   $applicability_type = array_keys($applicability);

      //   // All
      //   if ($applicability_type[0] == 1) {
      //     $student_fee[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
      //   }

      //   // On Selected Duration
      //   if ($applicability_type[0] == 2) {
      //     $student_fee[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
      //   }

      //   // On Admission Type
      //   if ($applicability_type[0] == 3) {
      //     $student_fee[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
      //   }

      //   // On New Admission Punch
      //   if ($applicability_type[0] == 4) {
      //     $student_fee[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
      //     $student_fee_without_sharing[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
      //   }
      // }

      // Variable Fee
      // if ($fee_structure['Sharing'] == 0 && $fee_structure['Is_Constant'] == 0) {
        // $fee = $conn->query("SELECT Fee, Applicable_In FROM Fee_Variables WHERE Fee_Structure_ID = $id AND Code = " . $student['Added_For'] . " AND University_ID = " . $student['University_ID'] . "");
        // if ($fee->num_rows == 0) {
        //   echo json_encode(['status' => 203, 'message' => 'Fee for this course is not configured yet!']);
        //   exit();
        // }
        $fee = $conn->query("SELECT Fee FROM Center_Sub_Courses WHERE User_ID = " . $student['Added_For'] . " AND Course_ID = " . $student['Course_ID'] . " AND Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND University_ID = " . $student['University_ID'] . "");

        $fee = $fee->fetch_assoc();
        // print_r($fee);
        // exit;
        // $applicability = json_decode($fee['Applicable_In'], true);

        // $applicability_type = array_keys($applicability);

        // // All
        // if ($applicability_type[0] == 1) {
        //   $student_fee[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
        //   $student_fee_without_sharing[$fee_structure['ID']] = !empty($fee['Fee']) ? $fee['Fee'] : 0;
        // }

        // // On Selected Duration
        // if ($applicability_type[0] == 2) {
        //   $student_fee[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
        //   $student_fee_without_sharing[$fee_structure['ID']] = in_array($i, $applicability[2]) ? $fee['Fee'] : 0;
        // }

        // // On Admission Type
        // if ($applicability_type[0] == 3) {
        //   $student_fee[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
        //   $student_fee_without_sharing[$fee_structure['ID']] = $student['Admission_Type_ID'] == $applicability[3] ? $fee['Fee'] : 0;
        // }

        // // On New Admission Punch
        // if ($applicability_type[0] == 4) {
        //   $student_fee[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
        //   $student_fee_without_sharing[$fee_structure['ID']] = $i == $student['Duration'] ? $fee['Fee'] : 0;
        // }
        $center_course_fee = $fee['Fee'];
    //   }
    // }

    $date = date('Y-m-d', strtotime($student['Created_At']));
 //kp-16-9   // $add = $conn->query("INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Fee, Fee_Without_Sharing, Status) VALUES ('$date', $student_id, " . $student['Duration'] . ", " . $student['University_ID'] . ", 1, $center_course_fee, $center_course_fee, 1)");

    // start kp 
    $centerFee = $center_course_fee;
    $studentFee = $center_course_fee;

    $maxDuration = json_decode($student['Min_Duration'], true);
    
    $admissionDate = $student['Created_At'];
    $startDuration = !empty($student['Adm_Duration']) ? $student['Adm_Duration'] : $student['Duration'];
    $durations = range($startDuration, $maxDuration);
    $session = $student['Session'];
    $sessionMonth = date("m", strtotime($session));
    
    $ledgerDates = array();
    $newDate = date("Y-$sessionMonth-01 H:i:s", strtotime($admissionDate));
    foreach ($durations as $duration) {
      if ($duration == $startDuration) {
        $ledgerDates[$duration] = $admissionDate;
        $studentLedgerDate = $admissionDate;
      } else {
        $newDate = $duration == $startDuration + 1 ? $newDate : $ledgerDates[$duration - 1];
        $ledgerDates[$duration] = date("Y-m-01 H:i:s", strtotime("+6 months " . $newDate));
        $studentLedgerDate = date("Y-m-01 H:i:s", strtotime("+6 months " . $newDate));
      }
// echo "INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Fee, Fee_Without_Sharing, Status) VALUES ('$studentLedgerDate', $student_id, '$duration'," . $student['University_ID'] . ", 1, '$studentFee', '$studentFee', 1)"; 
      $add = $conn->query("INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Fee, Fee_Without_Sharing, Status) VALUES ('$studentLedgerDate', $student_id, '$duration'," . $student['University_ID'] . ", 1, '$studentFee', '$studentFee', 1)");
      if ($add && $student['Role'] == 'Sub-Center') {
        // Settlement Amount
        $ledgerId = $conn->insert_id;
        $settlementAmount = $centerFee;
        // $settlementAmount = $studentFee - $centerFee;

        $update = $conn->query("UPDATE Student_Ledgers SET Settlement_Amount = $settlementAmount, Center_Fee = $centerFee WHERE ID = $ledgerId");
      }
    }

    // end kp
  // }
}

function activityLogs($conn, $message, $user_id)
{
}

function generateLeadHistory($conn, $lead_id, $user_id, $old, $new)
{
  $result = array_diff($old, $new);
  if (!empty($result)) {
    $update = $conn->query("INSERT INTO Lead_Histories (Lead_ID, `User_ID`, Data, Created_By) VALUES ($lead_id, $user_id, '" . json_encode($result) . "', " . $_SESSION['ID'] . ")");
  }
}


function generateStudentID($conn, $suffix, $length, $university_id)
{
  $student_ids = array();
  $ids = $conn->query("SELECT Unique_ID FROM Students WHERE University_ID = " . $university_id . " AND Unique_ID IS NOT NULL");
  while ($id = $ids->fetch_assoc()) {
    $student_ids[] = $id['Unique_ID'];
  }

  $ids = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE University_ID = " . $university_id . " AND Unique_ID IS NOT NULL");
  while ($id = $ids->fetch_assoc()) {
    $student_ids[] = $id['Unique_ID'];
  }

  $student_ids = array_filter($student_ids);

  $result = '';
  for ($i = 0; $i < $length; $i++) {
    $result .= mt_rand(0, 9);
  }

  $new_id = $suffix . $result;
  if (in_array($new_id, $student_ids)) {
    generateStudentID($conn, $suffix, $length, $university_id);
  } else {
    return $new_id;
  }
}

function clean($string)
{
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  return preg_replace('/[^A-Za-z0-9\-.|,]/', '', $string); // Removes special chars.
}

function balanceAmount($conn, $student_id, $duration)
{
  $balance = 0;
  $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = $student_id AND Status = 1 AND Duration <= " . $duration);
  while ($ledger = $ledgers->fetch_assoc()) {
    // $fees = json_decode($ledger['Fee'], true);
    // foreach ($fees as $key => $value) {
    //   $debit = $ledger['Type'] == 1 ? $value : 0;
    //   $credit = $ledger['Type'] == 2 ? $value : 0;
      // $balance = ($balance + $credit) - $debit;
    // }
    $balance = $ledger['Fee'];
  }

  return (int)$balance ;
}

function numberTowords($number)
{
  $decimal = round($number - ($no = floor($number)), 2) * 100;
  $hundred = null;
  $digits_length = strlen($no);
  $i = 0;
  $str = array();
  $words = array(
    0 => '', 1 => 'one', 2 => 'two',
    3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
    7 => 'seven', 8 => 'eight', 9 => 'nine',
    10 => 'ten', 11 => 'eleven', 12 => 'twelve',
    13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
    16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
    19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
    40 => 'forty', 50 => 'fifty', 60 => 'sixty',
    70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
  );
  $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
  while ($i < $digits_length) {
    $divider = ($i == 2) ? 10 : 100;
    $number = floor($no % $divider);
    $no = floor($no / $divider);
    $i += $divider == 10 ? 1 : 2;
    if ($number) {
      $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
      $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
      $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
    } else $str[] = null;
  }
  $Rupees = implode('', array_reverse($str));
  $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
  return ($Rupees ? $Rupees . 'Rs. Only ' : '') . $paise;
}

function getLedgerSummary($conn, $student_id)
{
  // Total Fee
  $totalFee = array();
  $remittedFee = array();
  $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  if ($debits->num_rows == 0) {
    generateStudentLedger($conn, $student_id);
    $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
  }

  if ($debits->num_rows > 0) {
    while ($debit = $debits->fetch_assoc()) {
      if (empty($debit['Fee_Without_Sharing'])) {
        generateStudentLedger($conn, $student_id);
        $debits = $conn->query("SELECT Fee_Without_Sharing FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 1");
        while ($debit = $debits->fetch_assoc()) {
          $fees = json_decode($debit['Fee_Without_Sharing'], true);
          $totalFee[] = array_sum($fees);
        }
      } else {
        $fees = json_decode($debit['Fee_Without_Sharing'], true);
        $totalFee = $fees;
      }
    }
  }

  $credits = $conn->query("SELECT Fee FROM Student_Ledgers WHERE Student_ID = $student_id AND Type = 2");
  if ($credits->num_rows > 0) {
    while ($credit = $credits->fetch_assoc()) {
      $paid = $credit['Fee'];
      $remittedFee = $paid;
    }
  }

  return json_encode(['totalFee' => $totalFee, 'totalRemitted' => $remittedFee, 'totalBalance' => $totalFee - (int)$remittedFee]);
}
