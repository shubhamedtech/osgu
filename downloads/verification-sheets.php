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
              <div class="text-end">
                <?php if ($_SESSION['Role'] == 'University Head' || $_SESSION['Role'] == 'Administrator') { ?>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add" onclick="add('downloads/verification-sheets', 'md')"> <i class="uil uil-plus-circle"></i></button>
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
        <?php if(in_array($_SESSION['Role'], ['Center', 'Sub-Center'])){ ?>
          <div class="row">
            <div class="col d-flex justify-content-center">
              <div class="col-lg-4">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group form-group-default required">
                          <label>Enrollment No</label>
                          <input type="text" id="file" class="form-control" placeholder="ex: W7328XXXXXXX" required>
                        </div>
                        <button class="btn btn-block btn-primary" onclick="find()">Download</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php }else{ ?>
          <div class="card card-transparent">
            <div class="card-header">
              <div class="pull-right">
                <div class="col-xs-12">
                  <input type="text" id="verification-sheets-search-table" class="form-control pull-right" placeholder="Search">
                </div>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover nowrap" id="verification-sheets-table">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>University</th>
                      <th data-orderable="false"></th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        <?php } ?>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      function find() {
        var file = $('#file').val();
        $.ajax({
          url: '/app/downloads/verification-sheets/find?file=' + file,
          type: 'GET',
          success: function(data) {
            if (data.match('200')) {
              $('#file').val('');
              var obj = JSON.parse(data);
              notification('success', obj.message);
              window.open("/uploads/verification-sheets/" + obj.file);
            } else {
              notification('danger', 'File not found!');
            }
          }
        })
      }
    </script>

    <script type="text/javascript">
      $(function() {
        var table = $('#verification-sheets-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var showToAdmin = role == 'Administrator' ? true : false;
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/downloads/verification-sheets/server'
          },
          'columns': [{
              data: "Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "University"
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                return '<div class="button-list text-end">\
              <a href="' + row.File + '" download="' + row.Name + '.' + row.Extension + '"><i class="uil uil-down-arrow icon-xs cursor-pointer" title="Download"></i></a>\
              <i class="uil uil-trash icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;downloads/verification-sheets&#39;,&#39;' + data + '&#39)"></i>\
            </div>'
              }
            },
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 25
        };

        table.dataTable(settings);

        // search box for table
        $('#verification-sheets-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });
      })
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
