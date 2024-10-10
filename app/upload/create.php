<?php
  if(isset($_GET['column']) && isset($_GET['id']) && isset($_GET['table'])){
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Upload <?=$_GET['column']?></h5>
</div>
<form role="form" id="form-upload" action="/app/upload/store" enctype="multipart/form-data">
  <div class="modal-body">    
    <div class="row mb-4 file-row">
      <div class="col-md-12 d-flex justify-content-between">
        <div>
          <input name="file[1]" type="file" accept="image/*, .pdf, .csv, .doc, .docx, application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
        </div>
        <div class="text-right">
          <span class="cursor-pointer" onclick="appendFileUpload()"><u>Add More</u></span>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Upload</span>
        <span class="hidden-block">
          <i class="uil uil-upload"></i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function(){
    $('#form-upload').validate({
      rules: {
        'file[1]': {required:true},
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

<script type="text/javascript">
  function appendFileUpload(){
    var uniqid  = $(".file-row").length+1;
    var div = '<div class="row mb-4 file-row" id="file_row_'+uniqid+'">\
      <div class="col-md-12 d-flex justify-content-between">\
        <div>\
          <input name="file['+uniqid+']" id="file_'+uniqid+'" type="file" accept="image/*, .pdf, .csv, .doc, .docx, application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required />\
        </div>\
        <div class="text-right">\
          <i class="uil uil-minus-square cursor-pointer" onclick="removeFileUpload('+uniqid+')"></i>\
        </div>\
      </div>\
    </div>';
    $(".modal-body").append(div);
  }

  function removeFileUpload(id){
    $("#file_row_"+id).remove();
  }
</script>

<script type="text/javascript">
  $("#form-upload").on("submit", function(e){
    e.preventDefault();
    if($("#form-upload").valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      formData.append("id", '<?=$_GET['id']?>');
      formData.append("column", '<?=$_GET['column']?>');
      formData.append("table", '<?=$_GET['table']?>');
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
            getTable();
          }else{
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', data.message);
          }
        },
        each: function(data) {
          $(':input[type="submit"]').prop('disabled', false);
          notification('danger', 'Something went wrong!');
        }
      });
    }
  });
</script>

<?php }