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
        <div class="row d-flex justify-content-center">
          <div class="col-md-8">
            <div class="card">
              <div class="card-body">
                <div class="form-group form-group-default required">
                  <label>Centers</label>
                  <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="center" onchange="getLedger(this.value)">
                    <option value="">Select</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row m-t-20">
          <div class="col-lg-12">
            <div class="card card-transparent">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                <li class="nav-item">
                  <a class="active" data-toggle="tab" data-target="#students" href="#"><span>Students</span></a>
                </li>
                <li class="nav-item">
                  <a data-toggle="tab" data-target="#invoices" href="#"><span>Invoices</span></a>
                </li>
                <li class="nav-item">
                  <a data-toggle="tab" data-target="#ledger" href="#"><span>Ledger</span></a>
                </li>
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane active" id="students">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="invoices">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="ledger">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
              </div>
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
      function getLedger(id) {
        getStudentList(id);
        getInvoiceList(id);
        getCenterLedger(id);
      }

      function getStudentList(id) {
        $.ajax({
          url: '/app/centers/ledgers/lump-sum/students?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#students").html(data);
          }
        })
      }

      function getInvoiceList(id) {
        $.ajax({
          url: '/app/centers/ledgers/lump-sum/invoices?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#invoices").html(data);
          }
        })
      }

      function getCenterLedger(id) {
        $.ajax({
          url: '/app/centers/ledgers/lump-sum/ledger?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#ledger").html(data);
          }
        })
      }

      function showStudents(id) {
        $.ajax({
          url: '/app/centers/ledgers/lump-sum/list?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }

      getCenterList('center');
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
