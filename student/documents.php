<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
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
        <div class="row">
          <?php $documents = $conn->query("SELECT * FROM Student_Documents WHERE Student_ID = " . $_SESSION['ID'] . "");
          while ($document = $documents->fetch_assoc()) {
            $images = explode("|", $document['Location']);
            foreach ($images as $image) {
              $id = uniqid();
          ?>
              <div class="col-sm-3 m-b-10" onclick="viewImage('<?= $id ?>')">
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
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
  </div>
</div>
<!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

<script>
  function viewImage(id) {
    var viewer = new Viewer(document.getElementById(id), {
      inline: false,
      toolbar: false,
      viewed() {
        viewer.zoomTo(0.6);
      },
    });
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
