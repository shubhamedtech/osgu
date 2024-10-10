<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold"></span>Offline Payment</h5>
</div>
<form role="form" id="form-add-offline-payments" action="/app/offline-payments/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Payment Type</label>
          <select class="full-width" style="border: transparent;" onchange="checkFileIsRequred(this.value)" name="payment_type" required>
            <option value="">Select</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cheque">Cheque</option>
            <option value="DD">DD</option>
            <option value="UPI">UPI</option>
            <option value="Cash Deposit in Bank">Cash Deposit in Bank</option>
            <option value="Cash">Cash</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default cash">
          <label>Bank Name</label>
          <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="ex: Axis">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default cash">
          <label>Transaction ID</label>
          <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="ex: ABC123XXXX">
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Amount</label>
          <input type="number" min="1" name="amount" id="amount" class="form-control" placeholder="ex: 50000" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Date</label>
          <input type="tel" name="transaction_date" class="form-control" placeholder="dd-mm-yyyy" id="transaction_date" required>
        </div>
      </div>

      <div class="col-md-6">
        <label>File<span id="file-required">*</span></label>
        <input type="file" id="file" name="file" accept="image/png, image/jpg, image/jpeg, application/pdf">
      </div>
    </div>

  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Add</span>
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
      if ($("#student").length > 0) {
        formData.append("student_id", $("#student").val());
      }
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
