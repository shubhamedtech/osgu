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
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Payment Gateway" onclick="add('payment-gateways', 'lg')"> <i class="uil uil-plus-circle"></i></button>
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
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="payment-gateway-table">
                <thead>
                  <tr>
                    <th>University</th>
                    <th>Type</th>
                    <th>Access Key</th>
                    <th>Salt/Secret Key</th>
                    <th data-orderable="false"></th>
                    <th data-orderable="false"></th>
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
    <script>
      $(function() {
        $("#payment-gateway-table").dataTable({
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/payment-gateways/server'
          },
          'columns': [{
              data: "Type"
            },
            {
              data: "University_ID"
            },
            {
              data: "Access_Key"
            },
            {
              data: "Secret_Key"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                var checked = data == 1 ? 'checked' : '';
                return '<div class="form-check form-check-inline switch switch-lg success">\
                        <input onclick="changeStatus(&#39;Payment_Gateways&#39;, &#39;' + row.ID + '&#39;)" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
                        <label for="status-switch-' + row.ID + '">' + active + '</label>\
                      </div>';
              }
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                return '<div class="button-list text-end">\
                <i class="uil uil-edit icon-xs cursor-pointer" onclick="edit(&#39;payment-gateways&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroy(&#39;payment-gateways&#39;, &#39;' + data + '&#39)"></i>\
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
        });
      })
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
