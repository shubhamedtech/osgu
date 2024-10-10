<?php
if (isset($_GET['id'])) {
  session_start();
  require '../../includes/db-config.php';
  $student_id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($student_id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

  $pending = array();
  $pendency = $conn->query("SELECT ID, Pendency FROM Student_Pendencies WHERE Student_ID = $id AND Status = 0");
  if ($pendency->num_rows > 0) {
    $pendency = $pendency->fetch_assoc();
    $pending = !empty($pendency['Pendency']) ? json_decode($pendency['Pendency'], true) : [];
  }

  $eligibility = $conn->query("SELECT Eligibility FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.ID = $id");
  $eligibility = $eligibility->fetch_assoc();
  $eligibility = !empty($eligibility['Eligibility']) ? json_decode($eligibility['Eligibility'], true) : [];
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" onclick="hideReportModal()" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5><span class="semi-bold">Mark Pendency</span></h5>
  </div>
  <form role="form" id="report-pendency-form" action="/app/pendencies/store" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <?php
      $documents = array('Photo', 'Student Signature', 'Parent Signature', 'Aadhar', 'Affidavit', 'Migration', 'Other Certificate');

      $marksheets = array('High School' => 'High School Marksheet', 'Intermediate' => 'Intermediate Marksheet', 'UG' => 'Graduation Marksheet', 'PG' => 'Post Graduation Marksheet', 'Other' => 'Other Marksheet');
      foreach ($marksheets as $key => $value) {
        if (in_array($key, $eligibility)) {
          $documents[] = $value;
        }
      }

      foreach ($documents as $document) { ?>
        <div class="row">
          <div class="col-md-12 form-check complete">
            <input type="checkbox" <?php if (in_array($_SESSION['Role'], ['Center', 'Sub-Center'])) {
                                      echo 'onclick="return false;"';
                                    } ?> id="document_<?= str_replace(" ", "_", $document) ?>" name="report[]" value="<?= $document ?>" onclick="addRemark('<?= str_replace(" ", "_", $document) ?>')">
            <label for="document_<?= str_replace(" ", "_", $document) ?>" class="font-weight-bold">
              <?= $document ?>
            </label>
          </div>
          <div class="col-lg-12" id="remark_<?= str_replace(" ", "_", $document) ?>">

          </div>
        </div>
      <?php } ?>
    </div>
    <div class="modal-footer d-flex justify-content-between">
      <div class="m-t-10 sm-m-t-10">
        <?php if (!empty($pending)) { ?>
          <button onclick="destroy('pendencies', '<?= $pendency['ID'] ?>')" aria-label="" type="button" class="btn btn-danger btn-cons btn-animated from-left">
            <span>Remove</span>
            <span class="hidden-block">
              <i class="uil uil-trash"></i>
            </span>
          </button>
        <?php } ?>
      </div>
      <?php if (!in_array($_SESSION['Role'], ['Center', 'Sub-Center'])) { ?>
        <div class="m-t-10 sm-m-t-10">
          <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
            <span><?php echo !empty($pending) ? 'Update' : 'Report' ?></span>
            <span class="hidden-block">
              <i class="pg-icon">tick</i>
            </span>
          </button>
        </div>
      <?php } ?>
    </div>
  </form>

  <script>
    $(function() {
      $("#report-pendency-form").validate();
    })

    $("#report-pendency-form").on("submit", function(e) {
      if ($('#report-pendency-form').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
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
              $('.table').DataTable().ajax.reload(null, false);
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      }
    });

    function addRemark(id) {
      var input = '<div class="form-group form-group-default required">\
              <label>Remark</label>\
              <input type="text" class="form-control" id="remark_for_' + id + '" autocomplete="off" name="remark[' + id + ']" placeholder="" required />\
            </div>';
      if ($("#document_" + id).prop('checked') == true) {
        $("#remark_" + id).html(input);
      } else {
        $("#remark_" + id).html('');
      }
    }

    <?php foreach ($pending as $key => $value) { ?>
      $("#document_<?= $key ?>").prop('checked', true);
      addRemark('<?= $key ?>');
      $("#remark_for_<?= $key ?>").val('<?= $value ?>');
    <?php } ?>

    function hideReportModal() {
      $("#reportmodal").modal('hide');
    }
  </script>
<?php } ?>