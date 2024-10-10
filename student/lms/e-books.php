<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-top.php'); ?>
<style>
  .stu-e-book-style {
    width: 300px;
    height: 150px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #F3B95F;
  }

  .e-book-icon {
    font-size: 80px;
    text-align: center;
    color: white;
  }
  .subject_name{
    font-size: 18px !important;
    font-weight: 600;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/menu.php'); ?>
    <!-- START PAGE-CONTAINER -->
<?php 
  $base_url="https://".$_SERVER['HTTP_HOST']."/";

  // $course_id=$_SESSION['Course_ID'];
  $course_id=$_SESSION['Sub_Course_ID'];
  $student_id=$_SESSION['ID'];

  $Syllabi = "SELECT Sub_Courses.ID,Sub_Courses.Mode_Id,Sub_Courses.Min_Duration, Modes.Name as mode ,Syllabi.Name,Syllabi.ID as subject_id from Syllabi  LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Modes ON Sub_Courses.Mode_Id = Modes.ID  WHERE Syllabi.Sub_Course_ID = $course_id";
  $Syllabi = mysqli_query($conn, $Syllabi);
  $mySyllabi=array();
  $subjectData=array();
  while ($row = mysqli_fetch_assoc($Syllabi)) {
    $mySyllabi[]= $row['subject_id'];
    $subjectData[]=$row;
  }

  
$query="SELECT e_books.`id`, e_books.`file_type`, e_books.`file_path`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, e_books.`status` FROM e_books LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.course_id LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id WHERE e_books.subject_id IN ('" . implode("','", $mySyllabi) . "')  AND e_books.status !=2 AND e_books.course_id=$course_id AND e_books.status=1";

$results = mysqli_query($conn, $query);
$eBookData=array();
while ($row = mysqli_fetch_assoc($results)) {
  $eBookData[]= $row;
}
?>


    <div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/includes/topbar.php'); ?>  
    <div class="page-content-wrapper ">
    <div class="content ">

    <?php 
      // echo "<pre>";
      // print_r($subjectData); die;
    ?>
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <!-- <div class="inner">
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php 
              // $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              // for ($i = 1; $i <= count($breadcrumbs); $i++) {
              //   if (count($breadcrumbs) == $i) : $active = "active";
              //     $crumb = explode("?", $breadcrumbs[$i]);
              //     echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
              //   endif;
              // }
              ?>
              <div>
              </div>
            </ol>
            
          </div> -->
        </div>
      </div>
     
      <div class=" container-fluid">
        <div class="card card-transparent">
          <div class="card-header">
            
            <?php 
              $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo $crumb[0];
                endif;
              }
              ?>

            <div class="pull-right">
              <div class="col-xs-7 " style="margin-right: 10px;">
                  <div class="form-group  required">
                      <select class="form-control"  onchange="subjectFilter(this.value)" style="width: 200px;">
                          <option value="">Subject</option>
                          <?php foreach($subjectData as $subj){ ?>
                              <option value="<?=$subj['subject_id']?>"><?=$subj['Name']?></option>
                          <?php } ?>
                      </select>
                  </div>
              </div>
            </div>

            <div class="clearfix"></div>
          </div>
          
          <div class="card-body">
            <div class="row" id="data_list">
              <?php  foreach($eBookData as $eBook){  ?>
                <div class="col-sm-6 col-md-3 mb-3 " >
                  <div class="stu-e-book-style" ><p><i class="uil uil-book-open e-book-icon" ></i></p>
                  <p class="subject_name"><span ><?php echo $eBook['subject_name']; ?></span></p>
                  </div>
                  <p class="mt-2 " style="text-align:center;"><a class="btn btn-dark" href="/student/lms/view-e-book?id=<?php echo $eBook['id']; ?>" >View </a></p>
                </div>
                <?php } ?>
            </div> 
          </div>
          
          
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>
<script type="text/javascript">
  
    function subjectFilter(subject_id){
      $.ajax({
          url: '/student/lms/e-books-filter',
          type: 'POST',
          dataType:'text',
          data: {
            "subject_id": subject_id,
            'course_id':"<?=$course_id?>"
          },
          success: function(result) {
            if(result!=0){
                $('#data_list').html(result);
            } else{
                $('#data_list').html('<div class="col-md-12"><h3 style="text-align: center;">Data not available!</h3></div>');
            }
          }
        })
    }

    </script>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
