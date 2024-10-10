<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);

  $invoices = $conn->query("SELECT SUM(Amount) as Amount, Invoice_No, COUNT(Student_ID) as Student_Count, GROUP_CONCAT(ID) as IDs FROM Invoices WHERE `User_ID` = $id AND University_ID = " . $_SESSION['university_id'] . " GROUP BY Invoice_No");
  if ($invoices->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No invoice(s) found!
      </div>
    </div>
  <?php } else { ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Invoice Date</th>
                <th>Invoice No.</th>
                <th>Amount</th>
                <th>No of Students</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($invoice = $invoices->fetch_assoc()) {
                $invoice_date = $conn->query("SELECT DATE_FORMAT(Created_At, '%d-%m-%Y') as Created_At FROM Invoices WHERE Invoice_No = '" . $invoice['Invoice_No'] . "'");
                $invoice_date = $invoice_date->fetch_assoc();
                $invoice_date = $invoice_date['Created_At'];
              ?>
                <tr>
                  <td><?= $invoice_date ?></td>
                  <td><b><?= $invoice['Invoice_No'] ?></b></td>
                  <td><?= "&#8377; " . (-1) * $invoice['Amount'] ?></td>
                  <td class="cursor-pointer" onclick="showStudents('<?= $invoice['IDs'] ?>')"><b><u><?= $invoice['Student_Count'] ?></u></b></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
<?php }
} ?>
