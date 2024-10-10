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
              <li class="breadcrumb-item active">Center Ledger</li>
              <div></div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <?php if ($_SESSION['Role'] != 'Center') { ?>
          <div class="row d-flex justify-content-center">
            <div class="col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="form-group form-group-default required">
                    <label>Centers</label>
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="center" onchange="getLedger()">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <div class="form-group form-group-default required">
                    <label>Adm Session</label>
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="admission_session_id" onchange="getLedger()">
                      <option value="">Select</option>
                      <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']);
                        while($session = $sessions->fetch_assoc()){
                          echo '<option value="'.$session['ID'].'">'.$session['Name'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php }else{ ?>
          <input type="hidden" id="center" value="<?=$_SESSION['ID']?>">
          <div class="row d-flex justify-content-center">
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <div class="form-group form-group-default required">
                    <label>Adm Session</label>
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="admission_session_id" onchange="getLedger()">
                      <option value="">Select</option>
                      <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']);
                        while($session = $sessions->fetch_assoc()){
                          echo '<option value="'.$session['ID'].'">'.$session['Name'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>

        <div class="row m-t-20">
          <div class="col-lg-12">
            <div class="card card-transparent">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs nav-tabs-linetriangle" id="all-counter" data-init-reponsive-tabs="dropdownfx">
                <li class="nav-item" id="counter_student">
                  <?php  
                  $counter = array();
                  if($_SESSION['ID']){
                    $center_id = 'AND Students.Added_For = '. $_SESSION['ID'] . ''; 
                  }else {
                    $center_id = ''; 
                  }

                  $students_count = $conn->query("SELECT ID FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Step = 4 AND Process_By_Center IS NULL $center_id");

                  

                  while ($student = $students_count->fetch_assoc()) {
                    $invoices_created = $conn->query("SELECT ID FROM Invoices WHERE Student_ID = " . $student['ID'] . " AND User_ID = " . $_SESSION['ID'] . " AND University_ID = " . $_SESSION['university_id'] . "");
                    if($invoices_created->num_rows != 0){
                      $counter = [];
                    }else {
                      $counter[] = $student;
                    }
                    
                  }
                  ?>
                  <a class="active" data-toggle="tab" data-target="#students" href="#"><span>Students</span>-<span id="applied_student_count"><?=count($counter) ?></span></a>
                </li>
                <li class="nav-item" id="counter_pending">
                  <?php  
                  $pending_counter = array();
                  $id = $_SESSION['ID'];
                  $added_for[] = $id;

                  $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = $id");
                  while ($downline = $downlines->fetch_assoc()) {
                    $added_for[] = $downline['User_ID'];
                  }
                
                  $users = implode(",", array_filter($added_for));
                
                  $already = array();
                  $already_ids = array();
                  $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
                  while ($invoice = $invoices->fetch_assoc()) {
                    $already[$invoice['Student_ID']] = $invoice['Duration'];
                    $already_ids[] = $invoice['Student_ID'];
                  }
                
                  $query = empty($already_ids) ? " AND ID IS NULL" : " AND ID IN (" . implode(',', $already_ids) . ")";
                
                  $sessionQuery = "";
                  if(isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])){
                    $admission_session_id = intval($_GET['admission_session_id']);
                    $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
                  }
                
                  $students_count = $conn->query("SELECT ID FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4 $sessionQuery AND Process_By_Center IS NULL $query");
                  while ($student = $students_count->fetch_assoc()) {
                    $pending_counter[] = $student;
                  }
                  ?>
                  <a data-toggle="tab" data-target="#pending" href="#"><span>Pending</span>-<span id="pending_student_count"><?=count($pending_counter) ?></span></a>
                </li>
                <li class="nav-item" id="counter_processed">
                  <?php  
                  $processed_countrer = array();
                  if($_SESSION['ID']){
                    $center_id = 'AND Students.Added_For = '. $_SESSION['ID'] . ''; 
                  }else {
                    $center_id = ''; 
                  }
                  $students_count = $conn->query("SELECT ID FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Step = 4 AND Process_By_Center IS NOT NULL AND Payment_Received IS NULL $center_id");
                  while ($student = $students_count->fetch_assoc()) {
                    $processed_countrer[] = $student;
                  }  
                  ?>
                  <a data-toggle="tab" data-target="#processed" href="#"><span>Processed</span>-<span id="processed_student_count"><?=count($processed_countrer) ?></span></a>
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
                <div class="tab-pane" id="pending">
                  <div class="row">
                    <div class="col-md-12 text-center">
                      Please select center!
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="processed">
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
      function getLedger() {
        $("#all-counter").html('');
        var id = $("#center").val();
        var admission_session_id = $("#admission_session_id").val();
        getStudentList(id, admission_session_id);
        getPendingList(id, admission_session_id);
        getProcessedList(id, admission_session_id);
        getCounter(id, 'all-count');
      }

      function getStudentList(id, admission_session_id) {
        $.ajax({
          url: '/app/centers/ledgers/student-wise/students?id=' + id +'&admission_session_id='+admission_session_id,
          type: 'GET',
          success: function(data) {
            $("#students").html(data);
          }
        })
      }

      function getCounter(id, status){
        $.ajax({
          url: '/app/centers/ledgers/student-wise/students-counter?id=' + id+'&count_status='+status,
          type: 'GET',
          success: function(data) {
            $("#all-counter").html(data);
          }
        })
      }

      function getPendingList(id, admission_session_id) {
        $.ajax({
          url: '/app/centers/ledgers/student-wise/pending?id=' + id +'&admission_session_id='+admission_session_id,
          type: 'GET',
          success: function(data) {
            $("#pending").html(data);
          }
        })
      }

      function getProcessedList(id, admission_session_id) {
        $.ajax({
          url: '/app/centers/ledgers/student-wise/processed?id=' + id +'&admission_session_id='+admission_session_id,
          type: 'GET',
          success: function(data) {
            $("#processed").html(data);
          }
        })
      }

      getCenterList('center');

      <?php if ($_SESSION['Role'] == 'Center') {
        echo 'getLedger()';
      } ?>
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
