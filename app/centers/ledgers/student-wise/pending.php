<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);

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

  $students = $conn->query("SELECT ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4 $sessionQuery AND Process_By_Center IS NULL $query");
  if ($students->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
  ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Payable</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($student = $students->fetch_assoc()) {
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['ID'] ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <td>
                    <?php
                    $balance = 0;
                    $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                    while ($ledger = $ledgers->fetch_assoc()) {
                      // $fees = json_decode($ledger['Fee'], true);
                      // foreach ($fees as $key => $value) {
                      //   $debit = $ledger['Type'] == 1 ? $value : 0;
                      //   $credit = $ledger['Type'] == 2 ? $value : 0;
                      //   $balance = ($balance + $credit) - $debit;
                      // }
                      $balance = $ledger['Fee'];
                    }
                    echo "&#8377; " . (-1) * $balance;
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php }
}

?>
