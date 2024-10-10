<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';

  $id = intval($_GET['id']);
  $user = $conn->query("SELECT Name, Code, Email, Mobile FROM Users WHERE ID = $id");
  $user = mysqli_fetch_assoc($user);
}
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Edit <span class="semi-bold">Sub-Counsellor</span></h5>
</div>
<form role="form" id="form-edit-sub-counsellors" action="/app/sub-counsellors/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Jhon Doe" value="<?= $user['Name'] ?>" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Employee ID</label>
          <input type="text" name="code" class="form-control" placeholder="ex: EM0001" value="<?= $user['Code'] ?>" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="ex: user@example.com" value="<?= $user['Email'] ?>" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Contact</label>
          <input type="tel" name="contact" class="form-control" placeholder="ex: 9998777655" onkeypress="return isNumberKey(event)" value="<?= $user['Mobile'] ?>" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label>Photo*</label>
        <input type="file" name="photo" accept="image/png, image/jpg, image/jpeg, image/svg">
      </div>

      <div class="col-md-6" id="logo-view"></div>
    </div>
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
  $(function() {
    $('#form-edit-sub-counsellors').validate({
      rules: {
        name: {
          required: true
        },
        code: {
          required: true
        },
        email: {
          required: true
        },
        contact: {
          required: true
        },
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

  $("#form-edit-sub-counsellors").on("submit", function(e) {
    if ($('#form-edit-sub-counsellors').valid()) {
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
            $('#users-table').DataTable().ajax.reload(null, false);
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
