<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_GET['id']);
  $sub_course = $conn->query("SELECT * FROM Sub_Courses WHERE id = $id");
  $sub_course = mysqli_fetch_assoc($sub_course);
?>
<link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Edit <span class="semi-bold">Specialization</span></h5>
</div>
<form role="form" id="form-edit-sub-course" action="/app/sub-courses/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">

    <!-- University & Course -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>University</label>
          <select class="full-width" style="border: transparent;" id="university_id" name="university_id" onchange="getDetails(this.value);">
            <option value="">Choose</option>
            <?php
            $university_query = $_SESSION['Role'] != 'Administrator' ? " AND ID =" . $_SESSION['university_id'] : '';
            $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE ID IS NOT NULL $university_query");
            while ($university = $universities->fetch_assoc()) { ?>
              <option value="<?= $university['ID'] ?>" <?php print $university['ID'] == $sub_course['University_ID'] ? 'selected' : '' ?>><?= $university['Name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Program</label>
          <select class="full-width" style="border: transparent;" id="course" name="course">
            <option value="">Choose</option>

          </select>
        </div>
      </div>
    </div>

    <!-- Name -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Mechanical Engineering" value="<?= $sub_course['Name'] ?>" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: ME" value="<?= $sub_course['Short_Name'] ?>" required>
        </div>
      </div>
    </div>

    <!-- Scheme & Mode -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Scheme</label>
          <select class="full-width" style="border: transparent;" id="scheme" name="scheme">

          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Mode</label>
          <select class="full-width" style="border: transparent;" id="mode" name="mode" onchange="getFeeSructures()">

          </select>
        </div>
      </div>
    </div>

    <!-- Eligibility -->
    <?php $eligibilities = array("High School", "Intermediate", "UG", "PG", "Other"); 
      $selected_eligibility = !empty($sub_course['Eligibility']) ? json_decode($sub_course['Eligibility'], true) : [];
    ?>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default form-group-default-select2 required">
          <label style="z-index:9999">Academic Eligibility</label>
          <select class=" full-width" data-init-plugin="select2" id="eligibilities" name="eligibilities[]" multiple>
            <?php foreach($eligibilities as $eligibility){ ?>
              <option value="<?=$eligibility?>" <?php echo in_array($eligibility, $selected_eligibility) ? 'selected' : '' ?>><?=$eligibility?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Duration -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Min Duration</label>
          <input type="tel" name="min_duration" id="min_duration" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)" onkeyup="getFeeSructures()" value="<?= $sub_course['Min_Duration'] ?>" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Max Duration</label>
          <input type="tel" name="max_duration" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)" value="<?= $sub_course['SOL'] ?>" required>
        </div>
      </div>
    </div>

    <div id="fee">

    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Lateral</label>
          <select class="full-width" style="border: transparent;" id="lateral" name="lateral">
            <option value="0" <?php print $sub_course['Lateral'] == 0 ? 'selected' : '' ?>>No</option>
            <option value="1" <?php print $sub_course['Lateral'] == 1 ? 'selected' : '' ?>>Yes</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>LE Start</label>
          <input type="text" id="le_start" name="le_start" class="form-control" placeholder="ex: 3,5" value="<?php print $sub_course['LE_Start'] == '' ? '' : $sub_course['LE_Start'] ?>">
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>LE SOL</label>
          <input type="tel" id="le_sol" name="le_sol" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)" value="<?php print $sub_course['LE_SOL'] == 0 ? '' : $sub_course['LE_SOL'] ?>">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Credit Transfer</label>
          <select class="full-width" style="border: transparent;" id="ct_transfer" name="ct_transfer">
            <option value="0" <?php print $sub_course['Credit_Transfer'] == 0 ? 'selected' : '' ?>>No</option>
            <option value="1" <?php print $sub_course['Credit_Transfer'] == 1 ? 'selected' : '' ?>>Yes</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>CT Start</label>
          <input type="tel" id="ct_start" name="ct_start" class="form-control" placeholder="ex: 3" onkeypress="return isNumberKey(event)" value="<?php print $sub_course['CT_Start'] == 0 ? '' : $sub_course['CT_Start'] ?>">
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>CT SOL</label>
          <input type="tel" id="ct_sol" name="ct_sol" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)" value="<?php print $sub_course['CT_SOL'] == 0 ? '' : $sub_course['CT_SOL'] ?>">
        </div>
      </div>
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
<script type="text/javascript" src="../../assets/plugins/select2/js/select2.full.min.js"></script>
<script>

  $(function(){
    $("#eligibilities").select2();
  })

  function getDetails(id) {
    $.ajax({
      url: '/app/sub-courses/courses?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#course').html(data);
        $('#course').val(<?= $sub_course['Course_ID'] ?>);
      }
    });

    $.ajax({
      url: '/app/sub-courses/schemes?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#scheme').html(data);
        $('#scheme').val(<?= $sub_course['Scheme_ID'] ?>);
      }
    });

    $.ajax({
      url: '/app/sub-courses/modes?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#mode').html(data);
        $('#mode').val(<?= $sub_course['Mode_ID'] ?>);
        getFeeSructures();
      }
    });
  }

  getDetails(<?= $sub_course['University_ID'] ?>);

  function getFeeSructures() {
    const id = '<?= $sub_course['ID'] ?>';
    const durations = $('#min_duration').val();
    const university_id = $('#university_id').val();
    const mode = $('#mode').val();
    $.ajax({
      url: '/app/sub-courses/fee-structures-edit?id=' + id + '&durations=' + durations + '&university_id=' + university_id + '&mode=' + mode,
      type: 'GET',
      success: function(data) {
        $('#fee').html(data);
      }
    });
  }

  $(function() {
    $('#form-edit-sub-course').validate({
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
        course: {
          required: true
        },
        scheme: {
          required: true
        },
        mode: {
          required: true
        },
        lateral: {
          required: true
        },
        ct_transfer: {
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

  $("#form-edit-sub-course").on("submit", function(e) {
    if ($('#form-edit-sub-course').valid()) {
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
            $('#sub-courses-table').DataTable().ajax.reload(null, false);
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
