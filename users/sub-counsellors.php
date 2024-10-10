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
                    <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="uil uil-down-arrow"></i></button>
                    <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add" onclick="add('sub-counsellors','lg')"> <i class="uil uil-plus-circle"></i></button>
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
                    <input type="text" id="users-search-table" class="form-control pull-right" placeholder="Search">
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover nowrap" id="users-table">
                    <thead>
                      <tr>
                        <th data-orderable="false"></th>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Mobile</th>
                        <th>University</th>
                        <th data-orderable="false">Alloted Centers</th>
                        <th data-orderable="false">Admissions</th>
                        <th data-orderable="false">Password</th>
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
    
      var table = $('#users-table');

      var settings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url':'/app/sub-counsellors/server'
        },
        'columns': [  
          { data: "Photo",
            "render": function(data, type, row){
              return '<span class="thumbnail-wrapper d48 circular inline">\
      					<img src="'+data+'" alt="" data-src="'+data+'"\
      						data-src-retina="'+data+'" width="32" height="32">\
      				</span>';
            }
          },
          { data: "Name",
            "render": function(data, type, row){
              return '<strong>'+data+'</strong>';
            }
          },
          { data: "Code",
            "render": function(data, type, row){
              return '<strong>'+data+'</strong>';
            }
          },
          { data: "Mobile"},
          { data: "University"},
          { data: "Center"},
          { data: "Admission"},
          { data: "Password",
            "render": function(data, type, row){
              return '<div class="row" style="width:250px !important;">\
                <div class="col-md-10">\
                  <input type="password" class="form-control" disabled="" style="border: 0ch;" value="'+data+'" id="myInput'+row.ID+'">\
                </div>\
                <div class="col-md-2">\
                  <i class="uil uil-eye pt-2 cursor-pointer" onclick="showPassword('+row.ID+')"></i>\
                </div>\
              </div>';
            }
          },
          { data: "Status",
            "render": function(data, type, row){
              var active = data==1 ? 'Active' : 'Inactive';
              var checked = data==1 ? 'checked' : '';
              return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(&#39;Users&#39;, &#39;'+row.ID+'&#39;)" type="checkbox" '+checked+' id="status-switch-'+row.ID+'">\
                <label for="status-switch-'+row.ID+'">'+active+'</label>\
              </div>';
            }
          },
          { data: "ID",
            "render": function(data, type, row){
              return '<div class="button-list text-end">\
                <i class="uil uil-plus-circle icon-xs cursor-pointer" data-toggle="tooltip" data-placement="top" title="Allot University" onclick="allot(&#39;'+data+'&#39, &#39;lg&#39;)"></i>\
                <i class="uil uil-edit icon-xs cursor-pointer" data-toggle="tooltip" data-placement="top" title="Edit" onclick="edit(&#39;sub-counsellors&#39;, &#39;'+data+'&#39, &#39;lg&#39;)"></i>\
                <i class="uil uil-trash icon-xs cursor-pointer" data-toggle="tooltip" data-placement="top" title="Delete" onclick="destroy(&#39;sub-counsellors&#39;, &#39;'+data+'&#39)"></i>\
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
        "iDisplayLength": 25,
        "drawCallback": function( settings ) {
          $('[data-toggle="tooltip"]').tooltip();
        },
      };

      table.dataTable(settings);

      // search box for table
      $('#users-search-table').keyup(function() {
          table.fnFilter($(this).val());
      });
    
  })
</script>

<script>

  function allot(id, modal){
    $.ajax({
      url: '/app/sub-counsellors/allot-universities?id='+id,
      type: 'GET',
      success: function(data){
        $('#'+modal+'-modal-content').html(data);
        $('#'+modal+'modal').modal('show');
      }
    });
  }

  function showPassword(id) {
    var x = document.getElementById("myInput".concat(id));
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  }
</script>

<script type="text/javascript">
  function exportData(){
    var search = $('#users-search-table').val();
    var url = search.length>0 ? "?search="+search : "";
    window.open('/app/sub-counsellors/export'+url);
  }
</script>

<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
        