<?php
ini_set('display_errors', 1);
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $heads = array();
  $fee_heads = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = " . $_SESSION['university_id']);
  while ($fee_head = $fee_heads->fetch_assoc()) {
    $heads[$fee_head['ID']] = $fee_head['Name'];
  }

  $student = $conn->query("SELECT Admission_Sessions.Name as Session, Admission_Types.Name as Admission_Type, Courses.Short_Name as Course, Sub_Courses.Name as Sub_Course, Students.Duration as Duration, Student_Documents.Location, Modes.Name as Mode FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo' LEFT JOIN Modes ON Students.Mode_ID = Modes.ID WHERE Students.ID = $id");
  $student = $student->fetch_assoc();
?>
  <div class="row d-flex justify-content-center">
    <div class="col-md-4">
      <div class="card card-transparent">
        <div class="card-header bg-transparent text-center">
          <img class="profile_img" src="<?= $student['Location'] ?>" alt="">
          <h5><?= $student['Session'] ?> (<?= $student['Admission_Type'] ?>)</h5>
          <h6><?= $student['Course'] ?> (<?= $student['Sub_Course'] ?>)</h6>
        </div>
      </div>
    </div>
  </div>
  <div class="row" style="margin-bottom:20px">
    <div class="col-md-12 d-flex justify-content-end">
      <div>
        <?php if (isset($_SESSION['gateway'])) { ?>
          <!--<button type="button" class="btn btn-primary" onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')"> Pay Online</button>-->
        <?php } ?>
        <?php if (in_array($_SESSION['Role'], ['Administrator', 'Accountant'])) { ?>
          <!--<button class="btn btn-primary" onclick="add('offline-payments', 'lg')">Pay Offline</button>-->
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-borderless">
              <thead>
                <tr>
                  <th><?= $student['Mode'] ?></th>
                  <th>Date</th>
                  <th>Particular</th>
                  <th>Source</th>
                  <th>Transaction ID</th>
                  <th class="text-right">Debit</th>
                  <th class="text-right">Credit</th>
                  <th class="text-right">Balance</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $balance = 0;
                $credit = 0;
                if ($_SESSION['university_id'] == 47) {
                  // echo "SELECT Student_Ledgers.* FROM Student_Ledgers WHERE Student_Ledgers.Student_ID = $id AND Student_Ledgers.Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At,Student_Ledgers.Duration ";die;
                  $ledgers = $conn->query("SELECT Student_Ledgers.* FROM Student_Ledgers WHERE Student_Ledgers.Student_ID = $id AND Student_Ledgers.Status = 1 AND Duration <= '" . $student['Duration'] . "' ORDER BY Student_Ledgers.Duration, Student_Ledgers.Type, Student_Ledgers.Created_At");
                } else {
                  $ledgers = $conn->query("SELECT Student_Ledgers.* FROM Student_Ledgers WHERE Student_Ledgers.Student_ID = $id AND Duration <= '" . $student['Duration'] . "' AND Student_Ledgers.Status = 1 ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_At");
                }
                $payments_arr = null;
                while ($ledger = $ledgers->fetch_assoc()) {
                  $credited = 0;
                  $student_count = '';
                  $payments_arr = array();
                  if ($ledger['Type'] == 3) {
                    $payments = $conn->query("SELECT Amount as received, Transaction_ID as art_id,  Wallet_Payments.Added_By FROM Wallet_Payments WHERE Transaction_ID='" . $ledger['Transaction_ID'] . "'");
                    $payments_arr = $payments->fetch_assoc();

                    $students = $conn->query("SELECT CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID FROM Wallet_Invoices LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID WHERE `User_ID` = " . $payments_arr['Added_By'] . " AND Invoice_No = '" . $payments_arr['art_id'] . "'  AND Wallet_Invoices.University_ID = " . $_SESSION['university_id'] . " ");
                    $student_name = array();
                    while ($student = mysqli_fetch_assoc($students)) {
                      $student_name[] = $student['std_ID'];
                    }
                    $student_count =  count($student_name);
                    if ($payments_arr !== null) {
                      $credited_val = $payments_arr['received'];

                      if ($student_count >  0) {
                        $credited = $credited_val / $student_count;
                      } else {
                        $credited = $payments_arr['received'];
                      }
                    }
                  } elseif ($ledger['Type'] == 2) {
                    if($ledger['Source']=='Wallet'){
                      $payments = $conn->query("SELECT Amount as received , Transaction_ID as art_id, Wallet_Payments.Added_By FROM Wallet_Payments WHERE Transaction_ID = '" . $ledger['Transaction_ID'] . "'");
                      $payments_arr = $payments->fetch_assoc();
                    }else{
                      $payments = $conn->query("SELECT Amount as received , Transaction_ID as art_id ,  Payments.Added_By FROM Payments WHERE Transaction_ID='" . $ledger['Transaction_ID'] . "'");
                      $payments_arr = $payments->fetch_assoc();
                    }

                    $students = $conn->query("SELECT CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE `User_ID` = " . $payments_arr['Added_By'] . " AND Invoice_No = '" . $payments_arr['art_id'] . "'  AND Invoices.University_ID = " . $_SESSION['university_id'] . " ");
                    $student_name = array();
                    while ($student = mysqli_fetch_assoc($students)) {
                      $student_name[] = $student['std_ID'];
                    }
                    $student_count =  count($student_name);

                    if ($payments_arr !== null) {
                      $credited_val = $payments_arr['received'];
                    }
                    if ($student_count >  0) {
                      $credited = $credited_val / $student_count;
                    } else {
                      $credited = $payments_arr['received'];
                    }
                  }

                ?>

                  <tr>

                    <td><?= $ledger['Duration'] ?></td>
                      <td><?= date("d-m-Y", strtotime($ledger['Date'])) ?></td>
                    <td><?= $ledger['Type'] == 1 ? "Due" : "Paid" ?></td>
                    <td><?= $ledger['Source'] ?></td>
                    <td><a href="/print/receipt/index.php?id=<?= $ledger['ID'] ?>&duration=<?= $ledger['Duration'] ?>" target="_blank"><u>
                          <?php if (!empty($payments_arr)) : ?>
                            <?= $payments_arr['art_id'] ?>
                          <?php endif; ?>
                        </u></a>
                    </td>
                    <td class="text-right">
                      <?php if ((int)$ledger['Fee']) {
                        $ledger_fee = $ledger['Fee'];
                      } else {
                        $ledger_fees = json_decode($ledger['Fee'], true);
                        $ledger_fee = reset($ledger_fees);
                      }

                      ?>
                      &#8377; <?= $ledger['Type'] == 1 ? number_format($ledger_fee, 2) : 0 ?>
                    </td>
                    <td class="text-right">&#8377; <?=  number_format($credited, 2) ?></td>
                    <td class="text-right">&#8377; <?= number_format($balance += ($ledger['Type'] == 1 ? (-1)*$ledger['Fee'] : $credited), 2) ?></td>
                  </tr>
                <?php

                  
                }
                ?>
              </tbody>

            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php }
?>