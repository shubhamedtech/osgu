<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
    $id = intval($_GET['id']);
    $student = $conn->query("SELECT * FROM Himalayan_ID_Cards WHERE ID = $id");
    $student = mysqli_fetch_assoc($student);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Update ID Card</h5>
  </div>
  <form role="form" id="form-id-card" action="/id-cards/himalayan/update" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row clearfix">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Center</label>
            <select class="full-width" style="border: transparent;" name="center" id="center">
              <option value="">Select</option>
              <?php $users = $conn->query("SELECT ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users WHERE Role IN ('Center', 'Sub-Center')");
                while ($user = $users->fetch_assoc()){ ?>
                  <option value="<?=$user['ID']?>" <?php print $user['ID']==$student['Center_Code'] ? 'selected' : '' ?>><?=$user['Name']?></option>
              <?php }
              ?>
            </select>
          </div>
        </div>    
      </div>
      <div class="row clearfix">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Lot</label>
            <input type="text" name="lot" id="lot" value="<?=$student['Lot']?>" class="form-control" placeholder="Lot">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Session</label>
            <input type="text" name="session" id="session" value="<?=$student['Session']?>" class="form-control" placeholder="Session">
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="student_name" id="student_name" value="<?=$student['Name']?>" class="form-control" placeholder="Name">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Father Name</label>
            <input type="text" name="father_name" id="father_name" value="<?=$student['Father_Name']?>" class="form-control" placeholder="Father Name">
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Student ID</label>
            <input type="text" name="student_id" id="student_id" value="<?=$student['Student_ID']?>" class="form-control" placeholder="Student ID">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Course</label>
            <input type="text" name="course" id="course" value="<?=$student['Course']?>" class="form-control" placeholder="Course">
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Contact</label>
            <input type="text" name="contact" id="contact" value="<?=$student['Contact']?>" class="form-control" placeholder="Contact">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Address</label>
            <input type="text" name="address" id="address" value="<?=$student['Address']?>" class="form-control" placeholder="Address">
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Photo</label>
            <input type="file" name="photo" id="photo">
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
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
      $('#form-id-card').validate({
        rules: {
          lot: {required:true},
          session: {required:true},
          student_name: {required:true},
          father_name: {required:true},
          student_id: {required:true},
          course: {required:true},
          contact: {required:true},
          address: {required:true},
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

    $("#form-id-card").on("submit", function(e){
      e.preventDefault();
      if($('#form-id-card').valid()){
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
              window.location.reload(true);
            }else{
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
      }
    });
  </script>
  
<?php } ?>
