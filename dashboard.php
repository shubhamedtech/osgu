<?php include('includes/header-top.php'); ?>
<?php include('includes/header-bottom.php'); ?>
<?php include('includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->
    <div class="page-container ">
    <?php include('includes/topbar.php'); ?>      
      <!-- START PAGE CONTENT WRAPPER -->
      <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
          <!-- START JUMBOTRON -->
          <div class="jumbotron" data-pages="parallax">
            <div class=" container-fluid sm-p-l-0 sm-p-r-0">
              <div class="inner">
                <!-- START BREADCRUMB -->
                <ol class="breadcrumb">
                  <li class="breadcrumb-item active">Dashboard</li>
                </ol>
                <!-- END BREADCRUMB -->
              </div>
            </div>
          </div>
          <!-- END JUMBOTRON -->
          <!-- START CONTAINER FLUID -->
          <div class=" container-fluid">
            <!-- BEGIN PlACE PAGE CONTENT HERE -->
            <?php 
              if($_SESSION['Role']=='Student'){
                include ('dashboards/student.php');
              }
            ?>
            <?php if($_SESSION['Role']=='Center'){ ?>
              <div class="row">
                <div class="col-md-6">
                  <div class="card card-default">
                    <div class="card-header separator">
                      <div class="card-title">Matrix</div>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <tbody>
                            <?php $head = $conn->query("SELECT UPPER(Name) as Name, LOWER(Email) as Email, Mobile, Role FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE University_User.University_ID = ".$_SESSION['university_id']." AND Users.Role = 'University Head'");
                              if($head->num_rows>0){ 
                                $head = $head->fetch_assoc();
                                ?>
                                <tr>
                                  <td><b><?=$head['Role']?></b></td>
                                  <td><?=$head['Name']?></td>
                                  <td><?=$head['Email']?></td>
                                  <td><?=$head['Mobile']?></td>
                                </tr>
                              <?php }
                            ?>
                            <?php $counsellor = $conn->query("SELECT UPPER(Name) as Name, LOWER(Email) as Email, Mobile, Role FROM Alloted_Center_To_Counsellor LEFT JOIN Users ON Alloted_Center_To_Counsellor.Counsellor_ID = Users.ID WHERE Alloted_Center_To_Counsellor.University_ID = ".$_SESSION['university_id']." AND Alloted_Center_To_Counsellor.Code = ".$_SESSION['ID']);
                              if($counsellor->num_rows>0){
                                $counsellor = $counsellor->fetch_assoc();
                                ?>
                                  <tr>
                                    <td><b><?=$counsellor['Role']?></b></td>
                                    <td><?=$counsellor['Name']?></td>
                                    <td><?=$counsellor['Email']?></td>
                                    <td><?=$counsellor['Mobile']?></td>
                                  </tr>
                                <?php
                              }
                            ?>
                            <?php $accountant = $conn->query("SELECT UPPER(Name) as Name, LOWER(Email) as Email, Mobile, Role FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE University_User.University_ID = ".$_SESSION['university_id']." AND Users.Role = 'Accountant'");
                              if($accountant->num_rows>0){ 
                                $accountant = $accountant->fetch_assoc();
                                ?>
                                <tr>
                                  <td><b><?=$accountant['Role']?></b></td>
                                  <td><?=$accountant['Name']?></td>
                                  <td><?=$accountant['Email']?></td>
                                  <td><?=$accountant['Mobile']?></td>
                                </tr>
                              <?php }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include('includes/footer-top.php'); ?>
<?php include('includes/footer-bottom.php'); ?>
        