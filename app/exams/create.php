<?php
  require '../../includes/db-config.php';
  session_start();
?>
<link href="../../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add Exam</h5>
</div>
<form role="form" id="form-exams" foemtarget="_blank" action="/app/exams/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Exam Type</label>
          <select class="full-width" style="border: transparent;" name="exam_type" required>
            <option value="">Choose</option>
            <option value="1">MCQs</option>
            <option value="2">File Upload</option>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Main Exam" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Exam Session</label>
          <select class="full-width" style="border: transparent;" name="exam_session" required>
            <option value="">Choose</option>
            <?php
              $exam_sessions = $conn->query("SELECT ID, Name FROM Exam_Sessions WHERE University_ID = ".$_SESSION['university_id']);
              while($exam_session = $exam_sessions->fetch_assoc()) { ?>
                <option value="<?=$exam_session['ID']?>"><?=$exam_session['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Start Date</label>
          <input type="tel" name="exam_start_date" id="exam_start_date" class="form-control" placeholder="dd-mm-yyyy" onchange="setEndDate(this.value);" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>End Date</label>
          <input type="tel" name="exam_end_date" id="exam_end_date" class="form-control" placeholder="dd-mm-yyyy" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Start Time</label>
          <input type="time" name="exam_start_time" id="exam_start_time" class="form-control" placeholder="HH:mm" onchange="setEndTime(this.value);" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>End Time</label>
          <input type="time" name="exam_end_time" id="exam_end_time" class="form-control" placeholder="HH:mm" required>
        </div>
      </div>
    </div>


  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Add</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="../../assets/plugins/bootstrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>

<script type="text/javascript">
  $("#exam_start_date").mask("99-99-9999");
  $("#exam_end_date").mask("99-99-9999");

  $('#exam_start_date').datepicker({
    format: 'dd-mm-yyyy',
    autoclose: true,
    startDate: '+0d'
  });

  function setEndDate(value){
    $('#exam_end_date').val(value);
    $('#exam_end_date').datepicker('remove');
    $('#exam_end_date').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      startDate: new Date(value.split("-").reverse().join("-"))
    });
  }

  function setEndTime(value){
    $("#exam_end_time").attr('min', value);
  }
</script>

<script type="text/javascript">
  $(function(){
    $('#form-exams').validate();
  })

  $("#form-exams").on("submit", function(e){
    if($('#form-exams').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
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
            $('#courses-table').DataTable().ajax.reload(null, false);
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