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
  $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices WHERE `User_ID` = $id AND University_ID = " . $_SESSION['university_id']);
  while ($invoice = $invoices->fetch_assoc()) {
    $already[$invoice['Student_ID']] = $invoice['Duration'];
    $already_ids[] = $invoice['Student_ID'];
  }

  $students = $conn->query("SELECT ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4 AND Process_By_Center IS NULL");
  if ($students->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
  ?>
    <div class="row m-b-20">
      <div class="col-md-12 d-flex justify-content-end">
        <div>
          <button type="button" class="btn btn-primary" onclick="generateInvoice()">Generate Invoice</button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th></th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Payable</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($student = $students->fetch_assoc()) {
                if (in_array($student['ID'], $already_ids) && $student['Duration'] == $already[$student['ID']]) {
                  continue;
                }
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td>
                    <div class="form-check complete" style="margin-bottom: 0px;">
                      <input type="checkbox" class="student-checkbox" id="student-<?= $student['ID'] ?>" name="student_id" value="<?= $student['ID'] ?>">
                      <label for="student-<?= $student['ID'] ?>" class="font-weight-bold"></label>
                    </div>
                  </td>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['ID'] ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <td>
                    <?php
                    $balance = 0;
                    $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                    while ($ledger = $ledgers->fetch_assoc()) {
                      $fees = json_decode($ledger['Fee'], true);
                      foreach ($fees as $key => $value) {
                        $debit = $ledger['Type'] == 1 ? $value : 0;
                        $credit = $ledger['Type'] == 2 ? $value : 0;
                        $balance = ($balance + $credit) - $debit;
                      }
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


    <script type="text/javascript">
      function generateInvoice() {
        if ($('.student-checkbox').filter(':checked').length == 0) {
          notification('danger', 'Please select Student');
        } else {
          var center = '<?= $id ?>';
          var ids = [];
          $.each($("input[name='student_id']:checked"), function() {
            ids.push($(this).val());
          });

          $.ajax({
            url: '/app/centers/ledgers/lump-sum/generate-invoice',
            type: 'POST',
            data: {
              ids,
              center
            },
            dataType: 'json',
            success: function(data) {
              if (data.status) {
                notification('success', data.message);
                getLedger(center);
              } else {
                notification('danger', data.message);
              }
            }
          })
        }
      }
    </script>
<?php }
}

?>
