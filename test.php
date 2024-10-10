<?php
ini_set('display_errors',1);
require 'includes/db-config.php';
$reRegistrations = $conn->query("SELECT Student_ID, Duration, Amount, University_ID, Payment_ID FROM Re_Registrations WHERE Status = 1");
      while($reRegistration = $reRegistrations->fetch_assoc()){
          $payments = $conn->query("SELECT Transaction_Date, Transaction_ID FROM Payments WHERE ID = ".$reRegistration['Payment_ID']);
          $payment = $payments->fetch_assoc();
          $updateStudent = $conn->query("UPDATE Students SET Duration = " . $reRegistration['Duration'] . " WHERE ID = ".$reRegistration['Student_ID']);
        $add = $conn->query("INSERT INTO Student_Ledgers (Student_ID, Duration, Date, University_ID, Type, Source, Transaction_ID, Fee, Status) VALUES (" . $reRegistration['Student_ID'] . ", " . $reRegistration['Duration'] . ", '" . date("Y-m-d", strtotime($payment['Transaction_Date'])) . "', " . $reRegistration['University_ID'] . ", 2, 'Offline', '" . $payment['Transaction_ID'] . "', '" . json_encode(['Paid' => $reRegistration['Amount']]) . "', 1)");
      }