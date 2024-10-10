<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  $id = intval($_GET['id']);
  $department = $conn->query("SELECT * FROM Departments WHERE ID = $id");
  $department = $department->fetch_assoc();
}
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Edit <span class="semi-bold">Department</span></h5>
</div>
<form role="form" id="form-edit-department" action="/app/departments/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>University</label>
          <select class="full-width" style="border: transparent;" name="university_id" onchange="getCourseType(this.value);">
            <option value="">Choose</option>
            <?php
            $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE ID IS NOT NULL " . $_SESSION['UniversityQuery']);
            while ($university = $universities->fetch_assoc()) { ?>
              <option value="<?= $university['ID'] ?>" <?php print $university['ID'] == $department['University_ID'] ? 'selected' : '' ?>><?= $university['Name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Department of Engineering & Technology" value="<?= $department['Name'] ?>" required>
        </div>
      </div>
    </div>
  </div>
  <div class=" modal-footer clearfix text-end">
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
  $(function() {
    $('#form-edit-department').validate({
      rules: {
        name: {
          required: true
        },
        university_id: {
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

  $("#form-edit-department").on("submit", function(e) {
    if ($('#form-edit-department').valid()) {
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
            $('#departments-table').DataTable().ajax.reload(null, false);
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
