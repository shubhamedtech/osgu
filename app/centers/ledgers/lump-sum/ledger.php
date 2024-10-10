<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);

  $invoices = $conn->query("SELECT Invoices.Created_At, SUM(Amount) as Amount, Invoice_No, COUNT(Student_ID) as Student_Count, GROUP_CONCAT(ID) as IDs FROM Invoices WHERE `User_ID` = $id AND University_ID = '" . $_SESSION['university_id'] . "' GROUP BY Invoice_No UNION SELECT Payments.Created_At, CAST(Payments.Amount AS SIGNED) as Amount, Gateway_ID as Invoice_No, Transaction_ID as Student_Count, Payment_Mode as IDs FROM Payments WHERE Status = 1 AND Added_By = $id AND University_ID = '" . $_SESSION['university_id'] . "' ORDER BY Created_At");
  if ($invoices->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No invoice(s) found!
      </div>
    </div>
  <?php } else { ?>

    <div class="row m-b-20">
      <div class="col-lg-12 d-flex justify-content-end">
        <div>
          <?php if (isset($_SESSION['gateway'])) { ?>
            <button type="button" class="btn btn-primary" onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')"> Pay Online</button>
          <?php } ?>
          <button type="button" class="btn btn-primary" onclick="add('offline-payments', 'lg')">Pay Offline</button>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Particular</th>
                <th>Transaction ID</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $balance = 0;
              while ($invoice = $invoices->fetch_assoc()) {
                $invoice_date = $invoice['Created_At'];
                $debit = $invoice['Amount'] < 0 ? (-1) * $invoice['Amount'] : 0;
                $credit = $invoice['Amount'] > 0 ? $invoice['Amount'] : 0;
                $balance = ($balance + $credit) - $debit;
              ?>
                <tr>
                  <td><?= $invoice_date ?></td>
                  <td><b><?= $invoice['Invoice_No'] ?></b></td>
                  <td><?php echo $invoice['Amount'] > 0 ? $invoice['Student_Count'] : '' ?></td>
                  <td><?= $debit ?></td>
                  <td><?= $credit ?></td>
                  <td><?= $balance ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php }
} ?>
