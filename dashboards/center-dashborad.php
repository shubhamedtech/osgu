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
            </ol>
            <!-- END BREADCRUMB -->
            <p class="text-danger">Last date of admission without fine has extended to 05th NOV 2023.Enroll all admissions within this date.</p>
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 col-xl-3 col-lg-4">
                    <div class="card custom-card">
                        <div class="card-body dash1">
                            <div class="d-flex">
                                <p class="mb-1 tx-inverse">Total students</p>
                                <div class="ml-auto">
                                    <i class="fas fa-chart-line fs-20 text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <?php
                                    $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For =  " . $_SESSION['ID'] . " ");
                                    $records = mysqli_fetch_assoc($all_count);
                                    $totalRecords = $records['allcount'];
                                ?>
                                <h3 class="dash-25"><?=$totalRecords?><span><i class="uil uil-users-alt"></i></span></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-3 col-lg-4">
                    <div class="card custom-card">
                        <div class="card-body dash1">
                            <div class="d-flex">
                                <p class="mb-1 tx-inverse">Total programs</p>
                                <div class="ml-auto">
                                    <i class="fas fa-chart-line fs-20 text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <?php
                                    $all_count = $conn->query("SELECT COUNT(Sub_Courses.ID) as allcount FROM Sub_Courses LEFT JOIN Center_Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID WHERE Sub_Courses.University_ID = " . $_SESSION['university_id'] . " ");
                                    $records = mysqli_fetch_assoc($all_count);
                                    $totalRecords = $records['allcount'];
                                ?>
                                <h3 class="dash-25"><?=$totalRecords?><span><i class="uil uil-book-open"></i></span></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-xl-3 col-lg-4">
                    <div class="card custom-card">
                        <div class="card-body dash1">
                            <div class="d-flex">
                                <p class="mb-1 tx-inverse">Total Revenue</p>
                                <div class="ml-auto">
                                    <i class="fas fa-chart-line fs-20 text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <?php
                                    $all_counts = $conn->query("SELECT SUM(Amount) as totalamount FROM Payments WHERE Status = 1  AND Added_By = " . $_SESSION['ID'] . " ");
                                    $records = mysqli_fetch_assoc($all_counts);
                                    $totalRecords = $records['totalamount'];
                                ?>
                                <h3 class="dash-25"><?=$totalRecords?><span><iconify-icon icon="uil:rupee-sign"></iconify-icon></span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Recent added Students</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student Code</th>
                                            <th>DOB</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $centers = $conn->query("SELECT * FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For =  " . $_SESSION['ID'] . " ");
                                            if($centers->num_rows>0){
                                            while ($row = $centers->fetch_assoc()) {
                                        ?>
                                            <tr>
                                                <td><?= $row['First_Name']?></td>
                                                <td><?= $row['Unique_ID']?></td>
                                                <td><?= $row['DOB']?></td>
                                                <td><?= $row['Created_At']?></td>
                                                <td><?php  if( $row['Status'] == 1){  ?>  <span class="badge badge-success">Active</span> 
                                                    <?php  } else {  ?>  <span class="badge badge-danger">Inactive</span>
                                                    <?php  } ?>  
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Recent added courses</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>Code</th>
                                            <th>Eligibility</th>
                                            <th>Added At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $courses = $conn->query("SELECT * FROM Sub_Courses LEFT JOIN Center_Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID WHERE Sub_Courses.University_ID = " . $_SESSION['university_id'] . " ");
                                            if($courses->num_rows>0){
                                            while ($row = $courses->fetch_assoc()) {
                                        ?>
                                            <tr>
                                                <td><?= $row['Name']?></td>
                                                <td><?= $row['Short_Name']?></td>
                                                <td><?= $row['Eligibility']?></td>
                                                <td><?= $row['Created_At']?></td>
                                                <td><?php  if( $row['Status'] == 1){  ?>  <span class="badge badge-success">Active</span> 
                                                    <?php  } else {  ?>  <span class="badge badge-danger">Inactive</span>
                                                    <?php  } ?>  
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
