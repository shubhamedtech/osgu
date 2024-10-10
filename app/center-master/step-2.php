<?php
if (isset($_POST['id']) && isset($_POST['university_id']) && isset($_POST['name'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = intval($_POST['id']);
  $university_id = intval($_POST['university_id']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $counsellor_id = '';
  $sub_counsellor_id = '';
  $durations = '';
  $alloted_counsellor_id = '';
  $alloted_sub_counsellor_id = '';

  $university_is_vocational = 0;
  $is_vocational = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Is_Vocational = 1");
  if ($is_vocational->num_rows > 0) {
    $university_is_vocational = 1;
  }

  $alloted_counsellor = $conn->query("SELECT Counsellor_ID FROM Alloted_Center_To_Counsellor WHERE `Code` = $id AND University_ID = $university_id");
  if ($alloted_counsellor->num_rows > 0) {
    $alloted_counsellor = mysqli_fetch_assoc($alloted_counsellor);
    $alloted_counsellor_id = $alloted_counsellor['Counsellor_ID'];
  }

  $alloted_sub_counsellor = $conn->query("SELECT Sub_Counsellor_ID FROM Alloted_Center_To_SubCounsellor WHERE `Code` = $id AND University_ID = $university_id");
  if ($alloted_sub_counsellor->num_rows > 0) {
    $alloted_sub_counsellor = mysqli_fetch_assoc($alloted_sub_counsellor);
    $alloted_sub_counsellor_id = $alloted_sub_counsellor['Sub_Counsellor_ID'];
  }

?>
  <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <p>
      <?php if ($_SESSION['Role'] == 'Administrator') { ?>
        <!-- Back Button -->
        <span class="pull-left link text-color cursor-pointer" onclick="allot(<?= $id ?>, 'lg')"><i class="uil uil-arrow-left"></i> Back</span>
      <?php } ?>
      <span class="pull-right"><button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i></button></span>
    </p>

    <h5>Allot <span class="semi-bold"><?= $name ?></span></h5>

    <!-- Delete Button -->
    <?php if (!empty($alloted_counsellor_id)) { ?>
      <p class="close link text-danger small cursor-pointer" style="margin-top:40px;" onclick="removeAllotment()"><i class="uil uil-trash"></i> Remove</p>
    <?php } ?>

  </div>
  <form role="form" id="form-allot-center-master" action="/app/center-master/allot<?php print $university_is_vocational == 1 ? '-vocational' : '' ?>" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Counsellor</label>
            <select class="full-width" style="border: transparent;" name="counsellor" id="counsellor" onchange="getSubCounsellor();">
              <option value="">Select</option>
              <?php
              $counsellors = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' AND University_User.University_ID = " . $university_id);
              while ($counsellor = $counsellors->fetch_assoc()) { ?>
                <option value="<?= $counsellor['ID'] ?>" <?php print $counsellor['ID'] == $alloted_counsellor_id ? 'selected' : '' ?>><?= $counsellor['Name'] ?></option>
              <?php }
              ?>
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group form-group-default">
            <label>Sub-Counsellor</label>
            <select class="full-width" style="border: transparent;" name="sub_counsellor" id="sub_counsellor">
              <option value="">Select</option>
            </select>
          </div>
        </div>
      </div>

      <div id="fee">

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
  <script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
  <script>
    function getSubCounsellor() {
      var counsellor = $('#counsellor').val();
      $.ajax({
        url: '/app/center-master/sub-counsellor?id=' + counsellor,
        type: 'GET',
        success: function(data) {
          $('#sub_counsellor').html(data);
          <?php if (!empty($alloted_sub_counsellor_id)) { ?>
            $('#sub_counsellor').val('<?= $alloted_sub_counsellor_id ?>');
          <?php } ?>
        }
      })
    }

    <?php if (!empty($alloted_counsellor_id)) { ?>
      getSubCounsellor();
    <?php } ?>

    function getFeeSructures() {
      const university_id = '<?= $university_id ?>';
      const id = '<?= $id ?>';
      $.ajax({
        url: '/app/center-master/<?php print $university_is_vocational == 1 ? 'vocational-course-type' : 'fee-structures' ?>?university_id=' + university_id + '&id=' + id,
        type: 'GET',
        success: function(data) {
          $('#fee').html(data);
        }
      });
    }

    getFeeSructures();

    function removeAllotment() {
      const id = '<?= $id ?>';
      const university_id = '<?= $university_id ?>';
      $.ajax({
        url: '/app/center-master/remove-allotment',
        type: 'POST',
        data: {
          id: id,
          university_id: university_id
        },
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
          } else {
            notification('danger', data.message);
          }
        }
      })
    }

    $(function() {
      $('#form-allot-center-master').validate({
        rules: {
          counsellor: {
            required: true
          },
          'fee[]': {
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

    $("#form-allot-center-master").on("submit", function(e) {
      if ($('#form-allot-center-master').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
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
