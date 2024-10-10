<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
<?php
unset($_SESSION['filterByUser']);
unset($_SESSION['filterByDate']);
?>
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
                <a href="/app/payments/export?type=2" target="_blank" class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download"> <i class="uil uil-down-arrow"></i></a>
                <?php if (isset($_SESSION['gateway'])) { ?>
                  <!-- <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Pay Now" onclick="add('<?php echo $_SESSION['gateway'] == 1 ? 'easebuzz' : '' ?>', 'md')"> <i class="uil uil-plus-circle"></i></button> -->
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
        <div class="card card-transparent">
          <div class="card-header">
            <div class="row">
              <div class="col-md-12 d-flex justify-content-start">
                <div class="col-md-3 m-b-10">
                  <div class="input-daterange input-group" id="datepicker-range">
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                    <div class="input-group-addon">to</div>
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                  </div>
                </div>
                <?php if ($_SESSION['Role'] != 'Sub-Center') { ?>
                  <div class="col-md-3 m-b-10">
                    <div class="form-group">
                      <select class="full-width" style="width:40px" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users', 1)" data-placeholder="Choose User">

                      </select>
                    </div>
                  </div>
                <?php } ?>

              </div>
            </div>
            <div class="pull-right">
              <div class="col-xs-12">
                <input type="text" id="payments-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="payments-table">
                <thead>
                  <tr>
                    <th data-orderable="false">Reciept</th>
                    <th>Transaction ID</th>
                    <th>Gateway ID</th>
                    <th>Mode</th>
                    <th>Bank Name</th>
                    <th>Amount</th>
                    <th>Payment By</th>
                    <th>Owner</th>
                    <th>Date</th>
                    <th>Status</th>
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
    <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
      $(function() {
        var role = "<?= $_SESSION['Role'] ?>";
        var showToAdminHeadAccountant = role == 'Administrator' || role == 'University Head' || role == 'Accountant' ? true : false;
        var table = $('#payments-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/online-payments/server'
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                if (['Center', 'Sub-Center'].includes(role)) {
                  if (row.File.length == 0) {
                    return '<center><i class="uil uil-upload cursor-pointer" onclick="uploadReceipt(' + data + ')" data-toggle="tooltip" data-placement="top" title="Upload Receipt"></i></center>';
                  } else {
                    return '<span class="label label-info">Pending Approval</span>';
                  }
                } else if (['Accountant', 'Administrator'].includes(role)) {
                  if (row.File.length == 0) {
                    return '<span class="label label-info">Pending Upload</span>';
                  } else {
                    return '<span class="label label-warning cursor-pointer" onclick="reviewReciept(' + data + ')">Review</span>';
                  }
                } else {
                  if (row.File.length == 0) {
                    return '<span class="label label-info">Pending Upload</span>';
                  } else {
                    return '<span class="label label-warning">In Review</span>';
                  }
                }
              }
            },
            {
              data: "Transaction_ID",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Gateway_ID"
            },
            {
              data: "Payment_Mode"
            },
            {
              data: "Bank"
            },
            {
              data: "Amount"
            },
            {
              data: "Student",
              "render": function(data, type, row) {
                if (Number.isInteger(data)) {
                  return '<span class="cursor-pointer" onclick="viewList(&#39;' + row.StudentIds + '&#39;)"><b>' + data + ' Students</b></span>';
                } else {
                  return '<strong>' + data + '</strong>';
                }
              }
            },
            {
              data: "Center_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + ' (' + row.Center_Code + ')</strong>';
              }
            },
            {
              data: "Transaction_Date"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var label_class = data == 0 ? "warning" : data == 1 ? "success" : "danger";
                var status = data == 0 ? "Pending" : data == 1 ? "Success" : "Rejected";
                return '<span class="label label-' + label_class + '">' + status + '</span>';
              }
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
        $('#payments-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });
      })

      function viewList(ids) {
        $.ajax({
          url: '/app/payments/list',
          type: 'POST',
          data: {
            ids
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }
    </script>

    <script>
      function uploadReceipt(id) {
        $.ajax({
          url: '/app/online-payments/create-receipt',
          type: 'GET',
          data: {
            id
          },
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }

      function reviewReciept(id) {
        $.ajax({
          url: '/app/online-payments/review-receipt',
          type: 'POST',
          data: {
            id
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }
    </script>

    <script>
      $('#datepicker-range').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        endDate: '0d'
      });
    </script>
    <script>
      if ($("#users").length > 0) {
        $("#users").select2({
          placeholder: 'Choose Center'
        })
        getCenterList('users');
      }

      function addFilter(id, by, page) {
        $.ajax({
          url: '/app/payments/filter',
          type: 'POST',
          data: {
            id,
            by,
            page
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
        page = 1;
        $.ajax({
          url: '/app/payments/filter',
          type: 'POST',
          data: {
            id,
            by,
            startDate,
            endDate,
            page
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>