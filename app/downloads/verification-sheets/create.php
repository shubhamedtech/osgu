<?php
  require '../../../includes/db-config.php';
  session_start();
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Upload Verification Sheets</h5>
</div>
<form role="form" id="" action="/app/downloads/verification-sheets/store" method="POST" enctype="multipart/form-data">
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

    <div class="row mt-4">
      <div class="col-md-12">
        <input type="file" name="file[]" accept="application/pdf" multiple><br>
        <span style="font-size:11px"><i>Upload upto 100 files named < Enrollment No >.pdf</i></span>
      </div>
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
    $('#form-upload-question').validate({
      rules: {
        "file[]": {required:true},
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
</script>
