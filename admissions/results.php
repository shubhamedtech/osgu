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
        <div class="card card-transparent">
          <div class="card-header">
            <div class="pull-right">
              <div class="col-xs-12">
                <input type="text" id="results-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="results-table">
                <thead>
                  <tr>
                    <th data-orderable="false"></th>
                    <th>Photo</th>
                    <th>Result</th>
                    <th>Student ID</th>
                    <th>Enrollment No</th>
                    <th>Name</th>
                    <th>Exam Type</th>
                    <th>Exam Session</th>
                    <th>Program</th>
                    <th>Specialization</th>
                    <th>Sem</th>
                    <th>Center</th>
                    <th>Published On</th>
                    <th data-orderable="false">User</th>
                    <th data-orderable="false">Student</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
      $(function() {
        var role = "<?= $_SESSION['Role'] ?>";
        var showToAdminHeadAccountant = role == 'Administrator' || role == 'University Head' || role == 'Accountant' ? true : false;
        var table = $('#results-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/results/server'
          },
          'columns': [
            { data: "Student_ID",
              "render": function(data, type, row) {
                return '<center><a href="/app/results/20/?id='+data+'" target="_blank"><i class="uil uil-file-bookmark-alt cursor-pointer" data-toggle="tooltip" data-placement="top" title="View Result"></i></a></center>'
              }
            },
            { data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
                  <img src="' + data + '" alt="" data-src="' + data + '" data-src-retina="' + data + '" width="32" height="32">\
                </span>';
              }
            },
            { data: "Remarks"},
            { data: "Unique_ID",
              "render": function(data, type, row) {
                return '<b>'+data+'</b>'
              }
            },
            { data: "Enrollment_No"},
            { data: "First_Name"},
            { data: "Type"},
            { data: "Exam_Session"},
            { data: "Course_ID"},
            { data: "Sub_Course_ID"},
            { data: "Sem"},
            { data: "Code",
              "render": function(data, type, row) {
                return row.Name + ' ('+data+')';
              }
            },
            { data: "Published_On"},
            { data: "User",
              "render": function(data, type, row) {
                var active = data==1 ? 'Active' : 'Inactive';
                var checked = data==1 ? 'checked' : '';
                return '<div class="form-check form-check-inline switch switch-lg success">\
                  <input onclick="changeStatus(&#39;'+row.ID+'&#39;, &#39;User&#39;, &#39;'+row.Exam_Session+'&#39;, '+data+');" type="checkbox" '+checked+' id="user-status-switch-'+row.ID+'">\
                  <label for="user-status-switch-'+row.ID+'">'+active+'</label>\
                </div>';
              },
              visible: showToAdminHeadAccountant
            },
            { data: "Student",
              "render": function(data, type, row) {
                var active = data==1 ? 'Active' : 'Inactive';
                var checked = data==1 ? 'checked' : '';
                return '<div class="form-check form-check-inline switch switch-lg success">\
                  <input onclick="changeStatus(&#39;'+row.ID+'&#39;, &#39;Student&#39;, &#39;'+row.Exam_Session+'&#39;, '+data+');" type="checkbox" '+checked+' id="student-status-switch-'+row.ID+'">\
                  <label for="student-status-switch-'+row.ID+'">'+active+'</label>\
                </div>';
              },
              visible: showToAdminHeadAccountant
            }
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 25,
          "drawCallback": function() {
            $('[data-toggle="tooltip"]').tooltip();
          }
        };

        table.dataTable(settings);

        // search box for table
        $('#results-search-table').keyup(function() {
          console.log($(this).val());
          table.fnFilter($(this).val());
        });
      })

      function changeStatus(student_id, column, exam_session, value){
        $.ajax({
          url: '/app/results/status',
          data:{student_id, column, exam_session, value},
          type:'POST',
          dataType: 'json',
          success: function(data){
            if(data.status){
              notification('success', data.message);
              $('#results-table').DataTable().ajax.reload(null, false);
            }else{
              notification('danger', data.message);
            }
          }
        })
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
