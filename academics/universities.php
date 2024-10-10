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
                  <div class="text-end">
                    <span class="text-muted bold cursor-pointer" onclick="add('universities','lg')"> Add</sapn>
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

            <div class="card card-transparent">
              <div class="card-header">
                <div class="pull-right">
                  <div class="col-xs-12">
                    <input type="text" id="universities-search-table" class="form-control pull-right" placeholder="Search">
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover nowrap" id="universities-table">
                    <thead>
                      <tr>
                        <th data-orderable="false">Logo</th>
                        <th>Name</th>
                        <th>Vertical</th>
                        <th data-orderable="false"></th>
                        <th data-orderable="false"></th>
                        <th data-orderable="false"></th>
                        <th data-orderable="false"></th>
                        <th data-orderable="false"></th>
                        <th data-orderable="false"></th>
                        <th data-orderable="false"></th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>

            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>
<script type="text/javascript">
  $(function(){
    
      var table = $('#universities-table');

      var settings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url':'/app/universities/server'
        },
        'columns': [  
          { data: "Logo",
            "render": function(data, type, row){
              return '<img src="'+data+'" width="60px" />'
            }
          },
          { data: "Short_Name"},
          { data: "Vertical"},
          { data: "Status",
            "render": function(data, type, row){
              var active = data==1 ? 'Active' : 'Inactive';
              var checked = data==1 ? 'checked' : '';
              return '<div class="form-check form-check-inline switch switch-lg success">\
                        <input onclick="changeStatus(&#39;Universities&#39;, &#39;'+row.ID+'&#39;)" type="checkbox" '+checked+' id="status-switch-'+row.ID+'">\
                        <label for="status-switch-'+row.ID+'">'+active+'</label>\
                      </div>';
            }
          },
          { data: "Is_B2C",
            "render": function(data, type, row){
              var type = data==1 ? 'University is delaing<br>with Students.' : data==2 ? 'University is dealing<br>with both Outsourced Partners and Students.' : 'University is dealing<br>with Outsourced Partners.';
              return type;
            }
          },
          { data: "Is_Vocational",
            "render": function(data, type, row){
              var active = data==1 ? 'Has Vocational Courses' : 'Don\'t have Vocational Courses';
              var checked = data==1 ? 'checked' : '';
              return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeColumnStatus(&#39;'+row.ID+'&#39;, &#39;Is_Vocational&#39;)" type="checkbox" '+checked+' id="vocational-switch-'+row.ID+'">\
                <label for="vocational-switch-'+row.ID+'">'+active+'</label>\
              </div>';
            }
          },
          { data: "Has_LMS",
            "render": function(data, type, row){
              var active = data==1 ? 'Has LMS' : 'Don\'t have LMS';
              var checked = data==1 ? 'checked' : '';
              return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeColumnStatus(&#39;'+row.ID+'&#39;, &#39;Has_LMS&#39;)" type="checkbox" '+checked+' id="lms-switch-'+row.ID+'">\
                <label for="lms-switch-'+row.ID+'">'+active+'</label>\
              </div>';
            }
          },
          { data: "Has_Unique_Center",
            "render": function(data, type, row){
              var active = data==1 ? 'Has Unique Center Code' : 'Don\'t have Unique Center Code';
              var checked = data==1 ? 'checked' : '';
              var character = 'XXXX';
              var centerCode = row.Center_Suffix!='' ? '<span>Center Code: <b>'+row.Center_Suffix+character+'</b></span>' : '<span>Please create Center Code</span>'; 
              var edit = data==1 ? '<span><i class="uil uil-cog icon-xs cursor-pointer" onclick="addCenterCode('+row.ID+')"></i></span>' : '';
              var generator = data==1 ? centerCode+edit : edit;
              return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeColumnStatus(&#39;'+row.ID+'&#39;, &#39;Has_Unique_Center&#39;)" type="checkbox" '+checked+' id="center-switch-'+row.ID+'">\
                <label for="center-switch-'+row.ID+'">'+active+'</label>\
              </div><br><p>'+generator+'</p>';
            }
          },
          { data: "Has_Unique_StudentID",
            "render": function(data, type, row){
              var active = data==1 ? 'Has unique Student ID' : 'Don\'t have a unique Student ID';
              var checked = data==1 ? 'checked' : '';
              var studentID = row.Max_Character!='' ? '<span>Student ID: <b>'+row.ID_Suffix+row.Max_Character+'</b></span>' : '<span>Please create Student ID</span>';
              var edit = data==1 ? '<span><i class="uil uil-cog icon-xs cursor-pointer" onclick="addStudentID('+row.ID+')"></i></span>' : '';
              var generator = data==1 ? studentID+edit : edit;
              return '<div class="form-check form-check-inline switch switch-lg success">\
                        <input onclick="changeColumnStatus(&#39;'+row.ID+'&#39;, &#39;Has_Unique_StudentID&#39;)" type="checkbox" '+checked+' id="student-switch-'+row.ID+'">\
                        <label for="student-switch-'+row.ID+'">'+active+'</label>\
                      </div><br><p>'+generator+'</p>';
            }
          },
          { data: "ID",
            "render": function(data, type, row){
              return '<div class="button-list text-end">\
                <i class="uil uil-edit icon-xs cursor-pointer" onclick="edit(&#39;universities&#39;, &#39;'+data+'&#39, &#39;lg&#39;)"></i>\
                <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroy(&#39;universities&#39;, &#39;'+data+'&#39)"></i>\
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
        "iDisplayLength": 5
      };

      table.dataTable(settings);

      // search box for table
      $('#universities-search-table').keyup(function() {
          table.fnFilter($(this).val());
      });
    
  })
</script>

<script type="text/javascript">
  function changeColumnStatus(id, column) {
    $.ajax({
      url: '/app/universities/status',
      type: 'post',
      data:{ id:id, column:column },
      dataType:'json',
      success: function(data) {
        if(data.status==200){
          notification('success', data.message);
          $('#universities-table').DataTable().ajax.reload(null, false);
        }else{
          notification('danger', data.message);
          $('#universities-table').DataTable().ajax.reload(null, false);
        }
      }
    })
  }

  function addStudentID(id){
    $.ajax({
      url: '/app/universities/student-id?id='+id,
      type: 'GET',
      success: function(data) {
        $('#lg-modal-content').html(data);
        $('#lgmodal').modal('show');
      }
    })
  }

  function addCenterCode(id){
    $.ajax({
      url: '/app/universities/center-code?id='+id,
      type: 'GET',
      success: function(data) {
        $('#lg-modal-content').html(data);
        $('#lgmodal').modal('show');
      }
    })
  }
</script>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
        