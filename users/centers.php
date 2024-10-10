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
        <div class=" container-fluid  sm-p-l-0 sm-p-r-0">
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
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="uil uil-down-arrow"></i></button>
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
                <input type="text" id="users-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">

            <?php
            $table = 'Alloted_Center_To_Counsellor';
            $university_query = "";
            if ($_SESSION['Role'] == 'University Head') {
              $university_query = " AND University_ID = " . $_SESSION['university_id'];
              $table = 'Alloted_Center_To_Counsellor';
            } elseif ($_SESSION['Role'] == 'Counsellor') {
              $university_query = " AND University_ID = " . $_SESSION['university_id'] . " AND Counsellor_ID =" . $_SESSION['ID'];
              $table = 'Alloted_Center_To_Counsellor';
            } elseif ($_SESSION['Role'] == 'Sub-Counsellor') {
              $university_query = " AND University_ID = " . $_SESSION['university_id'] . " AND Sub_Counsellor_ID =" . $_SESSION['ID'];
              $table = 'Alloted_Center_To_SubCounsellor';
            }
            $alloted_centers = $conn->query("SELECT Code FROM $table GROUP BY Code $university_query");
            if ($alloted_centers->num_rows == 0) { ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-body">
                      <center>
                        <h4>Center not alloted!</h4>
                      </center>
                    </div>
                  </div>
                </div>
              </div>
            <?php } else { ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="users-table">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Code</th>
                          <th>Email</th>
                          <th>Mobile</th>
                          <th data-orderable="false">Fee Alloted</th>
                          <th>Admissions</th>
                          <th data-orderable="false">RM</th>
                          <th data-orderable="false">Password</th>
                          <th data-orderable="false"></th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            <?php } ?>
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

        var table = $('#users-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/centers/server'
          },
          'columns': [{
              data: "Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Code",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Email",
              "render": function(data, type, row) {
                return '<div onmouseup="showEmailAgain(&#39;' + data + '&#39;,&#39;' + row.ID + '&#39;)" onmouseout="showEmailAgain(&#39;' + data + '&#39;,&#39;' + row.ID + '&#39;)">\
                <span style="cursor:pointer" title="Click to View" onmousedown="getEmail(&#39;' + row.ID + '&#39;)" id="show_email_' + row.ID + '">' + data + '</span>\
              </div>';
              }
            },
            {
              data: "Mobile",
              "render": function(data, type, row) {
                return '<div onmouseup="showMobileAgain(&#39;' + data + '&#39;,&#39;' + row.ID + '&#39;)" onmouseout="showMobileAgain(&#39;' + data + '&#39;,&#39;' + row.ID + '&#39;)">\
                <span style="cursor:pointer" title="Click to View" onmousedown="getMobile(&#39;' + row.ID + '&#39;)" id="show_mobile_' + row.ID + '">' + data + '</span>\
              </div>';
              }
            },
            {
              data: "Fee_Alloted"
            },
            {
              data: "Admission"
            },
            {
              data: "RM"
            },
            {
              data: "Password",
              "render": function(data, type, row) {
                return '<div class="row" style="width:250px !important;">\
                <div class="col-md-10">\
                  <input type="password" class="form-control" disabled="" style="border: 0ch;" value="' + data + '" id="myInput' + row.ID + '">\
                </div>\
                <div class="col-md-2">\
                  <i class="uil uil-eye pt-2 cursor-pointer" onclick="showPassword(' + row.ID + ')"></i>\
                </div>\
              </div>';
              }
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                return '<div class="button-list text-end">\
                <i class="uil uil-whatsapp icon-xs cursor-pointer" data-toggle="tooltip" data-original-title="Send WhatsApp" title="" onclick="sendWhatsApp(&#39;' + data + '&#39)"></i>\
                <i class="uil uil-plus-circle icon-xs cursor-pointer" data-toggle="tooltip" data-original-title="University Allotment" title="" onclick="allot(&#39;' + data + '&#39, &#39;lg&#39;)"></i>\
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
          "iDisplayLength": 25,
          "drawCallback": function() {
            $('[data-toggle="tooltip"]').tooltip();
          }
        };

        table.dataTable(settings);

        // search box for table
        $('#users-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });

      })
    </script>

    <script>
      function allot(id, modal) {
        $.ajax({
          url: '/app/center-master/allot-universities?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        });
      }
    </script>

    <script>
      function showPassword(id) {
        var x = document.getElementById("myInput".concat(id));
        if (x.type === "password") {
          x.type = "text";
        } else {
          x.type = "password";
        }
      }
    </script>

    <script type="text/javascript">
      function exportData() {
        var search = $('#users-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/centers/export' + url);
      }
    </script>

    <script>
      function getMobile(id) {
        $.ajax({
          url: '/app/centers/mobile',
          type: 'POST',
          data: {
            "id": id
          },
          success: function(data) {
            $('#show_mobile_' + id).html(data);
          }
        })
      }

      function sendWhatsApp(id) {
        $.ajax({
          url: '/app/centers/mobile',
          type: 'POST',
          data: {
            "id": id
          },
          success: function(data) {
            window.open('https://wa.me/+91' + data);
          }
        })
      }

      function showMobileAgain(val, id) {
        $('#show_mobile_' + id).html(val);
      }

      function getEmail(id) {
        $.ajax({
          url: '/app/centers/email',
          type: 'POST',
          data: {
            "id": id
          },
          success: function(data) {
            $('#show_email_' + id).html(data);
          }

        })
      }

      function showEmailAgain(val, id) {
        $('#show_email_' + id).html(val);
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
