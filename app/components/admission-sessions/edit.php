<?php if(isset($_GET['id'])){
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);
  $admission_session = $conn->query("SELECT ID, Name, Exam_Session, Scheme_ID, University_ID FROM Admission_Sessions WHERE ID = $id");
  if($admission_session->num_rows>0){
    $admission_session = mysqli_fetch_assoc($admission_session);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Edit <span class="semi-bold">Admission Sessions</span></h6>
  </div>
  <form role="form" id="form-edit-admission-sessions" action="/app/components/admission-sessions/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $admission_session['Name'] ?>" placeholder="ex: Jan-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Exam Session</label>
            <input type="text" name="exam_session" class="form-control" value="<?php echo $admission_session['Exam_Session'] ?>" placeholder="ex: July-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Scheme</label>
            <select class="full-width" style="border: transparent;" name="scheme">
              <option value="">Choose</option>
              <?php
                $schemes = $conn->query("SELECT ID, Schemes.Name FROM Schemes WHERE Schemes.Status = 1 AND University_ID = ".$admission_session['University_ID']."");
                while($scheme = $schemes->fetch_assoc()) { ?>
                  <option value="<?=$scheme['ID']?>" <?php print $scheme['ID']==$admission_session['Scheme_ID'] ? 'selected' : '' ?>><?=$scheme['Name']?></option>
              <?php } ?>
            </select>
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
      $('#form-edit-admission-sessions').validate({
        rules: {
          name: {required:true},
          exam_session: {required:true},
          scheme: {required:true},
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

    $("#form-edit-admission-sessions").on("submit", function(e){
      if($('#form-edit-admission-sessions').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$admission_session['University_ID']?>');
        formData.append('id', '<?=$admission_session['ID']?>');
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
              $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
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
<?php }} ?>
