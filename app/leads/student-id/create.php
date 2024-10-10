<?php
  if(isset($_GET['id'])){
    require '../../../includes/db-config.php';
    session_start();
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    $student_id = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE ID = $id");
    $student_id = mysqli_fetch_assoc($student_id);
    $student_id = $student_id['Unique_ID'];
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Student ID</h5>
  </div>
  <form role="form" id="form-student-id" action="/app/leads/student-id/store" method="POST">
    <div class="modal-body">
      <div class="row clearfix">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Student ID</label>
            <input type="text" name="unique_id" id="unique_id" value="<?php print !empty($student_id) ? $student_id : '' ?>" class="form-control" placeholder="">
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer flex justify-content-between">
      <div class="m-t-10 sm-m-t-10">
        <?php if(!empty($student_id)){ ?>
          <button aria-label="" type="button" onclick="deleteStudentID('<?=$_GET['id']?>')" class="btn btn-danger btn-cons btn-animated from-left">
            <span>Delete</span>
            <span class="hidden-block">
              <i class="uil uil-trash"></i>
            </span>
          </button>
        <?php } ?>
      </div>
      <div class="m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Update</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>


  <script type="text/javascript">
    $(function(){
      $('#form-student-id').validate({
        rules: {
          unique_id: {required:true},
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

    $("#form-student-id").on("submit", function(e){
      e.preventDefault();
      if($('#form-student-id').valid()){
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
            if(data.status==200){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#leads-table').DataTable().ajax.reload(null, false);
            }else{
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
      }
    });

    function deleteStudentID(id){
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/app/leads/student-id/destroy?id="+id,
            type: 'DELETE',
            dataType: 'json',
            success: function(data) {
              if(data.status==200){
                notification('success', data.message);
                $('.modal').modal('hide');
                $('#leads-table').DataTable().ajax.reload(null, false);
              }else{
                notification('danger', data.message);
              }
            }
          });
        }
      })
    }
  </script>
  
<?php } ?>
