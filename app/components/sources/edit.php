<?php 
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';
    session_start();
    $id = intval($_GET['id']);
    $source = $conn->query("SELECT Name FROM Sources WHERE ID = $id");
    $source = mysqli_fetch_assoc($source);
?>

<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h6>Edit <span class="semi-bold">Source</span></h6>
</div>
<form id="edit_source_form" method="POST" action="/app/components/sources/update">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" autocomplete="off" class="form-control" id="source" name="source" value="<?php echo $source['Name'] ?>" placeholder="Landing Page">
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Update</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function(){

    $('#edit_source_form').validate({
      rules: {
        source: {required:true}
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

    $("#edit_source_form").on("submit", function(e){
      if($('#edit_source_form').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?php echo $id ?>');
        $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if(data.status==200){
                $('.modal').modal('hide');
                notification('success', data.message);
                $('#tableSources').DataTable().ajax.reload(null, false);;
              }else{
                notification('danger', data.message);
              }
            }
        });
        e.preventDefault();
      }
    });
});
</script>
<?php } ?>
