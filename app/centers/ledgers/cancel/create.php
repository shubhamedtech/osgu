<?php if(isset($_GET['id']) && isset($_GET['center'])){ $id = intval($_GET['id']); $center = intval($_GET['center']); ?>
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Reason For Cancellation</h5>
  </div>
  <form role="form" id="form-cancellation" action="/app/centers/ledgers/cancel/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Reason</label>
          <textarea rows="6" name="reason" class="form-control" placeholder="" required></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix">
    <div class="m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Save</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script type="text/javascript">
  $(function(){
    $('#form-cancellation').validate();
  })

  $("#form-cancellation").on("submit", function(e){
    if($('#form-cancellation').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      formData.append('id', '<?=$id?>');
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
          if(data.status){
            $('.modal').modal('hide');
            notification('success', data.message);
            getLedger(<?=$center?>);
          }else{
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
