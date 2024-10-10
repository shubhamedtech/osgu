<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB --> 
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head'])) { ?>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="upload('datesheets', 'md')"> <i class="uil uil-export"></i></button>
                <?php } ?>
              </div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="row">
          <div class="col-md-6">
            <div class="form-group form-group-default required">
              <label>Course</label>
              <select class="full-width" style="border: transparent;" id="course" onchange="getSemester(this.value); removeTable()">
                <option value="">Choose</option>
                <?php
                $condition = "";
                if (in_array($_SESSION['Role'], ['Center', 'Sub-Center'])) {
                  $ids = array();
                  $sub_course_ids = $conn->query("SELECT Sub_Course_ID FROM Center_Sub_Courses WHERE `User_ID` = " . $_SESSION['ID'] . "");
                  while ($sub_course_id = $sub_course_ids->fetch_assoc()) {
                    $ids[] = $sub_course_id['Sub_Course_ID'];
                  }
                  $condition = " AND Sub_Courses.ID IN (" . implode(",", $ids) . ")";
                }
                $sub_courses = $conn->query("SELECT CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Sub_Course, Sub_Courses.ID FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Sub_Courses.University_ID = " . $_SESSION['university_id'] . " $condition ORDER BY Sub_Courses.Name ASC");
                while ($sub_course = $sub_courses->fetch_assoc()) {
                  echo '<option value="' . $sub_course['ID'] . '">' . $sub_course['Sub_Course'] . '</option>';
                }
                ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group form-group-default required">
              <label>Semester</label>
              <select class="full-width" style="border: transparent;" id="semester" onchange="getTable()">
                <option value="">Choose</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12" id="datesheets">
            
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      function getSemester(id) {
        $.ajax({
          url: '/app/datesheets/semester?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#semester").html(data);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function getTable() {
        var course_id = $('#course').val();
        var semester = $('#semester').val();
        if (course_id.length > 0 && semester.length > 0) {
          $.ajax({
            url: '/app/exam-students/results?course_id=' + course_id + '&semester=' + semester,
            type: 'GET',
            success: function(data) {
              $('#datesheets').html(data);
            }
          })
        } else {
          $('#datesheets').html('');
        }
      }

      function removeTable() {
        $('#datesheets').html('');
      }
    </script>

    <script type="text/javascript">
      function uploadFile(table, column, id) {
        $.ajax({
          url: '/app/upload/create?id=' + id + '&column=' + column + '&table=' + table,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
