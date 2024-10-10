<?php if (isset($_POST['id']) && isset($_POST['type'])) {
  require '../../../includes/db-config.php';

  $id = intval($_POST['id']);
  $type = mysqli_real_escape_string($conn, $_POST['type']);
  $column = $type == 'RR' ? 'RR_Last_Date' : 'BP_Last_Date';

  $lastDate = $conn->query("SELECT $column FROM Exam_Sessions WHERE ID = $id");
  $lastDate = $lastDate->fetch_assoc();
  $lastDate = !empty($lastDate[$column]) ? date("d-m-Y", strtotime($lastDate[$column])) : '';
?>
  <!-- Modal -->
  <link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Update <span class="semi-bold">Last Date for </span></h6>
  </div>
  <form role="form" id="lastDateForm" action="/app/components/exam-sessions/update-last-date" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="form-group form-group-default required">
          <label>Last Date</label>
          <input type="text" name="lastDate" id="lastDate" value="<?= $lastDate ?>" class="form-control" placeholder="ex: dd-mm-yyyy" required>
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
  <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script>
    $(function() {
      $("#lastDate").mask("99-99-9999");
      $("#lastDate").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        startDate: '+1d',
      });

      $('#lastDateForm').validate({
        rules: {
          lastDate: {
            required: true
          }
        },
        highlight: function(element) {
          $(element).addClass('error');
          $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function(element) {
          $(element).removeClass('error');
          $(element).closest('.form-control').removeClass('has-error');
        }
      });
    })

    $("#lastDateForm").on("submit", function(e) {
      e.preventDefault();
      if ($('#lastDateForm').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
        formData.append('type', '<?= $type ?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if (data.status) {
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#tableExamSessions').DataTable().ajax.reload(null, false);
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
      }
    });
  </script>
<?php } ?>