<?php if (isset($_POST['ids']) && isset($_POST['amount']) && isset($_POST['center'])) {
  $ids = is_array($_POST['ids']) ? array_filter($_POST['ids']) : [];
  $amount = intval($_POST['amount']);
  $center = intval($_POST['center']);
?>
  <link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Add <span class="semi-bold">Wallet Payment</span></h5>
  </div>
  <form role="form" id="form-add-offline-payments" action="/app/wallet-payments/store-multiple" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Payable Amount</label>
            <input type="number" min="1" name="amount" id="amount" readonly value="<?= $amount ?>" class="form-control" placeholder="ex: 50000" required>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Pay</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>
  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>

  <script type="text/javascript">
    $("#transaction_date").mask("99-99-9999")
    $('#transaction_date').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      endDate: 'today',
    });
  </script>

  <script>
    $(function() {
      $('#form-add-offline-payments').validate({
        rules: {
          payment_type: {
            required: true
          },
          amount: {
            required: true
          },
          transaction_date: {
            required: true
          },
        },
        highlight: function(element) {
          $(element).addClass('error');
          $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function(element) {
          $(element).removeClass('error');
          $(element).closest('.form-control').removeClass('has-error');
        }
      });
    });

    function checkFileIsRequred(value) {
      if (value == 'Cash') {
        fileNotRequired();
      } else {
        fileRequired();
      }
    }

    function fileRequired() {
      $('.cash').addClass('required');
      $('#transaction_id').validate();
      $('#transaction_id').rules('add', {
        required: true
      });
      $('#bank_name').validate();
      $('#bank_name').rules('add', {
        required: true
      });
      $('#file-required').html('*');
      $('#file').validate();
      $('#file').rules('add', {
        required: true
      });
    }

    function fileNotRequired() {
      $('.cash').removeClass('required');
      $('#file-required').html('');
      $('#file').validate();
      $('#transaction_id').rules('remove', 'required');
      $('#bank_name').rules('remove', 'required');
      $('#file').rules('remove', 'required');
    }


    $("#form-add-offline-payments").on("submit", function(e) {
      if ($('#form-add-offline-payments').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('ids', '<?= implode("|", $ids) ?>');
        formData.append('amount', '<?= $amount ?>');
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
              getLedger('<?= $center ?>');
              $('#payments-table').DataTable().ajax.reload(null, false);
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
<?php } ?>
