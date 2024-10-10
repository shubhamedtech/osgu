<?php if(isset($_GET['id'])){
  require '../../includes/db-config.php'; 
  $student_id = $conn->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = '".$_GET['id']."'");
  $student_id = mysqli_fetch_assoc($student_id);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Student ID</h5>
  </div>
  <form role="form" id="form-add-student-id" action="/app/universities/store-student-id" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Starting with</label>
            <input type="text" name="suffix" id="suffix" onkeyup="showStudentID()" value="<?php print !empty($student_id['ID_Suffix']) ? $student_id['ID_Suffix'] : '' ?>" class="form-control" placeholder="ex: XYZ" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>No. of Characters</label>
            <input type="number" min="4" max="10" onkeyup="showStudentID()" name="character" id="character" value="<?php print !empty($student_id['Max_Character']) ? $student_id['Max_Character'] : '' ?>" class="form-control" placeholder="ex: 5" required>
          </div>
        </div>
      </div>

      <div class="row" id="generated-student-id">
        <?php 
          if(!empty($student_id['ID_Suffix'])): echo '<p>Student ID: <b>'.$student_id['ID_Suffix'].str_repeat('X', $student_id['Max_Character']).'</b></p>'; endif;
        ?>
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
      $('#form-add-student-id').validate({
        rules: {
          suffix: {required:true},
          character: {required:true, max:10, min: 4},
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

    function showStudentID(){
      var suffix = $('#suffix').val();
      var value = $('#character').val();
      if(value>=4 && value<=10 && suffix!=''){
        var character = 'X';
        $('#generated-student-id').html('<p>Student ID: <b>'+suffix+character.repeat(value)+'</b></p>');
      }else{
        $('#generated-student-id').html('');
      }
      
    }

    $("#form-add-student-id").on("submit", function(e){
      if($('#form-add-student-id').valid()){
        var formData = new FormData(this);
        formData.append('id', '<?=$_GET['id']?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            $('.modal').modal('hide');
            $('#universities-table').DataTable().ajax.reload(null, false);
            if(data.status==200){
              notification('success', data.message);
            }else{
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      }
    });
  </script>
<?php } ?>
