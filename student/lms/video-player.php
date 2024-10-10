<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/header-top.php'); ?>
<style>
  .copyright {
    background: #fff;
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
}

.stu-e-book-style {
    width: 140px;
    height: 80px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #838383;
  }

  .video-icon {
    font-size: 30px;
    text-align: center;
    color: #fff;
    position: absolute;
    /*color: currentcolor;*/
    top: 22px;
    left: 65px;
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
  $id = $_GET['id'];
  $base_url="http://".$_SERVER['HTTP_HOST']."/";
  $course_id=$_SESSION['Sub_Course_ID'];
  $student_id=$_SESSION['ID'];

  $query = "SELECT video_lectures.`id`,video_lectures.`subject_id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Sub_Courses ON Sub_Courses.ID = video_lectures.course_id LEFT JOIN Syllabi ON Syllabi.ID = video_lectures.subject_id WHERE video_lectures.id=$id ";

  $results = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($results);

  $query2 = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Sub_Courses ON Sub_Courses.ID = video_lectures.course_id LEFT JOIN Syllabi ON Syllabi.ID = video_lectures.subject_id WHERE video_lectures.status=1 AND video_lectures.subject_id=".$row['subject_id'];

  $allVideo = mysqli_query($conn, $query2);
  $videoData=array();
  while ($row1 = mysqli_fetch_assoc($allVideo)) {
    $videoData[]= $row1;
  }
  
?>
    <div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/includes/topbar.php'); ?>  
    <div class="page-content-wrapper ">
    <div class="content pb-0" style="padding-top: 40px;">

      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
        </div>
      </div>

      <div class=" container-fluid">
        <div class="card card-transparent" style="height: 100%;">
          <section class="my-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 border-top" style="padding: 0px;">
                        <video width="760" height="350" controls autoplay controlsList="nodownload" src="<?=$base_url?><?=$row['video_url']?> " type="video/<?=$row['video_type']?>"></video>
                        <h5 class="mt-2  mb-1"><b><?=ucwords($row['subject_name'])?> : </b><?=ucwords($row['unit'])?></h5>
                        <p class="video-description "><?=ucwords($row['description']) ?></p>

                    </div>
                    <div class="col-md-4 ">
                    <?php  foreach($videoData as $video){  ?>
                        <div class="row border m-1">
                            <div class="col-sm-6 mt-2 mb-2">
                                <a  href="/student/lms/video-player?id=<?php echo $video['id']; ?>" >
                                    <div class="stu-e-book-style" ><img class="thumbnail" src="<?=$base_url?><?=$video['thumbnail_url']?> "><p><i class="uil uil-play-circle video-icon" ></i></p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 mt-2 mb-2 ">
                                <p><b><?=ucwords($video['unit'])?></b></p>
                                <p><?=ucwords($video['description']) ?></p>
                            </div>
                        </div>
                      <?php } ?>

                    </div>
                </div>
            </div>
          </section>

        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>
<script type="text/javascript"></script>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
