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
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Semester</label>
            <select class="full-width" style="border: transparent;" id="semester" onchange="getTable()">
              <option value="">Choose</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row" id="assignments"></div>
      <!-- END PLACE PAGE CONTENT HERE -->
    </div>
    <!-- END CONTAINER FLUID -->
  </div>
  <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>

<script type="text/javascript">
  function getSemester(id){
    $.ajax({
      url: '/app/assignments/semester?id='+id,
      type:'GET',
      success: function(data) {
        $("#semester").html(data);
      }
    })
  }

  getSemester(<?=$_SESSION['Sub_Course_ID']?>);
</script>

<script type="text/javascript">
  function getTable(){
    var course_id = '<?=$_SESSION['Sub_Course_ID']?>';
    var semester = $('#semester').val();
    if(course_id.length>0 && semester.length>0) {
      $.ajax({
        url:'/app/assignments/syllabus?course_id='+course_id+'&semester='+semester,
        type:'GET',
        success: function(data) {
          $('#assignments').html(data);
        }
      })
    }else{
      $('#assignments').html('');
    }
  }

  function removeTable(){
    $('#assignments').html('');
  }
</script>

<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
