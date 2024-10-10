<?php if(isset($_GET['university_id'])){
  require '../../../includes/db-config.php';
  ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Add <span class="semi-bold">Exam Session</span></h6>
  </div>
  <form role="form" id="form-add-exam-sessions" action="/app/components/exam-sessions/store" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="ex: Jan-22">
          </div>
        </div>
      </div>
      <div class="row session_row">
        <div class="col-md-3">
          <div class="form-group form-group-default required">
            <label>Session</label>
            <select class="full-width" style="border: transparent;" name="session[1]">
              <option value="">Choose</option>
              <?php
                $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE Status = 1 AND University_ID = ".$_GET['university_id']."");
                while($session = $sessions->fetch_assoc()) { ?>
                  <option value="<?=$session['ID']?>"><?=$session['Name']?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group form-group-default required">
            <label>Admission Type</label>
            <select class="full-width" style="border: transparent;" name="admission_type[1]">
              <option value="">Choose</option>
              <?php
                $types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE Status = 1 AND University_ID = ".$_GET['university_id']."");
                while($type = $types->fetch_assoc()) { ?>
                  <option value="<?=$type['ID']?>"><?=$type['Name']?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group form-group-default required">
            <label>Semesters</label>
            <input type="text" name="semesters[1]" class="form-control" placeholder="ex: 1,2,3,4">
          </div>
        </div>
        <div class="col-md-2 text-center p-t-15">
          <i class="uil uil-plus-square" onclick="appendDiv()"></i>
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
    function appendDiv(){
      var uniqid = $(".session_row").length+1;
      var div = '<div class="row session_row" id="session_row_'+uniqid+'">\
        <div class="col-md-3">\
          <div class="form-group form-group-default required">\
            <label>Session</label>\
            <select class="full-width" style="border: transparent;" name="session['+uniqid+']" required>\
              <option value="">Choose</option>\
              <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE Status = 1 AND University_ID = ".$_GET['university_id'].""); while($session = $sessions->fetch_assoc()) { ?><option value="<?=$session['ID']?>"><?=$session['Name']?></option><?php } ?>\
            </select>\
          </div>\
        </div>\
        <div class="col-md-4">\
          <div class="form-group form-group-default required">\
            <label>Admission Type</label>\
            <select class="full-width" style="border: transparent;" name="admission_type['+uniqid+']">\
              <option value="">Choose</option>\
              <?php $types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE Status = 1 AND University_ID = ".$_GET['university_id'].""); while($type = $types->fetch_assoc()) { ?> <option value="<?=$type['ID']?>"><?=$type['Name']?></option> <?php } ?>\
            </select>\
          </div>\
        </div>\
        <div class="col-md-3">\
          <div class="form-group form-group-default required">\
            <label>Semesters</label>\
            <input type="text" name="semesters['+uniqid+']" class="form-control" required placeholder="ex: 1,2,3,4">\
          </div>\
        </div>\
        <div class="col-md-2 text-center p-t-15">\
          <i class="uil uil-minus-square" onclick="removeDiv('+uniqid+')"></i>\
        </div>\
      </div>';
      $(".modal-body").append(div);
    }

    function removeDiv(id) {
      $("#session_row_"+id).remove();
    }
  </script>

  <script>
    $(function(){
      $('#form-add-exam-sessions').validate({
        rules: {
          name: {required:true},
          'session[1]' : {required:true},
          'semesters[1]' : {required:true},
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

    $("#form-add-exam-sessions").on("submit", function(e){
      if($('#form-add-exam-sessions').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$_GET['university_id']?>');
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
              $('#tableExamSessions').DataTable().ajax.reload(null, false);
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
<?php } ?>
