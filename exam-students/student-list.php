<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .tooltip-inner {
    white-space: pre-wrap;
    max-width: 100% !important;
    text-align: left !important;
  }
</style>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-qZvrmS2ekKPF2mSznTQsxqPgnpkI4DNTlrdUmTzrDgektczlKNRRhy5X5AAOnx5S09ydFYWWNSfcEqDTTHgtNA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['current_session']);
unset($_SESSION['current_session']);
unset($_SESSION['filterByDepartment']);
unset($_SESSION['filterByUser']);
unset($_SESSION['filterByDate']);
unset($_SESSION['filterBySubCourses']);
unset($_SESSION['filterByStatus']);
?>
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
                <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'University Head') { ?>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload OA, Enrollment AND Roll No." onclick="uploadOAEnrollRoll()"> <i class="uil uil-upload"></i></button>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload Pendency" onclick="uploadMultiplePendency()"> <i class="uil uil-file-upload-alt"></i></button>
                <?php } ?>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Excel" onclick="exportData()"> <i class="uil uil-down-arrow"></i></button>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Documents" onclick="exportSelectedDocument()"> <i class="uil uil-file-download-alt"></i></button>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Student" onclick="window.open('/admissions/application-form');"> <i class="uil uil-plus-circle"></i></button>
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
        <?php if (isset($_SESSION['university_id'])) { ?>
          <div class="card card-transparent">
            <div class="card-header">
              <!-- <div class="d-flex justify-content-start">
                <div class="col-md-2">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sessions" onchange="changeSession(this.value)">
                      <option value="All">All</option>
                      <?php
                      $role_query = "";
                      if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
                        $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
                        $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                      }
                      $sessions = $conn->query("SELECT Admission_Sessions.ID,Admission_Sessions.Name,Admission_Sessions.Current_Status FROM Admission_Sessions LEFT JOIN Students ON Admission_Sessions.ID = Students.Admission_Session_ID WHERE Admission_Sessions.University_ID = '" . $_SESSION['university_id'] . "' $role_query GROUP BY Name ORDER BY Admission_Sessions.ID ASC");
                      while ($session = mysqli_fetch_assoc($sessions)) { ?>
                        <option value="<?= $session['Name'] ?>" <?php print $session['Current_Status'] == 1 ? 'selected' : '' ?>><?= $session['Name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="departments" onchange="addFilter(this.value, 'departments');">
                      <option value="">Choose Types</option>
                      <?php $departments = $conn->query("SELECT ID, Name FROM Course_Types WHERE University_ID = " . $_SESSION['university_id']);
                      while ($department = $departments->fetch_assoc()) {
                        echo '<option value="' . $department['ID'] . '">' . $department['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_courses" onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Program">
                      <option value="">Choose Program</option>
                      <?php $programs = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Name FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $role_query GROUP BY Students.Sub_Course_ID");
                      while ($program = $programs->fetch_assoc()) {
                        echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 m-b-10">
                  <div class="input-daterange input-group" id="datepicker-range">
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                    <div class="input-group-addon">to</div>
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                  </div>
                </div>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="application_status" onchange="addFilter(this.value, 'application_status')" data-placeholder="Choose App. Status">
                      <option value="">Application Status</option>
                      <option value="1">Document Verified</option>
                      <option value="2">Payment Verified</option>
                      <option value="3">Both Verified</option>
                    </select>
                  </div>
                </div>
                <?php if ($_SESSION['Role'] != 'Sub-Center') { ?>
                  <div class="col-md-2 m-b-10">
                    <div class="form-group">
                      <select class="full-width" style="width:40px" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users')" data-placeholder="Choose User">

                      </select>
                    </div>
                  </div>
                <?php } ?>
              </div> -->
              <div class="clearfix"></div>
            </div>
            <div class="card-body">
              <div class="card card-transparent">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                  <li class="nav-item">
                    <a class="active" data-toggle="tab" data-target="#applications" href="#"><span>All Applications - <span id="application_count">0</span></span></a>
                  </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="applications">
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-2 d-flex justify-content-start">
                        <input type="text" id="application-search-table" class="form-control pull-right" placeholder="Search">
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover nowrap" id="application-table">
                        <thead>
                          <tr>
                            <th data-orderable="false">S.NO</th>
                            <th>Photo</th>
                            <th>Student</th>
                            <th>DOB</th>
                            <th>Status</th>
                            <th>Enrollment No.</th>
                            <th>Adm Session SEN/ Years</th>
                            <th>Adm Type</th>
                            <th>Program</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>

        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->

    <div class="modal fade slide-up" id="reportmodal" style="z-index:9999" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="false">
      <div class="modal-dialog modal-md">
        <div class="modal-content-wrapper">
          <div class="modal-content" id="report-modal-content">
          </div>
        </div>
      </div>
    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script>
      $('#datepicker-range').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        endDate: '0d'
      });
    </script>

    <?php if ($_SESSION['Role'] == 'Administrator' && !isset($_SESSION['university_id'])) { ?>
      <script type="text/javascript">
        changeUniversity();
      </script>
    <?php } ?>

    <script type="text/javascript">
      $(function() {
        var role = '<?php echo $_SESSION['Role']; ?>';
        var showInhouse = role != 'Center' && role != 'Sub-Center' ? true : false;
        var is_accountant = ['Accountant', 'Administrator'].includes(role) ? true : false;
        var is_university_head = ['University Head', 'Administrator'].includes(role) ? true : false;
        var is_operations = role == 'Operations' ? true : false;
        var hasStudentLogin = '<?php echo $_SESSION['has_lms'] == 1 ? true : false; ?>';
        var applicationTable = $('#application-table');
        var notProcessedTable = $('#not-processed-table');
        var readyForVerificationTable = $('#ready-for-verification-table');
        var verifiedTable = $('#verified-table');
        var processedToUniversityTable = $('#proccessed-to-university-table');
        var enrolledTable = $('#enrolled-table');

        var applicationSettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/exam-students/server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
            "render": function(data, type, row) {
                return '<div>\
                '+row.ID+'<button class="btn btn-primary" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Admit card" onclick="exportAdmitCard(' + row.Student_id + ')"> <i class="uil uil-down-arrow"></i></button>\
                <button class="btn btn-primary" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Examation form" onclick="exportAdmitionForm(' + row.Student_id + ')"> <i class="uil uil-down-arrow"></i></button>\
              </div>';
              }
            	
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
                        <img src="' + data + '" alt="" data-src="' + data + '"\
                         data-src-retina="' + data + '" width="32" height="32">\
                        </span>';
              }
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<span >'+row.First_Name+'</span>\
                </br><span >'+row.Phone_Number+'</span>\
                </br><span >'+row.Email+'</span>';
              }
            },
            {
              data: "DOB",
            },
            {
              data: "Stauts",
              "render": function(data, type, row) {
                var status = data == 1 ? 'Active' : 'Inactive';
                return '<sapn>' + status + '</sapn>';
              }
            },
            {
              data: "Enrollment_No",
            },
            {
              data: "Adm_Session",
            },
            {
              data: "Adm_Type",
            },
            {
              data: "Course",
              "render": function(data, type, row) {
                return '<span >'+row.Course+'</span>\
                </br><span >'+row.Sub_Course+'</span>';
              }
            },
          ],
          "sDom": "l<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          drawCallback: function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
          },
          "aaSorting": []
        };

        applicationTable.dataTable(applicationSettings);


        // search box for table
        $('#application-search-table').keyup(function() {
          applicationTable.fnFilter($(this).val());
        });


      })
    </script>

    <script type="text/javascript">
      function changeSession(value) {
        $('input[type=search]').val('');
        updateSession();
      }

      function updateSession() {
        var session_id = $('#sessions').val();
        $.ajax({
          url: '/app/applications/change-session',
          data: {
            session_id: session_id
          },
          type: 'POST',
          success: function(data) {
            $('.table').DataTable().ajax.reload(null, false);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function addEnrollment(id) {
        $.ajax({
          url: '/app/applications/enrollment/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }

      function addOANumber(id) {
        $.ajax({
          url: '/app/applications/oa-number/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function exportData() {
        var search = $('#application-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/applications/export' + url);
      }

      function exportDocuments(id) {
        $.ajax({
          url: '/app/applications/document?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }

      function exportZip(id) {
        window.open('/app/applications/zip?id=' + id);
      }

      function exportPdf(id) {
        window.open('/app/applications/pdf?id=' + id);
      }

      function exportSelectedDocument() {
        var search = $('#application-search-table').val();
        var searchQuery = search.length > 0 ? "?search=" + search : "";
        $.ajax({
          url: '/app/applications/documents/create' + searchQuery,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function uploadOAEnrollRoll() {
        $.ajax({
          url: '/app/applications/uploads/create_oa_enroll_roll',
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function printForm(id) {
        // window.open('/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + id);
        window.location.href = '/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + id;
      }
    </script>
    
    <script type="text/javascript">
      function exportAdmitCard(id) {
        window.location.href = '/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + id+'&For=admitcard';
      }
      
      
      function exportAdmitionForm(id) {
        window.location.href = '/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + id+'&For=aplicationform';
      }
    </script>

    <script type="text/javascript">
      function processByCenter(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Process'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "/app/applications/process-by-center",
              type: 'POST',
              dataType: 'json',
              data: {
                id: id
              },
              success: function(data) {
                if (data.status == 200) {
                  notification('success', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                } else {
                  notification('danger', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                }
              }
            });
          } else {
            $('.table').DataTable().ajax.reload(null, false);
          }
        })
      }

      function processedToUniversity(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Process.'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "/app/applications/processed-to-university",
              type: 'POST',
              dataType: 'json',
              data: {
                id: id
              },
              success: function(data) {
                if (data.status == 200) {
                  notification('success', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                } else {
                  notification('danger', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                }
              }
            });
          } else {
            $('.table').DataTable().ajax.reload(null, false);
          }
        })
      }

      function verifyPayment(id) {
        $.ajax({
          url: '/app/applications/review-payment?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }

      function verifyDocument(id) {
        $.ajax({
          url: '/app/applications/review-documents?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#full-modal-content').html(data);
            $('#fullmodal').modal('show');
          }
        })
      }

      function reportPendency(id) {
        $.ajax({
          url: '/app/pendencies/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#report-modal-content').html(data);
            $('#reportmodal').modal('show');
          }
        })
      }

      function uploadPendency(id) {
        $(".modal").modal('hide');
        $.ajax({
          url: '/app/pendencies/edit?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }

      function uploadMultiplePendency() {
        $(".modal").modal('hide');
        $.ajax({
          url: '/app/pendencies/upload',
          type: 'GET',
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }
    </script>

    <script>
      if ($("#users").length > 0) {
        $("#users").select2({
          placeholder: 'Choose Center'
        })
        getCenterList('users');
      }

      $("#departments").select2({
        placeholder: 'Choose Department'
      })

      function addFilter(id, by) {
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }

      function addDateFilter() {
        var startDate = $("#startDateFilter").val();
        var endDate = $("#endDateFilter").val();
        if (startDate.length == 0 || endDate == 0) {
          return
        }
        var id = 0;
        var by = 'date';
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by,
            startDate,
            endDate
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }

      function getCourses(id) {
        $.ajax({
          url: '/app/courses/department-courses',
          type: 'POST',
          data: {
            id
          },
          success: function(data) {
            $("#sub_courses").html(data);
          }
        })
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
