<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);

  $students_count = $conn->query("SELECT ID FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Added_For = ". $id ." AND Step = 4 AND Process_By_Center IS NULL");
    $counter = array();
    while ($student = $students_count->fetch_assoc()){
      $invoices_created = $conn->query("SELECT ID FROM Invoices WHERE Student_ID = " . $student['ID'] . " AND User_ID = " . $id . " AND University_ID = " . $_SESSION['university_id'] . "");
      if($invoices_created->num_rows != 0){
        $count = [];
      }else {
        $counter = $student;
      }
    }

    $pending_counter = array();
      $added_for[] = $id;
      $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = $id");
      while ($downline = $downlines->fetch_assoc()) {
        $added_for[] = $downline['User_ID'];
      }

      $users = implode(",", array_filter($added_for));

      $already = array();
      $already_ids = array();
      $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
      while ($invoice = $invoices->fetch_assoc()) {
        $already[$invoice['Student_ID']] = $invoice['Duration'];
        $already_ids[] = $invoice['Student_ID'];
      }

      $query = empty($already_ids) ? " AND ID IS NULL" : " AND ID IN (" . implode(',', $already_ids) . ")";

      $sessionQuery = "";
      if(isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])){
        $admission_session_id = intval($_GET['admission_session_id']);
        $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
      }

      $pending_count = $conn->query("SELECT ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4 $sessionQuery AND Process_By_Center IS NULL $query");

      while ($student = $pending_count->fetch_assoc()){
          $pending_counter[] = $student;      
      }


      $processed_count = $conn->query("SELECT ID FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Step = 4 AND Process_By_Center IS NOT NULL AND Payment_Received IS NULL AND Added_For = ". $id ."");
      $processed_countrer = array();
      while ($student = $processed_count->fetch_assoc()){
        $processed_countrer[] = $student;
      }
}?>
<ul class="nav nav-tabs nav-tabs-linetriangle " id="all-counter" data-init-reponsive-tabs="dropdownfx">
  <li class="nav-item" id="counter_student">
    <a class="active" data-toggle="tab" data-target="#students" href="#"><span>Students</span>-<span id="applied_student_count"><?=count($counter) == 0 ? 0 : count($counter) ?></span></a>
  </li>
  <li class="nav-item" id="counter_pending">
    <a data-toggle="tab" data-target="#pending" href="#"><span>Pending</span>-<span id="pending_student_count"><?=count($pending_counter) ?></span></a>
  </li>
  <li class="nav-item" id="counter_processed">
    <a data-toggle="tab" data-target="#processed" href="#"><span>Processed</span>-<span id="processed_student_count"><?=count($processed_countrer) ?></span></a>
  </li>
</ul>

