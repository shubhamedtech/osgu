<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style type="text/css">
  input {
    text-transform: uppercase;
  }
</style>
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
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
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
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download"> <i class="uil uil-down-arrow"></i></button>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="add('exam-students', 'lg')"> <i class="uil uil-export"></i></button>
              </div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <?php
      $is_get = 0;
      $id = 0;
      $address = [];
      if (isset($_GET['id'])) {
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $id = base64_decode($id);
        $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
        $student = $conn->query("SELECT * FROM Students WHERE ID = $id");
        if ($student->num_rows > 0) {
          $is_get = 1;
        } else {
          header("Location: /admissions/applications");
        }
        $student = mysqli_fetch_assoc($student);

        if (!empty($student['Unique_ID']) && $_SESSION['crm']) {
          $check_in_leads = $conn->query("SELECT Leads.Email, Leads.Mobile FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.Unique_ID = '" . $student['Unique_ID'] . "'");
          if ($check_in_leads->num_rows > 0) {
            $lead = $check_in_leads->fetch_assoc();
            $student['Email'] = $lead['Email'];
            $student['Contact'] = $lead['Mobile'];
          }
        }

        echo '<script>localStorage.setItem("inserted_id",' . $id . ');</script>';
        $address = !empty($student['Address']) ? json_decode($student['Address'], true) : [];
      }

      if (isset($_GET['lead_id'])) {
        $lead_id = mysqli_real_escape_string($conn, $_GET['lead_id']);
        $lead_id = base64_decode($lead_id);
        $lead_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $lead_id));
        $lead = $conn->query("SELECT Lead_Status.Admission, Lead_Status.University_ID, Lead_Status.User_ID, Lead_Status.Course_ID,Lead_Status.Sub_Course_ID,Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS University,Courses.Name AS Category,Sub_Courses.Name AS Sub_Category,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID= $lead_id");
        if ($lead->num_rows > 0) {
          $is_get = 1;
        } else {
          header("Location: /leads/lists");
        }
        $lead = $lead->fetch_assoc();
      }
      ?>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid  ">
        <div id="rootwizard" class="m-t-50">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist" data-init-reponsive-tabs="dropdownfx">
            <li class="nav-item">
              <a class="active d-flex align-items-center" data-toggle="tab" href="#tab1" data-target="#tab1" role="tab"><i class="uil uil-user-circle fs-14 tab-icon"></i> <span>Studen Details</span></a>
            </li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane padding-20 sm-no-padding active slide-left" id="tab1">
              <div class="row row-same-height">
                <div class="col-md-4 b-r b-dashed b-grey sm-b-b">
                  <div class="padding-10 sm-padding-5 sm-m-t-15 m-t-50">
                    <div class="d-flex justify-content-center">
                      <lottie-player src="https://assets6.lottiefiles.com/packages/lf20_qfkr9cgr.json" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></lottie-player>
                    </div>
                    <h2>Fill up the Basic Details for admission</h2>
                    <ol>
                      <li>Option with (*) star mark are mandatory.</li>
                      <li>Please keep the required documents within 500KB.</li>
                      <li>Marksheet, Certificate, and Aadhaar Card are the only documents that can be uploaded in multiple files</li>
                    </ol>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="padding-10 sm-padding-5">
                    <form id="step_1" role="form" autocomplete="off" action="/app/exam-students/store-exam-student" enctype="multipart/form-data">
                      <h5>Applying For</h5>
                      <div class="row clearfix">
                        <!-- Center -->
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Center</label>
                            <select class="full-width" style="border: transparent;" name="center" id="center" onchange="getCourse()">
                              <option value="">Select</option>
                            </select>
                          </div>
                        </div>

                        <!-- Admission Session -->
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Admission Session</label>
                            <select class="full-width" style="border: transparent;" name="admission_session" id="admission_session" onchange="getAdmissionType(this.value)">
                              <option value="">Select</option>
                            </select>
                          </div>
                        </div>

                        <!-- Admission Type -->
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Admission Type</label>
                            <select class="full-width" style="border: transparent;" name="admission_type" id="admission_type" onchange="getCourse()">
                              <option value="">Select</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="row clearfix">
                        <!-- Center -->
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Course</label>
                            <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="course" id="course" onchange="getSubCourse()">
                              <option value="">Select</option>
                            </select>
                          </div>
                        </div>

                        <!-- Admission Session -->
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Sub Course</label>
                            <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="sub_course" id="sub_course" onchange="getDuration(); getEligibility();">
                              <option value="">Select</option>
                            </select>
                          </div>
                        </div>

                        <!-- Admission Type -->
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label id="mode">Mode</label>
                            <select class="full-width" style="border: transparent;" name="duration" id="duration">
                              <option value="">Select</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <h5>Basic Details</h5>
                      <div class="row clearfix">
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Full Name</label>
                            <?php $student_name = !empty($id) ? array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) : [] ?>
                            <input type="text" name="full_name" class="form-control" placeholder="ex: Jhon Doe" value="">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="" placeholder="">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Mobile number</label>
                            <input type="tel" name="phone_number" maxlength="10" minlength="10" class="form-control" placeholder="">
                          </div>
                        </div>
                      </div>

                      <div class="row clearfix">
                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>Enrolment Number</label>
                            <input type="tel" name="Enrolment" class="form-control" value="" placeholder="123XX" id="Enrolment">
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group form-group-default required">
                            <label>DOB</label>
                            <input type="tel" name="dob" class="form-control" value="" placeholder="dd-mm-yyyy" id="dob">
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="padding-20 sm-padding-5 sm-m-b-20 sm-m-t-20 bg-white clearfix">
                <ul>
                    <li>
                        <button aria-label="" onclick="submitForm(1)" class="btn btn-primary btn-cons btn-animated from-left pull-right" type="button">
                            <span>Submit</span>
                            <span class="hidden-block">
                            <i class="uil uil-check"></i>
                            </span>
                        </button>
                        <button aria-label="" class="btn btn-default btn-cons btn-animated from-left pull-right" type="button">
                            <span>Cancle</span>
                            <span class="hidden-block">
                            <i class="uil uil-angle-left"></i>
                            </span>
                        </button>
                    </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>

    <?php if (empty($id)) { ?>
      <script>
        $(function() {
          if (localStorage.getItem('inserted_id') !== null) {
            localStorage.removeItem('inserted_id');
            Swal.fire(
              'Previous Application is saved!',
              'Please go to Applications > Edit if you want to proceed further!',
              'success'
            );
          }
        });
      </script>
    <?php } ?>

    <script>
      $(function() {
        localStorage.removeItem('print_id');
        $("#dob").mask("99-99-9999")
        $("#aadhar").mask("9999-9999-9999")
        $('#dob').datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          endDate: '-15y'
        });
      });
    </script>

    <!-- Application Form Functions -->
    <script type="text/javascript">
      function getCenter(university_id) {
        $.ajax({
          url: '/app/application-form/center?university_id=' + university_id,
          type: 'GET',
          success: function(data) {
            $('#center').html(data);
            $('#center').val(<?php echo !empty($id) ? $student['Added_For'] : (isset($_GET['lead_id']) ? $lead['User_ID'] : '') ?>);
          }
        })
      }

      function getAdmissionSession(university_id) {
        $.ajax({
          url: '/app/application-form/admission-session?university_id=' + university_id + '&form=<?php print !empty($id) ? 1 : "" ?>',
          type: 'GET',
          success: function(data) {
            $('#admission_session').html(data);
            $('#admission_session').val(<?php print !empty($id) ? $student['Admission_Session_ID'] : '' ?>);
            getAdmissionType($('#admission_session').val());

          }
        })
      }

      function getAdmissionType(session_id) {
        const university_id = '<?= $_SESSION['university_id'] ?>';
        $.ajax({
          url: '/app/application-form/admission-type?university_id=' + university_id + '&session_id=' + session_id,
          type: 'GET',
          success: function(data) {
            $('#admission_type').html(data);
            $('#admission_type').val(<?php print !empty($id) ? $student['Admission_Type_ID'] : '' ?>);
            getCourse();
          }
        })
      }

      function getCourse() {
        var center = $('#center').val();
        const university_id = '<?= $_SESSION['university_id'] ?>';
        const session_id = $('#admission_session').val();
        const admission_type_id = $('#admission_type').val();
        $.ajax({
          url: '/app/application-form/course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&form=<?php print !empty($id) || !empty($lead_id) ? 1 : "" ?>',
          type: 'GET',
          success: function(data) {
            $('#course').html(data);
            $('#course').val(<?php print !empty($id) ? $student['Course_ID'] : (isset($_GET['lead_id']) ? $lead['Course_ID'] : '') ?>);
            getSubCourse();
          }
        })
      }

      function highDetailsRequired() {
        $('.high_school').addClass('required');
        $('#high_subject').validate();
        $('#high_subject').rules('add', {
          required: true
        });
        $('#high_year').validate();
        $('#high_year').rules('add', {
          required: true
        });
        $('#high_board').validate();
        $('#high_board').rules('add', {
          required: true
        });
        $('#high_total').validate();
        $('#high_total').rules('add', {
          required: true
        });
        <?php if (empty($id)) { ?>
          $('#high_marksheet').validate();
          $('#high_marksheet').rules('add', {
            required: true
          });
        <?php } ?>

        <?php if (!empty($id) && empty($high_marksheet)) { ?>
          $('#high_marksheet').validate();
          $('#high_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function highDetailsNotRequired() {
        $('.high_school').removeClass('required');
        $('#high_subject').rules('remove', 'required');
        $('#high_year').rules('remove', 'required');
        $('#high_board').rules('remove', 'required');
        $('#high_total').rules('remove', 'required');
        $('#high_marksheet').rules('remove', 'required');
      }

      function interDetailsRequired() {
        $('.intermediate').addClass('required');
        $('#inter_subject').validate();
        $('#inter_subject').rules('add', {
          required: true
        });
        $('#inter_year').validate();
        $('#inter_year').rules('add', {
          required: true
        });
        $('#inter_board').validate();
        $('#inter_board').rules('add', {
          required: true
        });
        $('#inter_total').validate();
        $('#inter_total').rules('add', {
          required: true
        });
        <?php if (empty($id)) { ?>
          $('#inter_marksheet').validate();
          $('#inter_marksheet').rules('add', {
            required: true
          });
        <?php } ?>

        <?php if (!empty($id) && empty($inter_marksheet)) { ?>
          $('#inter_marksheet').validate();
          $('#inter_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function interDetailsNotRequired() {
        $('.intermediate').removeClass('required');
        $('#inter_subject').rules('remove', 'required');
        $('#inter_year').rules('remove', 'required');
        $('#inter_board').rules('remove', 'required');
        $('#inter_total').rules('remove', 'required');
        $('#inter_marksheet').rules('remove', 'required');
      }

      function ugDetailsRequired() {
        $('.ug-program').addClass('required');
        $('#ug_subject').validate();
        $('#ug_subject').rules('add', {
          required: true
        });
        $('#ug_year').validate();
        $('#ug_year').rules('add', {
          required: true
        });
        $('#ug_board').validate();
        $('#ug_board').rules('add', {
          required: true
        });
        $('#ug_total').validate();
        $('#ug_total').rules('add', {
          required: true
        });
        <?php if (empty($id)) { ?>
          $('#ug_marksheet').validate();
          $('#ug_marksheet').rules('add', {
            required: true
          });
        <?php } ?>

        <?php if (!empty($id) && empty($ug_marksheet)) { ?>
          $('#ug_marksheet').validate();
          $('#ug_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function ugDetailsNotRequired() {
        $('.ug-program').removeClass('required');
        $('#ug_subject').rules('remove', 'required');
        $('#ug_year').rules('remove', 'required');
        $('#ug_board').rules('remove', 'required');
        $('#ug_total').rules('remove', 'required');
        $('#ug_marksheet').rules('remove', 'required');
      }

      function pgDetailsRequired() {
        $('.pg-program').addClass('required');
        $('#pg_subject').validate();
        $('#pg_subject').rules('add', {
          required: true
        });
        $('#pg_year').validate();
        $('#pg_year').rules('add', {
          required: true
        });
        $('#pg_board').validate();
        $('#pg_board').rules('add', {
          required: true
        });
        $('#pg_total').validate();
        $('#pg_total').rules('add', {
          required: true
        });
        <?php if (empty($id)) { ?>
          $('#pg_marksheet').validate();
          $('#pg_marksheet').rules('add', {
            required: true
          });
        <?php } ?>

        <?php if (!empty($id) && empty($pg_marksheet)) { ?>
          $('#pg_marksheet').validate();
          $('#pg_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function pgDetailsNotRequired() {
        $('.pg-program').removeClass('required');
        $('#pg_subject').rules('remove', 'required');
        $('#pg_year').rules('remove', 'required');
        $('#pg_board').rules('remove', 'required');
        $('#pg_total').rules('remove', 'required');
        $('#pg_marksheet').rules('remove', 'required');
      }

      function otherDetailsRequired() {
        $('.other-program').addClass('required');
        $('#other_subject').validate();
        $('#other_subject').rules('add', {
          required: true
        });
        $('#other_year').validate();
        $('#other_year').rules('add', {
          required: true
        });
        $('#other_board').validate();
        $('#other_board').rules('add', {
          required: true
        });
        $('#other_total').validate();
        $('#other_total').rules('add', {
          required: true
        });
        <?php if (empty($id)) { ?>
          $('#other_marksheet').validate();
          $('#other_marksheet').rules('add', {
            required: true
          });
        <?php } ?>

        <?php if (!empty($id) && empty($other_marksheet)) { ?>
          $('#other_marksheet').validate();
          $('#other_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function otherDetailsNotRequired() {
        $('.other-program').removeClass('required');
        $('#other_subject').rules('remove', 'required');
        $('#other_year').rules('remove', 'required');
        $('#other_board').rules('remove', 'required');
        $('#other_total').rules('remove', 'required');
        $('#other_marksheet').rules('remove', 'required');
      }

      function getSubCourse() {
        var center = $('#center').val();
        const university_id = '<?= $_SESSION['university_id'] ?>';
        const session_id = $('#admission_session').val();
        const admission_type_id = $('#admission_type').val();
        const course_id = $('#course').val();
        $.ajax({
          url: '/app/application-form/sub-course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&course_id=' + course_id,
          type: 'GET',
          success: function(data) {
            $('#sub_course').html(data);
            $('#sub_course').val(<?php print !empty($id) ? $student['Sub_Course_ID'] : (isset($_GET['lead_id']) ? $lead['Sub_Course_ID'] : '') ?>);
            getMode();
          }
        })
      }

      function getMode() {
        const sub_course_id = $('#sub_course').val();
        $.ajax({
          url: '/app/application-form/mode?sub_course_id=' + sub_course_id,
          type: 'GET',
          success: function(data) {
            $('#mode').html(data);
            getDuration();
            getEligibility();
          }
        })
      }

      function getDuration() {
        const admission_type_id = $('#admission_type').val();
        const sub_course_id = $('#sub_course').val();
        $.ajax({
          url: '/app/application-form/duration?admission_type_id=' + admission_type_id + '&sub_course_id=' + sub_course_id,
          type: 'GET',
          success: function(data) {
            $('#duration').html(data);
            $('#duration').val(<?php print !empty($id) ? $student['Duration'] : '' ?>)
          }
        })
      }

      function getEligibility(){
        const sub_course_id = $('#sub_course').val();
        $.ajax({
          url: '/app/application-form/course-eligibility?id='+sub_course_id,
          type:'GET',
          dataType: 'json',
          success: function(data) {
            if(data.status){
              var col_size = data.count==1 ? 10 : data.count==2 ? 5 : data.count==3 ? 3 : data.count==4 ? 2 : 2

              if(data.eligibility.includes('High School')){
                highDetailsRequired();
                $("#high_school_column").css('display', 'block');
                $("#high_school_column").addClass('col-md-'+col_size);
              }else{
                highDetailsNotRequired();
                $("#high_school_column").css('display', 'none');
              }

              if(data.eligibility.includes('Intermediate')){
                interDetailsRequired();
                $("#intermediate_column").css('display', 'block');
                $("#intermediate_column").addClass('col-md-'+col_size);
              }else{
                interDetailsNotRequired();
              }

              if(data.eligibility.includes('UG')){
                ugDetailsRequired();
                $("#ug_column").css('display', 'block');
                $("#ug_column").addClass('col-md-'+col_size);
              }else{
                ugDetailsNotRequired();
              }

              if(data.eligibility.includes('PG')){
                pgDetailsRequired();
                $("#pg_column").css('display', 'block');
                $("#pg_column").addClass('col-md-'+col_size);
              }else{
                pgDetailsNotRequired();
              }

              if(data.eligibility.includes('Other')){
                otherDetailsRequired();
                $("#other_column").css('display', 'block');
                $("#other_column").addClass('col-md-'+col_size);
              }else{
                otherDetailsNotRequired();
              }
            }else{
              notification('danger', 'Eligibility is not configured for this course!');
            }
          }
        })
      }

      getCenter('<?= $_SESSION['university_id'] ?>');
      getAdmissionSession('<?= $_SESSION['university_id'] ?>');

      function fileValidation(id) {
        var fi = document.getElementById(id);
        if (fi.files.length > 0) {
          for (var i = 0; i <= fi.files.length - 1; i++) {
            var fsize = fi.files.item(i).size;
            var file = Math.round((fsize / 1024));
            // The size of the file.
            if (file >= 500) {
              $('#' + id).val('');
              alert("File too Big, each file should be less than or equal to 500KB");
            }
          }
        }
      }
    </script>

    <script type="text/javascript">
      $(document).ready(function() {
        $('#step_1').validate({
          rules: {
            center: {
              required: true
            },
            admission_session: {
              required: true
            },
            admission_type: {
              required: true
            },
            course: {
              required: true
            },
            sub_course: {
              required: true
            },
            duration: {
              required: true
            },
            full_name: {
              required: true
            },
            first_name: {
              required: true
            },
            last_name: {
              required: true
            },
            father_name: {
              required: true
            },
            mother_name: {
              required: true
            },
            dob: {
              required: true
            },
            gender: {
              required: true
            },
            category: {
              required: true
            },
            employment_status: {
              required: true
            },
            aadhar: {
              required: true
            },
            nationality: {
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
      });

      function submitForm(index) {
        var $valid = $("#step_" + index).valid();
        if (!$valid) {
          return false;
        } else {
          $('#step_' + index).submit();
        }
      }

      $('#step_1').submit(function(e) {
        var formData = new FormData(this);
        e.preventDefault();
        $.ajax({
          url: this.action,
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              notification('success', data.message);
              location.reload();
            //   localStorage.setItem('inserted_id', data.id);
            } else {
              notification('danger', data.message);
              $('#previous-button').click();
            }
          },
          error: function(data) {
            notification('danger', 'Server is not responding. Please try again later');
            $('#previous-button').click();
            console.log(data);
          }
        });
      });

      function printForm() {
        // window.open('/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + localStorage.getItem('print_id'));
        location.reload();
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
