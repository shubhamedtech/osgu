<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
  session_start();
  if(isset($_SESSION["Password"]) || isset($_SESSION["Unique_ID"])){
    header("Location: /dashboard");
  }
  include ('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Login | <?=$app_title?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/toastr.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
    window.onload = function()
    {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
    </script>
  </head>
  <body class="fixed-header ">
    <div class="login-wrapper">
      <!-- START Login Background Pic Wrapper-->
      <div class="bg-pic" style="background-image: url('<?=$login_cover?>'); background-repeat: no-repeat; background-size: cover;">
        <!-- START Background Caption-->
        <div class="bg-caption text-white m-b-20" style="padding: 200px;">
          <h1 class="semi-bold text-white">
            <img src="assets/img/university/svu_logo.png" alt="">
					</h1>
          <p class="small">
          
          </p>
        </div>
        <!-- END Background Caption-->
      </div>
      <!-- END Login Background Pic Wrapper-->
      <!-- START Login Right Container-->
      <div class="login-container bg-white">
        <div class="p-l-50 p-r-50 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
          <?php if(!empty($dark_logo)){ ?>
            <img src="<?=$dark_logo?>" alt="logo" data-src="<?=$dark_logo?>" data-src-retina="<?=$dark_logo?>" height="50">
          <?php } ?>
          <h2 class="p-t-25">Welcome</h2>
          <p class="mw-80 m-t-5">Sign in to your account</p>
          <!-- START Login Form -->
          <form id="form-login" class="p-t-15" role="form" autocomplete="off" action="app/login/login">
            <!-- START Form Control-->
            <div class="form-group form-group-default">
              <label>User Name</label>
              <div class="controls">
                <input type="text" name="username" style="text-transform: uppercase" placeholder="Username" class="form-control" required>
              </div>
            </div>
            <!-- END Form Control-->
            <!-- START Form Control-->
            <div class="form-group form-group-default">
              <label>Password</label>
              <div class="controls">
                <input type="password" class="form-control" name="password" placeholder="Credentials" required>
              </div>
            </div>
            <!-- START Form Control-->
            <div class="row">
              <div class="col-md-6 no-padding sm-p-l-10">
                <div class="form-check">
                  <input type="checkbox" checked value="1" id="checkbox1">
                  <label for="checkbox1">Remember me</label>
                </div>
              </div>
              <div class="col-md-6 d-flex align-items-center justify-content-end">
                <button aria-label="" class="btn btn-primary btn-lg m-t-10" type="submit">Sign in</button>
              </div>
            </div>
            <div class="m-b-5 m-t-30">
              <a href="#" class="normal">Lost your password?</a>
            </div>
            
            <!-- END Form Control-->
          </form>
        </div>
      </div>
      <!-- END Login Right Container-->
    </div>
    <!-- BEGIN VENDOR JS -->
    <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
    <!--  A polyfill for browsers that don't support ligatures: remove liga.js if not needed-->
    <script src="assets/plugins/liga.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
    <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
    <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
    <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
    <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
    <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <!-- END VENDOR JS -->
    <script src="pages/js/pages.min.js"></script>
    <script src="assets/js/toastr.min.js"></script>
        
    <script>
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "3000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
    </script>

    <script>
    $(function()
    {
      $('#form-login').validate();
      $("#form-login").on("submit", function(e){
        if($('#form-login').valid()){
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
                    toastr.success(data.message);
                    window.setTimeout(function() {
                      window.location.href = data.url;
                    }, 1000);
                  }else{
                    $(':input[type="submit"]').prop('disabled', false);
                    toastr.error(data.message);
                  }
              }
          });
          e.preventDefault();
        }
      });
    })
    </script>
  </body>
</html>
