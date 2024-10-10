<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<?php 
  $base_url="http://".$_SERVER['HTTP_HOST']."/";
?>
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  
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
              <div class="row">
                <div class="col-xs-7" style="margin-right: 10px;">
                  <input type="text" id="video_lectures-search-table" class="form-control pull-right p-2 fw-bold" placeholder="Search">
                </div>
                <div class="col-xs-5" style="margin-right: 10px;">
                  <button class="btn btn-primary p-2 "  data-toggle="tooltip" data-original-title="Add videos" onclick="add('videos','lg')"> <i class="uil uil-plus-circle"></i>Add</button>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="video_lectures-table">
                <thead>
                  <tr>
                  <th>Subject</th>
                  <th>Course</th>
                  <th>Semester</th>
                  <th>Unit</th>
                  <th>video</th>
                  <th>Status</th>
                  <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
        $(function() {
            var role = '<?= $_SESSION['Role'] ?>';
            var show = role == 'Administrator' ? true : false;
            var table = $('#video_lectures-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/videos/data-list'
                },
                'columns': [
                    {
                        data: "subject_name"
                    },
                    {
                        data: "course_name"
                    },
                    {
                        data: "semester"
                    },
                    {
                        data: "unit"
                    },
                    {
                        data: "video_url",
                        render: function(data,type,row) { 
                            return '<div class="col-sm-6 mt-2 mb-2"><a href="/student/lms/video-player?id='+row.ID+'"><div class="stu-e-book-style"><img class="thumbnail" src="<?=$base_url?>'+row.thumnail_url+'"><p><i class="uil uil-play-circle video-icon"></i></p></div></a></div>';
                          //return '<video  width="120px" height="80px" controls="controls"><source src="'+'<?=$base_url?>'+row.video_url+'" type="video/'+row.video_type+'" /></video>' ;
                        }
                    },
                    {
                      data: "status",
                      "render": function(data, type, row) {
                        var active = data == 1 ? 'Active' : 'Inactive';
                        var checked = data == 1 ? 'checked' : '';
                        return '<div class="form-check form-check-inline switch switch-lg success">\
                                <input onclick="changeStatus('+"'video_lectures'"+', '+row.ID+','+"'status'"+')" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
                                <label for="status-switch-' + row.ID + '">' + active + '</label>\
                              </div>';
                      }
                    },
                    {
                      data: "ID",
                      "render": function(data, type, row) {
                        return '<div class="button-list text-end">\
                <i class="uil uil-edit icon-xs cursor-pointer" onclick="edit(&#39;videos&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                        <i class="uil uil-trash icon-xs cursor-pointer" onclick="changeStatus('+"'video_lectures'"+', '+data+','+"'status'"+',2)"></i>\
                      </div>'
                      }
                    },
                ],
                "sDom": "<t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                "aaSorting": [],
                "iDisplayLength": 5,
            };
            table.dataTable(settings);
            // search box for table
            $('#video_lectures-search-table').keyup(function() {
              table.fnFilter($(this).val());
            });
          })
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
