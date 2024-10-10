<?php
  require '../../../includes/db-config.php';
  session_start();
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Upload Date-Sheet</h5>
</div>
<form role="form" id="form-upload-date-sheet" action="/app/downloads/date-sheets/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">

    <?php if($_SESSION['Role']=='Administrator'){ ?>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>University</label>
            <select class="full-width" style="border: transparent;" name="university_id">
              <option value="">Choose</option>
              <?php
                $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Universities ORDER BY Short_Name ASC");
                while($university = $universities->fetch_assoc()) { ?>
                  <option value="<?=$university['ID']?>"><?=$university['University']?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
    <?php } ?>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: File Name" required>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-12">
        <input type="file" name="file" accept=".zip,.rar,.7zip,application/pdf">
      </div>

      <div class="col-md-6" id="logo-view"></div>
    </div>  
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Upload</span>
        <span class="hidden-block">
          <i class="uil uil-export"></i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function(){
    $('#form-upload-date-sheet').validate({
      rules: {
        name: {required:true},
        file: {required:true},
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $("#form-upload-date-sheet").on("submit", function(e){
    if($('#form-upload-date-sheet').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
              if(data.status==200){
                $('.modal').modal('hide');
                notification('success', data.message);
                $('#date-sheets-table').DataTable().ajax.reload(null, false);
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
