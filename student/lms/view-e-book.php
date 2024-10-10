<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-top.php'); ?>
<style>
  .copyright {
    background: #fff;
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
}
</style>

<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->

<?php 
  $id = $_GET['id'];
  $base_url="https://".$_SERVER['HTTP_HOST']."/";
  $course_id=$_SESSION['Sub_Course_ID'];
  $student_id=$_SESSION['ID'];

  $query="SELECT e_books.`id`, e_books.`file_type`, e_books.`file_path`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, e_books.`status` FROM e_books LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.course_id LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id WHERE e_books.id=$id ";

$results = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($results);
  
?>

    <div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/includes/topbar.php'); ?>  
    <div class="page-content-wrapper ">
    <div class="content pb-0" style="padding-top: 25px;">

      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
        </div>
      </div>
     
      <div class=" container-fluid">
        <div class="card card-transparent">
          <div class="card-header">
            <b><?=$row['subject_name']?></b>  E-Book  
            
            <div class="pull-right">
              <div class="col-xs-7 " style="margin-right: 10px;">
              <a class="btn btn-danger p-2 " href="/student/lms/e-books" data-toggle="tooltip" data-original-title="Back" > <i class="uil uil-arrow-circle-left"></i>Back</a>
              </div>
            </div>
            <div class="clearfix"></div>
            </div>
            <div class="card-body">
            <embed src="<?=$base_url?><?=$row['file_path']?>#toolbar=0&scrollbar=1&&navpanes=0&controls=0" type="application/pdf" width="100%" height="560px" />
            </div>
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>
<script type="text/javascript">

</script>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
