<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>

<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  
  <div class="page-content-wrapper ">
    <div class="content ">
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <!-- <div class="inner">
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php 
              // $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              // for ($i = 1; $i <= count($breadcrumbs); $i++) {
              //   if (count($breadcrumbs) == $i) : $active = "active";
              //     $crumb = explode("?", $breadcrumbs[$i]);
              //     echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
              //   endif;
              // }
              ?>
              <div>
              </div>
            </ol>
            
          </div> -->
        </div>
      </div>
     
      <div class=" container-fluid">
        <div class="card card-transparent">
          <div class="card-header">
            
            <?php 
              $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo $crumb[0];
                endif;
              }
              ?>

            <div class="pull-right">
              <div class="row">
                <div class="col-xs-7" style="margin-right: 10px;">
                  <input type="text" id="e-book-search-table" class="form-control pull-right p-2 fw-bold" placeholder="Search">
                </div>
                <div class="col-xs-5" style="margin-right: 10px;">
                  <button class="btn btn-primary p-2 "  data-toggle="tooltip" data-original-title="Add E-books" onclick="add('e-books','lg')"> <i class="uil uil-plus-circle"></i>Add</button>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="e_books-table">
                <thead>

                <tr>
                  <th>Course</th>
                  <th>Subject</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
   

    <script type="text/javascript">
        $(function() {
            var role = '<?= $_SESSION['Role'] ?>';
            var show = role == 'Administrator' ? true : false;
            var table = $('#e_books-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/e-books/data-list'
                },
                'columns': [{
                        data: "course_name"
                    },
                    {
                        data: "subject_name"
                    },
                    {
                        data: "file_type"
                    },
                    {
                      data: "status",
                      "render": function(data, type, row) {
                        var active = data == 1 ? 'Active' : 'Inactive';
                        var checked = data == 1 ? 'checked' : '';
                        return '<div class="form-check form-check-inline switch switch-lg success">\
                                <input onclick="changeStatus('+"'e_books'"+', '+row.ID+','+"'status'"+')" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
                                <label for="status-switch-' + row.ID + '">' + active + '</label>\
                              </div>';
                      }
                    },
                    {
                      data: "ID",
                      "render": function(data, type, row) {
                        return '<div class="button-list text-end">\
                        <i class="uil uil-trash icon-xs cursor-pointer" onclick="changeStatus('+"'e_books'"+', '+data+','+"'status'"+',2)"></i>\
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
                "iDisplayLength": 5,
            };
            table.dataTable(settings);
            // search box for table
            $('#e-book-search-table').keyup(function() {
              table.fnFilter($(this).val());
            });
          })
    </script>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
