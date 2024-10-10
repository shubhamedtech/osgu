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
            <form role="form" id="contact-us-form" action="/app/contact-us/store">
              <div class="row">
                <div class="col d-flex justify-content-center">
                  <div class="col-md-5">
                    <div class="card card-default">
                      <div class="card-body">
                        <p class="m-t-10">Mail us at:</p>
                        <div class="col-md-12">
                          <div class="form-group form-group-default required">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="ex: user@example.com" required>
                          </div>
                        </div>
                        <p class="m-t-20">Call us at:</p>
                        <div class="col-md-12">
                          <div class="form-group form-group-default required">
                            <label>Contact</label>
                            <input type="tel" name="contact" class="form-control" placeholder="ex: 9998777655" minlength="10" maxlength="10" onkeypress="return isNumberKey(event)" required>
                          </div>
                        </div>
                        <div class="modal-footer clearfix text-end m-t-20">
                          <div class="col-md-4 m-t-10 sm-m-t-10">
                            <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
                              <span>Save</span>
                              <span class="hidden-block">
                                <i class="pg-icon">tick</i>
                              </span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>

<script>
  $(function(){
    $('#contact-us-form').validate({
      rules: {
        email: {required:true},
        contact: {required:true},
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $("#contact-us-form").on("submit", function(e){
    if($('#contact-us-form').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
          if(data.status==200){
            notification('success', data.message);
          }else{
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', data.message);
          }
        }
      });
      e.preventDefault();
    }
  });
</script>

<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
