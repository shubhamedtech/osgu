<?php ini_set('display_errors', 1);
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
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
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">Lead Details</li>
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
        $lead_id = str_replace("W1Ebt1IhGN3ZOLplom9I", "", base64_decode($_GET['id']));
        $lead = $conn->query("SELECT Leads.ID,Lead_Status.User_ID, Lead_Status.Unique_ID, UPPER(Leads.Name) as Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Extra,Leads.Address,Cities.`Name` as City,States.`Name` as State,Countries.`Name` as Country,Lead_Status.University_ID as University_ID,Universities.Name as Universities,Courses.Short_Name as Courses,Sub_Courses.Name as Sub_Courses,Stages.Name as Stages,Reasons.Name as Reasons,Sources.Name as Sources,Sub_Sources.Name as Sub_Sources,Users.Name as `User`,Users.Code,Lead_Status.Created_At,Lead_Status.Updated_At FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID = Cities.ID LEFT JOIN States ON Leads.State_ID = States.ID LEFT JOIN Countries ON Leads.Country_ID = Countries.ID WHERE Lead_Status.ID = $lead_id");
        if ($lead->num_rows == 0) {
          header("Location: leads.php");
        }
        $lead = $lead->fetch_assoc();
        ?>
        <div class="row d-flex justify-content-center">
          <div class="col-md-4">
            <div class="card card-transparent">
              <div class="card-header bg-transparent text-center">
                <img class="profile_img" src="/assets/img/default-user.png" alt="" height="88">
                <h5><?= $lead['Name'] ?></h5>
                <h6><?= $lead['Unique_ID'] ?></h6>
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="lead_details_page">
          <div class="col-lg-12">
            <div class="card-body">
              <div class="card card-transparent">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                  <li class="nav-item">
                    <a class="active" data-toggle="tab" data-target="#journey" href="#"><span>Journey</span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#activities" href="#"><span>Activities</span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#follow_ups" href="#"><span>Follow Ups</span></a>
                  </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active bg-contrast-lower " id="journey">
                    <div class="content">
                      <div class="container-fluid sm-p-l-5">
                        <div class="timeline-container top-circle">
                          <section class="timeline">
                            <?php $journies = $conn->query("SELECT 'History',Data,Lead_Histories.Lead_ID as Follow_Up_Status,Lead_Histories.Lead_ID as Follow_Up_Remark,CONCAT(Users.`Name`,' (',Users.Code,')')AS User_ID,Lead_Histories.Created_At FROM Lead_Histories LEFT JOIN Users ON Lead_Histories.User_ID=Users.ID WHERE Lead_Histories.Lead_ID=" . $lead['ID'] . " AND Lead_Histories.User_ID=" . $lead['User_ID'] . " UNION SELECT 'Follow_Up',Follow_Ups.`At`,Follow_Ups.Status,Follow_Ups.Remark,CONCAT(Users.`Name`,' (',Users.Code,')')AS User_ID,Follow_Ups.Created_At FROM Follow_Ups LEFT JOIN Users ON Follow_Ups.User_ID=Users.ID WHERE Follow_Ups.Lead_ID=" . $lead['ID'] . " AND Follow_Ups.User_ID=" . $lead['User_ID'] . " ORDER BY Created_At DESC");
                            while ($journey = $journies->fetch_assoc()) {
                              if ($journey['History'] == 'History') {
                                $data = json_decode($journey['Data'], true);
                            ?>
                                <div class="timeline-block">
                                  <div class="timeline-point small">
                                  </div>
                                  <div class="timeline-content">
                                    <div class="card social-card share full-width ">
                                      <div class="card-description">
                                        <h6 class="fs-15 mt-0 mb-2">Lead Updated</h6>
                                        <?php foreach ($data as $key => $value) {
                                          if (!in_array($key, ['Updated_At', 'Created_At'])) { ?>
                                            <p class="text-muted fs-12" style="margin: 0px"><strong><?= $key ?>:</strong> <?= $value ?></p>
                                        <?php }
                                        } ?>
                                      </div>
                                    </div>
                                    <div class="event-date">
                                      <small class="fs-12 hint-text"><?php echo date("dS M Y h:i A", strtotime($journey['Created_At'])) ?></small>
                                    </div>
                                  </div>
                                </div>
                              <?php } else { ?>
                                <div class="timeline-block">
                                  <div class="timeline-point small">
                                  </div>
                                  <div class="timeline-content">
                                    <div class="card social-card share full-width ">
                                      <div class="card-description">
                                        <h6 class="fs-15 mt-0 mb-2">Follow-Up</h6>
                                        <p class="text-muted fs-12" style="margin: 0px">Added Follow Up on <strong><?php echo date("dS M Y h:i A", strtotime($journey['Data'])) ?></strong></p>
                                        <p class="text-muted fs-12" style="margin: 0px">Remark: <strong><?= $journey['Follow_Up_Remark'] ?></strong></p>
                                        <p class="text-muted fs-12" style="margin: 0px">Status: <strong><?php if ($journey['Follow_Up_Status'] == 0) { ?>
                                              <span class="text-danger">Missed</span>
                                            <?php } else { ?>
                                              <span class="text-success">Attend</span>
                                            <?php } ?></strong></p>
                                      </div>
                                    </div>
                                    <div class="event-date">
                                      <small class="fs-12 hint-text"><?php echo date("dS M Y h:i A", strtotime($journey['Created_At'])) ?></small>
                                    </div>
                                  </div>
                                </div>
                            <?php }
                            } ?>

                          </section>
                          <!-- timeline -->
                        </div>
                        <!-- -->
                      </div>
                      <!-- END CONTAINER FLUID -->
                    </div>
                  </div>
                  <div class="tab-pane bg-contrast-lower" id="activities">
                    <div class="content">
                      <div class="container-fluid sm-p-l-5">
                        <div class="timeline-container top-circle">
                          <section class="timeline">

                          </section>
                          <!-- timeline -->
                        </div>
                        <!-- -->
                      </div>
                      <!-- END CONTAINER FLUID -->
                    </div>
                  </div>
                  <div class="tab-pane bg-contrast-lower" id="follow_ups">
                    <div class="content">
                      <div class="container-fluid sm-p-l-5">
                        <div class="timeline-container top-circle">
                          <section class="timeline">
                            <?php $follow_ups = $conn->query("SELECT Follow_Ups.`At`, Follow_Ups.Status, Follow_Ups.Remark, Follow_Ups.Created_At FROM Follow_Ups WHERE Lead_ID = " . $lead['ID'] . " AND `User_ID` = " . $lead['User_ID'] . " ORDER BY Created_At DESC");
                            while ($follow_up = $follow_ups->fetch_assoc()) { ?>
                              <div class="timeline-block">
                                <div class="timeline-point small">
                                </div>
                                <div class="timeline-content">
                                  <div class="card social-card share full-width ">
                                    <div class="card-description">
                                      <h6 class="fs-15 mt-0 mb-2">Marked Follow Up On: <?php echo date("dS M Y h:i A", strtotime($follow_up['At'])) ?></h6>
                                      <p class="text-muted fs-12" style="margin: 0px">Remark: <strong><?= $follow_up['Remark'] ?></strong></p>
                                      <p class="text-muted fs-12" style="margin: 0px">Status: <strong><?php print $follow_up['Status'] == 0 ? '<span class="text-danger">Missed</span>' : '<span class="text-success">Attend</span>' ?></strong></p>
                                    </div>
                                  </div>
                                  <div class="event-date">
                                    <small class="fs-12 hint-text"><?php echo date("dS M Y h:i A", strtotime($follow_up['Created_At'])) ?></small>
                                  </div>
                                </div>
                              </div>
                            <?php } ?>
                          </section>
                          <!-- timeline -->
                        </div>
                        <!-- -->
                      </div>
                      <!-- END CONTAINER FLUID -->
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
      <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
