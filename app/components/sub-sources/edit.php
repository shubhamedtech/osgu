<?php 
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';
    session_start();
    $id = intval($_GET['id']);
    $sub_source = $conn->query("SELECT Name,Source_ID FROM Sub_Sources WHERE ID = $id");
    $sub_source = mysqli_fetch_assoc($sub_source);
?>

<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h6>Edit <span class="semi-bold">Sub-Source</span></h6>
</div>
<form id="edit_sub_sources_form" method="POST" action="/app/components/sub-sources/update">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Stage</label>
          <select class="full-width" style="border: transparent;" name="source" id="source">
            <option value="">Choose</option>
            <?php
              $sources = $conn->query("SELECT ID,Name FROM Sources WHERE Status = 1");
              while($source = $sources->fetch_assoc()) { ?>
                <option value="<?php echo $source['ID']; ?>" <?php print $source['ID']==$sub_source['Source_ID'] ? ' selected' : '' ?>><?php echo $source['Name']; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" autocomplete="off" id="sub_source" name="sub_source" placeholder="Popup Form" value="<?php echo $gc['Name'] ?>" class="form-control" required>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Save</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function(){

    $('#edit_sub_sources_form').validate({
      rules: {
        source: {required:true},
        sub_source: {required:true}
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

    $("#edit_sub_sources_form").on("submit", function(e){
      if($('#edit_sub_sources_form').valid()){
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
                $('#tableSubSources').DataTable().ajax.reload(null, false);
              }else{
                $(':input[type="submit"]').prop('disabled', false);
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
