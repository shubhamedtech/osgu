<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-top.php'); ?>
<style>
  .select2-selection--multiple{
    overflow: hidden !important;
    height: auto !important;
  }
</style>
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
            <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
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
                </ol>
                <!-- END BREADCRUMB -->
                
              </div>
            </div>
          </div>
          <!-- END JUMBOTRON -->
          <!-- START CONTAINER FLUID -->
          <div class=" container-fluid container-fixed-lg" id="main-content">
            <!-- BEGIN PlACE PAGE CONTENT HERE -->
            <div class="row">
              <div class="col-md-12">
                <form id="lead_assignment_form" method="POST" action="ajax_admin/ajax_lead_assignment/store">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group form-group-default required">
                        <label>Name</label>
                        <input type="text" autocomplete="off" id="name" name="name" class="form-control" placeholder="ex: MBA Rule" required>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group form-group-default">
                        <label>Description</label>
                        <textarea autocomplete="off" id="description" rows="1" name="description" class="form-control" placeholder=""></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group form-group-default required">
                        <label>Course</label>
                        <select class="full-width" style="border: transparent;" name="course" id="course" onchange="getUniversities(this.value)" required>
                          <option value="">Select</option>
                          <?php
                            $courses = $conn->query("SELECT Name FROM Courses GROUP BY Name ORDER BY Name");
                            while ($course = $courses->fetch_assoc()){ ?>
                              <option value="<?php echo $course['Name']; ?>"><?php echo $course['Name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group row mt-3 mb-2" id="departments">
                    
                  </div>
                  <div class="modal-footer clearfix text-end">
                    <div class="col-md-4 m-t-10 sm-m-t-10">
                      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
                        <span>Save</span>
                        <span class="hidden-block">
                          <i class="pg-icon">tick</i>
                        </span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>

<script>
  $(function(){
    $("#lead_assignment_form").on("submit", function(e){
        var formData = new FormData(this);
        $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if(data.status==200){
                $('.modal').modal('hide');
                toastr.success(data.message);
                $('#lead-assignment-table').DataTable().ajax.reload(null, false);;
              }else{
                toastr.error(data.message);
              }
            }
        });
        e.preventDefault();
    });
  });

  function getUniversities(course){
    $.ajax({
      url:'/app/components/lead-assignments/universities?course='+course,
      type:'get',
      success: function(data) {
        $('#departments').html(data);
      }
    })
  }
</script>

<script>
  $('#source_10').select2({
    placeholder: "If Source(s)",
  });
</script>

<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
        
