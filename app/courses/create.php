<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold"></span>Program</h5>
</div>
<form role="form" id="form-add-course" action="/app/courses/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>University</label>
          <select class="full-width" style="border: transparent;" name="university_id" onchange="getCourseType(this.value); getDepartments(this.value);">
            <option value="">Choose</option>
            <?php
            require '../../includes/db-config.php';
            $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE ID IS NOT NULL " . $_SESSION['UniversityQuery']);
            while ($university = $universities->fetch_assoc()) { ?>
              <option value="<?= $university['ID'] ?>"><?= $university['Name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Department</label>
          <select class="full-width" style="border: transparent;" id="department" name="department"">
          
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Program Type</label>
          <select class="full-width" style="border: transparent;" id="course_type" name="course_type"">
          
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Bachelor of Technology" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: B.Tech" required>
        </div>
      </div>
    </div>
  </div>
  <div class=" modal-footer clearfix text-end">
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
  function getCourseType(id) {
    $.ajax({
      url: '/app/courses/course-types?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#course_type').html(data);
      }
    });
  }

  function getDepartments(id) {
    $.ajax({
      url: '/app/courses/departments?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#department').html(data);
      }
    });
  }

  $(function() {
    $('#form-add-course').validate({
      rules: {
        name: {
          required: true
        },
        short_name: {
          required: true
        },
        university_id: {
          required: true
        },
        department: {
          required: true
        },
        course_type: {
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

  $("#form-add-course").on("submit", function(e) {
    if ($('#form-add-course').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
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
            $('#courses-table').DataTable().ajax.reload(null, false);
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
