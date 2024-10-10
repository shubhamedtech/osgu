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
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="results-table">
                <thead>
                  <tr>
                    <th data-orderable="false"></th>
                    <th>Result</th>
                    <th>Exam Type</th>
                    <th>Exam Session</th>
                    <th>Sem</th>
                    <th>Published On</th>
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
            { data: "Remarks"},
            { data: "Type"},
            { data: "Exam_Session"},
            { data: "Sem"},
            { data: "Published_On"},
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
      })
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
