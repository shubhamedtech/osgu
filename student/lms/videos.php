<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-top.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video-js.min.css" rel="stylesheet">
<style>
  .stu-e-book-style {
    width: 300px;
    height: 150px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #838383;
  }

  .video-icon {
    font-size: 55px;
    text-align: center;
    color: #fff;
    position: absolute;
    /*color: currentcolor;*/
    top: 45px;
    left: 125px;
    display: none;
    cursor: pointer;
  }
  .subject_name{
    font-size: 18px !important;
    font-weight: 600;
  }
  
  .container-play-btn {
  position: relative;
  width: 400px;
  height: 200px;
}

.play-btn {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  /* display: none; */
  font-size: 40px;
}

.thumbnail{
  height: inherit;
  width: inherit;
  border-radius: 10px;
  cursor: pointer;
}


.stu-e-book-style:hover .video-icon {
  display: block;
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


$result_record = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Sub_Courses ON Sub_Courses.ID = video_lectures.course_id LEFT JOIN Syllabi ON Syllabi.ID = video_lectures.subject_id WHERE video_lectures.subject_id IN ('" . implode("','", $mySyllabi) . "')  AND video_lectures.status =1  AND video_lectures.course_id=$course_id ";

$results = mysqli_query($conn, $result_record);
$videoData=array();
while ($row = mysqli_fetch_assoc($results)) {
  $videoData[]= $row;
}

?>
    <div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/includes/topbar.php'); ?>      
    <div class="page-content-wrapper ">
    <div class="content ">
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
              <?php  foreach($videoData as $video){  ?>
                <div class="col-sm-6 col-md-3 mb-3 " >
                 <a  href="/student/lms/video-player?id=<?php echo $video['id']; ?>" >
                  <div class="stu-e-book-style" ><img class="thumbnail" src="<?=$base_url?><?=$video['thumbnail_url']?> "><p><i class="uil uil-play-circle video-icon" ></i></p>
                  </div>
                </a>
                  <h5 class="mt-2 text-center mb-1"><b><?=ucwords($video['subject_name'])?> : </b><?=ucwords($video['unit'])?></h5>
                  <p class="video-description text-center"><?=ucwords($video['description']) ?></p>
                </div>
                <?php } ?>
              </div>
            

          </div>
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video.min.js"></script>

<script type="text/javascript">
  
    function subjectFilter(subject_id){
      $.ajax({
          //url: '/app/videos/students/show-list',
          url: '/student/lms/video-filter',
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
