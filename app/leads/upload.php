<div class="modal-header">
  <h5 class="modal-title" id="myCenterModalLabel">Upload Leads</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="upload_lead_form" method="POST" action="ajax_admin/ajax_leads/upload_store" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="form-group row">
      <div class="col-lg-12 text-end">
        <span class="text-end"><a href="/assets/sample-files/lead_sample" target="_blank"><i class="uil uil-file-download-alt"></i> Download Sample</a></span>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-lg-12">
        <label class="control-label">Upload File with prospect Lead's Details</label>
        <input type="file" name="lead_file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="uploadButton">Upload</button>
  </div>
</form>

<script>
  $(function(){
    $("#upload_lead_form").on("submit", function(e){
        $(':input[type="submit"]').prop('disabled', true);
        $('#uploadButton').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');
        var formData = new FormData(this);
        $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            dataType:'json',
            success: function(data) {
              if(data.status==200){
                $('.modal').modal('hide');
                $('#leads-table').DataTable().ajax.reload(null, false);
                window.location.href = 'ajax_admin/ajax_leads/'+data.file;
              }else{
                toastr.error(data.message);
              }
            }
        });
        e.preventDefault();
    });
});
</script>
