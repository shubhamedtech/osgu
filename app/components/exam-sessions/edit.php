<?php
ini_set('display_errors', 1);
if (isset($_GET['id'])) {
  require '../../../includes/db-config.php';

  $keyd = intval($_GET['id']);
  $exam_session = $conn->query("SELECT * FROM Exam_Sessions WHERE ID = $keyd");
  $exam_session = $exam_session->fetch_assoc();
  $admission_sessions = json_decode($exam_session['Admission_Session'], true);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Edit <span class="semi-bold">Exam Session</span></h6>
  </div>
  <form role="form" id="form-edit-exam-sessions" action="/app/components/exam-sessions/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" value="<?= $exam_session['Name'] ?>" class="form-control" placeholder="ex: Jan-22">
          </div>
        </div>
      </div>
      <?php foreach ($admission_sessions as $key => $value) {
        $uniqId = uniqid();
      ?>
        <div class="session_row id=" session_row_<?= $uniqId ?>"">
          <div class="dropdown-divider"></div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group form-group-default required">
                <label>For</label>
                <select class="full-width" style="border: transparent;" name="for[<?= $key ?>]" required>
                  <option value="">Choose</option>
                  <option value="Re-Registrations" <?= $value['for'] == 'Re-Registrations' ? 'selected' : '' ?>>Re-Reg</option>
                  <option value="Exam" <?= $value['for'] == 'Exam' ? 'selected' : '' ?>>Exam</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group form-group-default required">
                <label>From</label>
                <select class="full-width" style="border: transparent;" name="table[<?= $key ?>]" required>
                  <option value="">Choose</option>
                  <option value="Fresh" <?= $value['table'] == 'Fresh' ? 'selected' : '' ?>>Fresh</option>
                  <option value="Re-Reg" <?= $value['table'] == 'Re-Reg' ? 'selected' : '' ?>>Re-Reg</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group form-group-default required">
                <label>Session</label>
                <select class="full-width" style="border: transparent;" name="session[<?= $key ?>]">
                  <option value="">Choose</option>
                  <?php
                  $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = " . $exam_session['University_ID']);
                  while ($session = $sessions->fetch_assoc()) { ?>
                    <option value="<?= $session['ID'] ?>" <?= $value['session'] == $session['ID'] ? 'selected' : '' ?>><?= $session['Name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group form-group-default">
                <label>Admission Type</label>
                <select class="full-width" style="border: transparent;" name="admissionType[<?= $key ?>]">
                  <option value="">Choose</option>
                  <?php
                  $admissionTypes = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = " . $exam_session['University_ID']);
                  while ($admissionType = $admissionTypes->fetch_assoc()) { ?>
                    <option value="<?= $admissionType['ID'] ?>" <?= $value['admissionType'] == $admissionType['ID'] ? 'selected' : '' ?>><?= $admissionType['Name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group form-group-default required">
                <label>Duration</label>
                <input type="text" name="duration[<?= $key ?>]" class="form-control" value="<?= implode(",", $value['duration']) ?>" placeholder="ex: 1,2,3,4" required>
              </div>
            </div>
            <?php if ($key > 1) { ?>
              <div class="col-md-1 text-center p-t-15">
                <i class="uil uil-minus-square cursor-pointer" onclick="removeDiv('<?= $uniqId ?>')"></i>
              </div>
            <?php } else { ?>
              <div class="col-md-1 text-center p-t-15">
                <i class="uil uil-plus-square cursor-pointer" onclick="appendDiv()"></i>
              </div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
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
    function appendDiv() {
      var uniqid = $(".session_row").length + 1;
      var div = '<div class="session_row" id="session_row_' + uniqid + '">\
        <div class="dropdown-divider"></div>\
        <div class="row">\
          <div class="col-md-12">\
            <div class="form-group form-group-default required">\
              <label>For</label>\
              <select class="full-width" style="border: transparent;" name="for[' + uniqid + ']" required>\
                <option value="">Choose</option>\
                <option value="Re-Registrations">Re-Reg</option>\
                <option value="Exam">Exam</option>\
              </select>\
            </div>\
          </div>\
        </div>\
        <div class="row">\
          <div class="col-md-6">\
            <div class="form-group form-group-default required">\
              <label>From</label>\
              <select class="full-width" style="border: transparent;" name="table[' + uniqid + ']">\
                <option value="">Choose</option>\
                <option value="Fresh">Fresh</option>\
                <option value="Re-Reg">Re-Reg</option>\
              </select>\
            </div>\
          </div>\
          <div class="col-md-6">\
            <div class="form-group form-group-default required">\
              <label>Session</label>\
              <select class="full-width" style="border: transparent;" name="session[' + uniqid + ']" required>\
                <option value="">Choose</option>\
                <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = " . $exam_session['University_ID'] . ""); while ($session = $sessions->fetch_assoc()) { ?><option value="<?= $session['ID'] ?>"><?= $session['Name'] ?></option><?php } ?>\
              </select>\
            </div>\
          </div>\
        </div>\
        <div class="row">\
          <div class="col-md-6">\
            <div class="form-group form-group-default">\
              <label>Admission Type</label>\
              <select class="full-width" style="border: transparent;" name="admissionType[' + uniqid + ']">\
                <option value="">Choose</option>\
                <?php $admissionTypes = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = " . $exam_session['University_ID'] . ""); while ($admissionType = $admissionTypes->fetch_assoc()) { echo '<option value="' . $admissionType['ID'] . '">' . $admissionType['Name'] . '</option>'; } ?>\
              </select>\
            </div>\
          </div>\
          <div class="col-md-5">\
            <div class="form-group form-group-default required">\
              <label>Duration</label>\
              <input type="text" name="duration[' + uniqid + ']" class="form-control" required placeholder="ex: 1,2,3,4">\
            </div>\
          </div>\
          <div class="col-md-1 text-center p-t-15">\
            <i class="uil uil-minus-square cursor-pointer" onclick="removeDiv(' + uniqid + ')"></i>\
          </div>\
        </div>\
      </div>';
      $(".modal-body").append(div);
    }

    function removeDiv(id) {
      $("#session_row_" + id).remove();
    }
  </script>

  <script>
    $(function() {
      $('#form-edit-exam-sessions').validate({
        rules: {
          name: {
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

    $("#form-edit-exam-sessions").on("submit", function(e) {
      if ($('#form-edit-exam-sessions').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $exam_session['ID'] ?>');
        formData.append('university_id', '<?= $exam_session['University_ID'] ?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if (data.status == 200) {
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#tableExamSessions').DataTable().ajax.reload(null, false);
            } else {
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