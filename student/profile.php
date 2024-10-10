<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .profile_img {
    width: 150px;
    height: 150px;
    object-fit: fill;
    margin: 10px auto;
    border: 5px solid #ccc;
    border-radius: 50%;
  }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.5.0/viewer.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.5.0/viewer.js"></script>
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
          <div class="col-md-4">
            <div class="card card-transparent">
              <div class="card-header bg-transparent text-center">
                <img class="profile_img" src="<?= $_SESSION['Photo'] ?>" alt="">
                <h3><?= $_SESSION['Name'] ?></h3>
                <h6><?= $_SESSION['Unique_ID'] ?></h6>
                <h6><?= $_SESSION['Course_Sub_Course'] ?></h6>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="card card-transparent">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                <li class="nav-item">
                  <a href="#" class="active" data-toggle="tab" data-target="#personal_detials"><span>Personal Details</span></a>
                </li>
                <li class="nav-item">
                  <a href="#" data-toggle="tab" data-target="#communication_details"><span>Communication Details</span></a>
                </li>
                <li class="nav-item">
                  <a href="#" data-toggle="tab" data-target="#qualification_details"><span>Qualification Details</span></a>
                </li>
                <li class="nav-item">
                  <a href="#" data-toggle="tab" data-target="#documents"><span>Documents</span></a>
                </li>
                <li class="nav-item">
                  <a href="#" data-toggle="tab" data-target="#form"><span>Application Form</span></a>
                </li>
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane fade show active" id="personal_detials">
                  <div class="row column-seperation">
                    <div class="table-responsive">
                      <table class="table table-borderless">
                        <tr>
                          <th width="30%">Father's Name</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Father_Name'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Mother's Name</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Mother_Name'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">DOB</th>
                          <th width="2%">:</th>
                          <th><?= date("d-m-Y", strtotime($_SESSION['DOB'])) ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Age</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Age'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Gender</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Gender'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Category</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Category'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Marital Status</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Marital_Status'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Religion</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Religion'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Aadhar No.</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Aadhar_Number'] ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Nationality</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Nationality'] ?></th>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="communication_details">
                  <div class="row">
                    <div class="table-responsive">
                      <table class="table table-borderless">
                        <tr>
                          <th width="30%">Email</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Email'] ?></th>
                        </tr>
                        <?php if (!empty($_SESSION['Alternate_Email'])) { ?>
                          <tr>
                            <th width="30%">Alternate Email</th>
                            <th width="2%">:</th>
                            <th><?= $_SESSION['Alternate_Email'] ?></th>
                          </tr>
                        <?php } ?>
                        <tr>
                          <th width="30%">Mobile</th>
                          <th width="2%">:</th>
                          <th><?= $_SESSION['Contact'] ?></th>
                        </tr>
                        <?php if (!empty($_SESSION['Alternate_Email'])) { ?>
                          <tr>
                            <th width="30%">Alternate Mobile</th>
                            <th width="2%">:</th>
                            <th><?= $_SESSION['Alternate_Contact'] ?></th>
                          </tr>
                        <?php } ?>
                        <?php $address = json_decode($_SESSION['Address']); ?>
                        <tr>
                          <th width="30%">Address</th>
                          <th width="2%">:</th>
                          <th><?= $address->present_address ?></th>
                        </tr>
                        <tr>
                          <th width="30%">City</th>
                          <th width="2%">:</th>
                          <th><?= $address->present_city ?></th>
                        </tr>
                        <tr>
                          <th width="30%">District</th>
                          <th width="2%">:</th>
                          <th><?= $address->present_district ?></th>
                        </tr>
                        <tr>
                          <th width="30%">State</th>
                          <th width="2%">:</th>
                          <th><?= $address->present_state ?></th>
                        </tr>
                        <tr>
                          <th width="30%">Pincode</th>
                          <th width="2%">:</th>
                          <th><?= $address->present_pincode ?></th>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="qualification_details">
                  <div class="row">
                    <div class="col-lg-12">
                      <?php $academics = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = " . $_SESSION['ID'] . "");
                      while ($academic = $academics->fetch_assoc()) {
                      ?>
                        <div class="row m-t-20">
                          <div class="col-md-12">
                            <h6><?= $academic['Type'] ?></h6>
                            <div class="table-responsive">
                              <table class="table table-borderless">
                                <tr>
                                  <th>Board: <br><?= $academic['Board/Institute'] ?></th>
                                  <th>Passing Year: <br><?= $academic['Year'] ?></th>
                                  <th>Result Status: <br><?= $academic['Total_Marks'] ?></th>
                                <tr>
                              </table>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="documents">
                  <div class="row">
                    <?php $documents = $conn->query("SELECT * FROM Student_Documents WHERE Student_ID = " . $_SESSION['ID'] . "");
                    while ($document = $documents->fetch_assoc()) {
                      $images = explode("|", $document['Location']);
                      foreach ($images as $image) {
                        $id = uniqid();
                    ?>
                        <div class="col-sm-3 m-b-10" onclick="viewImage('<?= $id ?>'); viewImage('<?= $id ?>');">
                          <div class="ar-1-1">
                            <div class="widget-2 card no-margin">
                              <div class="card-body">
                                <img src="<?= $image ?>" alt="<?= $document['Type'] ?>" class="cursor-pointer" width="100%" height="100%" style="object-fit:fill" id="<?= $id ?>">
                                <div class="pull-bottom bottom-left bottom-right padding-25">
                                  <span class="label font-montserrat fs-11"><?= $document['Type'] ?></span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    <?php }
                    }
                    ?>
                  </div>
                </div>
                <div class="tab-pane fade" id="form">
                  <div class="row">
                    <div class="col-md-12">
                      <iframe src="/forms/<?= $_SESSION['university_id'] ?>/?student_id=<?= base64_encode($_SESSION['ID'] . 'W1Ebt1IhGN3ZOLplom9I') ?>" frameborder=0 width="100%" height="700px"></iframe>
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
      function viewImage(id) {
        $("#" + id).dblclick();
        var viewer = new Viewer(document.getElementById(id), {
          inline: false,
          toolbar: false,
          viewed() {
            viewer.zoomTo(0.6);
          },
        });
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
