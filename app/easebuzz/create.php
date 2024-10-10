<?php session_start(); ?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Pay Online</h5>
</div>
<form role="form" id="payment-form" action="/app/easebuzz/pay">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Amount</label>
          <input type="number" step="1" min="1" name="amount" id="amount" autocomplete="off" class="form-control" required>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Pay</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script src="https://ebz-static.s3.ap-south-1.amazonaws.com/easecheckout/easebuzz-checkout.js"></script>
<script type="text/javascript">
  $(function() {
    $('#payment-form').validate();
  })

  $("#payment-form").on("submit", function(e) {
    if ($('#payment-form').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
          if (data.status == 1) {
            $('.modal').modal('hide');
            initiatePayment(data.data)
          } else {
            notification('danger', data.error);
          }
        }
      });
      e.preventDefault();
    }
  });

  function initiatePayment(data) {
    var easebuzzCheckout = new EasebuzzCheckout('<?= $_SESSION['access_key'] ?>', 'prod')
    var options = {
      access_key: data,
      dataType: 'json',
      onResponse: (response) => {
        updatePayment(response);
        if (response.status == 'success') {
          Swal.fire({
            title: 'Thank You!',
            text: "Your payment is successfull!",
            icon: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.isConfirmed) {

            }
          })
        } else {
          Swal.fire(
            'Payment Failed',
            'Please try again!',
            'error'
          )
        }
      },
      theme: "#272B35" // color hex
    }
    easebuzzCheckout.initiatePayment(options);
  }

  function updatePayment(response) {
    $.ajax({
      url: '/app/easebuzz/response',
      type: 'POST',
      data: {
        response
      },
      dataType: 'json',
      success: function(response) {
        if (response.status) {
          $('#payments-table').DataTable().ajax.reload(null, false);
        } else {
          notification('danger', data.message);
        }
      },
      error: function(response) {
        console.error(response);
      }
    })
  }
</script>
