<?php
if (isset($_POST['ids'])) {
  require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
  session_start();

  $ids = is_array($_POST['ids']) ? array_unique(array_filter($_POST['ids'])) : array();
  $totalFee = array();
  include 'calculate-fee.php';

  $amounts = $conn->query("SELECT sum(Amount) as total_amt FROM Wallets WHERE Added_By = " . $_SESSION['ID'] . " AND Status = 1 AND University_ID = " . $_SESSION['university_id']);
  $amounts = $amounts->fetch_assoc();

  //Debit Amount
  $debited_amount = 0;
  $debit_amts = $conn->query("SELECT sum(Amount) as debit_amt FROM Wallet_Payments WHERE Added_By = " . $_SESSION['ID'] . " AND Type = 3 AND University_ID = " . $_SESSION['university_id']);
  if ($debit_amts->num_rows > 0) {
    $debit_amt = $debit_amts->fetch_assoc();
    $debited_amount = $debit_amt['debit_amt'];
  }

  $walletBalance = $amounts['total_amt'] - $debited_amount;
?>
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Wallet Payment <span class="semi-bold"></span></h5>
  </div>
  <form role="form" id="form-add-offline-payments" action="/app/re-registrations/payment-methods/wallet-store" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12 mb-3">
          <h4>Summary</h4>
          <h6>Total Selected Students: <?= count($totalFee) ?> </h6>
          <h6>Total Fee: <?= array_sum($totalFee) ?> </h6>
          <h6>Wallet Balance: <?= $walletBalance ?></h6>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 mb-3 text-center">
          <?php if (array_sum($totalFee) > $walletBalance) {
            echo '<h5>Insufficient Wallet Balance!</h5>';
          } ?>
        </div>
      </div>
    </div>
    <?php if (array_sum($totalFee) <= $walletBalance) { ?>
      <div class="modal-footer clearfix text-end">
        <div class="col-md-4 m-t-10 sm-m-t-10">
          <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
            <span>Process</span>
            <span class="hidden-block">
              <i class="pg-icon">tick</i>
            </span>
          </button>
        </div>
      </div>
    <?php } ?>
  </form>

  <script>
    $("#form-add-offline-payments").validate();
    $("#form-add-offline-payments").on("submit", function(e) {
      if ($('#form-add-offline-payments').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('ids', '<?= implode(",", array_keys($totalFee)) ?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if (data.status == 200) {
              $('.modal').modal('hide');
              notification('success', data.message);
              updateTable();
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      }
    });
  </script>
<?php }
