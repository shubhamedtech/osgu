<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->
    <div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/includes/topbar.php'); ?>      
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
                    for($i=1; $i<=count($breadcrumbs); $i++) {
                      if(count($breadcrumbs)==$i): $active = "active";
                        $crumb = explode("?", $breadcrumbs[$i]);
                        echo '<li class="breadcrumb-item '.$active.'">'.$crumb[0].'</li>';
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
            <?php $details = $conn->query("SELECT * FROM Contact_Us WHERE University_ID = ".$_SESSION['university_id']."");
              if($details->num_rows>0){ 
                $details = $details->fetch_assoc();  
            ?>
                <div class="row">
                  <div class="col d-flex justify-content-center">
                    <div class="col-md-7">
                      <div class="card card-default">
                        <div class="card-body text-center">
                          <p class="m-t-10">Mail us at:</p>
                          <a href="mailto:<?=$details['Email']?>"><h1 class="text-center"><?=$details['Email']?></h1></a>
                          <p class="m-t-20">Call us at:</p>
                          <a href="tel:<?=$details['Mobile']?>"><h1 class="text-center"><?=$details['Mobile']?></h1></a>
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
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
