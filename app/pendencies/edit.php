<?php if (isset($_GET['id'])) {
  session_start();
  require '../../includes/db-config.php';

  $student_id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($student_id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
?>
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5><span class="semi-bold">Upload Documents</span></h5>
  </div>
  <form role="form" id="upload-pendency-form" action="/app/pendencies/update" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <?php $pendency = $conn->query("SELECT * FROM Student_Pendencies WHERE Student_ID = $id AND Status = 0");
      if ($pendency->num_rows > 0) {
        $pendency = $pendency->fetch_assoc();
        $pending = !empty($pendency['Pendency']) ? json_decode($pendency['Pendency'], true) : [];

        $multiple = array('High_School_Marksheet', 'Intermediate_Marksheet', 'Graduation_Marksheet', 'Post_Graduation_Marksheet', 'Other_Marksheet', 'Affidavit', 'Migration', 'Other_Certificate', 'Aadhar');
        foreach ($pending as $key => $value) { ?>
          <div class="row m-b-20">
            <div class="col-md-12">
              <h6><?= str_replace("_", " ", $key); ?></h6>
              <input type="file" name="<?= strtolower($key) ?><?php echo in_array($key, $multiple) ? '[]' : '' ?>" <?php echo in_array($key, $multiple) ? 'multiple' : '' ?> required>
              <p class="text-info m-t-10 font-weight-bold"><i class="uil uil-info-circle m-r-10"></i><?= $value ?></p>
            </div>
          </div>
      <?php }
      } else {
        echo '<h4 class="text-center m-t-20">No Pendency!!!</h4>';
      }
      ?>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Upload</span>
          <span class="hidden-block">
            <i class="uil uil-upload"></i>
          </span>
        </button>
      </div>
    </div>
  </form>
  <script>
    $(function() {
      $("#upload-pendency-form").validate();
    })

    $("#upload-pendency-form").on("submit", function(e) {
      if ($('#upload-pendency-form').valid()) {
        $(':input[type="submit"]').prop('disabled', false);
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
  </script>

<?php
} ?>
