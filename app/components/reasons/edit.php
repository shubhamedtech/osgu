<?php 
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';
    session_start();
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $get_reason = $conn->query("SELECT Name,Stage_ID FROM Reasons WHERE ID = $id");
    $gc = mysqli_fetch_assoc($get_reason);
?>

<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h6>Edit <span class="semi-bold">Reason</span></h6>
</div>
<form id="edit_reasons_form" method="POST" action="/app/components/reasons/update">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Stage</label>
          <select class="full-width" style="border: transparent;" name="stage">
            <option value="">Choose</option>
            <?php
              $stages = $conn->query("SELECT ID, Name FROM Stages WHERE Status = 1");
              while($stage = $stages->fetch_assoc()) { ?>
                <option value="<?php echo $stage['ID']; ?>" <?php print $stage['ID']==$gc['Stage_ID'] ? ' selected' : '' ?>><?php echo $stage['Name']; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Reason</label>
          <input type="text" autocomplete="off" id="reason" name="reason" value="<?php echo $gc['Name'] ?>" class="form-control" placeholder="ex: No Call Yet" required>
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

    $('#edit_reasons_form').validate({
      rules: {
        stage: {required:true},
        reason: {required:true}
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

    $("#edit_reasons_form").on("submit", function(e){
      if($('#edit_reasons_form').valid()){
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
                $('#tableReasons').DataTable().ajax.reload(null, false);
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
