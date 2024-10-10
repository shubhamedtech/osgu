<?php
if (isset($_POST['id']) && isset($_POST['name'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = str_replace("W1Ebt1IhGN3ZOLplom9I", "", base64_decode($_POST['id']));
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $university_id = mysqli_real_escape_string($conn, $_POST['university_id']);
  date_default_timezone_set("Asia/Kolkata");
?>

  <link href="../../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../../assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" media="screen">
  <style>
    input[type="time"]::-webkit-calendar-picker-indicator {
      background: none;
    }
  </style>
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Add Follow-Up For <?= ucwords(strtolower($name)) ?></h5>
  </div>
  <form id="followup_form" method="POST" action="/app/follow-ups/store">
    <div class="modal-body">

      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Stage</label>
            <select class="full-width" style="border: transparent;" name="stage" id="stage" onchange="getReasons(this.value)" required>
              <option value="">Choose</option>
              <?php $stages = $conn->query("SELECT ID, Name FROM Stages WHERE Status = 1");
              while ($stage = $stages->fetch_assoc()) { ?>
                <option value="<?= $stage['ID'] ?>"><?= $stage['Name'] ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Reason</label>
            <select class="full-width" style="border: transparent;" name="reason" id="reason" required>
              <option value="">Choose</option>

            </select>
          </div>
        </div>
      </div>


      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Remark</label>
            <textarea class="form-control" name="remark" id="remark" rows="3" required></textarea>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12 pb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="addFollowUpDate" onclick="followupMessage()" checked id="addFollowUpDate">
            <label class="form-check-label" for="addFollowUpDate"><span id="followupMessage">Add Follow-Up</span></label>
          </div>
        </div>
        <div class="form-group form-group-default input-group datetime col-md-12">
          <div class="form-input-group">
            <label>Date</label>
            <input type="text" onkeypress="return isNumberKey(event);" name="followup-date" value="<?= date("d-m-Y") ?>" class="form-control" placeholder="Pick a date" id="followup-date">
          </div>
          <div class="input-group-append ">
            <span class="input-group-text"><i class="pg-icon">calendar</i></span>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="form-group form-group-default datetime input-group col-md-12">
          <div class="form-input-group">
            <label>Time</label>
            <input type="time" value="<?= date('H:m') ?>" class="form-control" onfocus="this.showPicker()" id="followup-time" name="followup-time">
          </div>
          <div class="input-group-append ">
            <span class="input-group-text"><i class="pg-icon">time</i></span>
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
  <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="../../assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
  <script type="text/javascript" src="../../assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
  <script>
    $(function() {

      $("#followup-date").mask("99-99-9999")
      $('#followup-date').datepicker({
        format: 'dd-mm-yyyy',
        startDate: new Date(),
        setDate: new Date(),
        autoclose: true
      });

      $("#followup_form").on("submit", function(e) {
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
        formData.append('university_id', '<?= $university_id ?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#leads-table').DataTable().ajax.reload(null, false);
              $('#followups-table').DataTable().ajax.reload(null, false);
            } else {
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      });
    });

    function getReasons(id) {
      $.ajax({
        url: '/app/leads/stage_reasons?stage_id=' + id,
        type: 'GET',
        success: function(data) {
          $('#reason').html(data);
        }
      })
    }
  </script>

  <script type="text/javascript">
    function followupMessage() {
      if ($("#addFollowUpDate").prop('checked') == true) {
        $('#followupMessage').html('Add Follow-Up');
        $(".datetime").css("display", "flex");
      } else {
        $('#followupMessage').html('Close Follow-Up with <?= $name ?>');
        $(".datetime").css("display", "none");
      }
    }
  </script>
<?php } ?>
