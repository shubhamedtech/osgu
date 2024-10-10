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
                </ol>
                <!-- END BREADCRUMB -->
                
              </div>
            </div>
          </div>
          <!-- END JUMBOTRON -->
          <!-- START CONTAINER FLUID -->
          <div class="container-fluid p-2">
            <!-- BEGIN PlACE PAGE CONTENT HERE -->
            <?php include('../app/components/crm-settings.php'); ?>
            <!-- END PLACE PAGE CONTENT HERE -->
          </div>
          <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-top.php'); ?>

<script type="text/javascript">
function addComponents(url, modal, university_id){
    $.ajax({
      url: '/app/components/'+url+'/create?university_id='+university_id,
      type: 'GET',
      success: function(data){
        $('#'+modal+'-modal-content').html(data);
        $('#'+modal+'modal').modal('show');
      }
    })
  }

  function editComponents(url, id, modal){
    $.ajax({
      url: '/app/components/'+url+'/edit?id='+id,
      type: 'GET',
      success: function(data){
        $('#'+modal+'-modal-content').html(data);
        $('#'+modal+'modal').modal('show');
      }
    })
  }

  function changeComponentStatus(table, datatable, id){
    $.ajax({
      url: '/app/status/update',
      type: 'post',
      data: {"table": table, "id": id},
      dataType: 'json',
      success: function(data) {
        if(data.status==200){
          notification('success', data.message);
          $('#table'+datatable).DataTable().ajax.reload(null, false);;
        }else{
          notification('danger', data.message);
          $('#table'+datatable).DataTable().ajax.reload(null, false);;
        }
      }
    });
  }

  function destroyComponents(url, table, id){
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/app/components/"+url+"/destroy?id="+id,
          type: 'DELETE',
          dataType: 'json',
          success: function(data) {
            if(data.status==200){
              notification('success', data.message);
              $('#table'+table).DataTable().ajax.reload(null, false);;
            }else{
              notification('danger', data.message);
            }
          }
        });
      }
    })
  }
</script>


<!-- CRM Scripts -->
<script type="text/javascript">
  var table = $('#tableStages');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/stages/server',
    },
    'columns': [  
      { data: "Name"},
      { data: "Is_First",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeFirst('+row.ID+');" type="checkbox" '+checked+' id="stage-first-status-switch-'+row.ID+'">\
            <label for="stage-first-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Is_Last",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeLast('+row.ID+');" type="checkbox" '+checked+' id="stage-last-status-switch-'+row.ID+'">\
            <label for="stage-last-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Is_ReEnquired",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeReEnquired('+row.ID+');" type="checkbox" '+checked+' id="stage-re-enquired-status-switch-'+row.ID+'">\
            <label for="stage-re-enquired-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Stages\', \'Stages\', \''+row.ID+'\');" type="checkbox" '+checked+' id="stage-status-switch-'+row.ID+'">\
            <label for="stage-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'stages\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'stages\', \'Stages\', \''+data+'\');"></i>\
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
    "iDisplayLength": 50
  };

  table.dataTable(settings);
</script>

<script type="text/javascript">

function changeFirst(id) {
  $.ajax({
    url: '/app/components/stages/first',
    type: 'POST',
    data:{ "id": id},
    dataType: 'json',
    success: function (data) {
      if(data.status==200){
        notification('success',data.message);
        $('#tableStages').DataTable().ajax.reload(null, false);;
      }else{
        notification('danger', data.message);
        $('#tableStages').DataTable().ajax.reload(null, false);;
      }
    }
  })
}

function changeLast(id) {
  $.ajax({
    url: '/app/components/stages/last',
    type: 'POST',
    data:{ "id": id},
    dataType: 'json',
    success: function (data) {
      if(data.status==200){
        notification('success',data.message);
        $('#tableStages').DataTable().ajax.reload(null, false);;
      }else{
        notification('danger', data.message);
        $('#tableStages').DataTable().ajax.reload(null, false);;
      }
    }
  })
}

function changeReEnquired(id) {
  $.ajax({
    url: '/app/components/stages/re_enquired',
    type: 'POST',
    data:{ "id": id},
    dataType: 'json',
    success: function (data) {
      if(data.status==200){
        notification('success',data.message);
        $('#tableStages').DataTable().ajax.reload(null, false);;
      }else{
        notification('danger', data.message);
        $('#tableStages').DataTable().ajax.reload(null, false);;
      }
    }
  })
}
</script>

<!-- Reasons -->
<script type="text/javascript">
$(function(){
  var table = $("#tableReasons").DataTable({
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/reasons/server'
    },
    'columns': [  
      { data: "Name"},
      { data: "Stages"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Reasons\', \'Reasons\', \''+row.ID+'\');" type="checkbox" '+checked+' id="reason-status-switch-'+row.ID+'">\
            <label for="reason-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'reasons\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'reasons\', \'Reasons\', \''+data+'\');"></i>\
          </div>'
        }
      },
    ],
    "sDom": "<t><'row'<p i>>",
    "oLanguage": { "sEmptyTable": "Please add Reason!" },
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 500
    });
});
</script>

<!-- Sources -->
<script type="text/javascript">
  var table = $('#tableSources');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/sources/server',
    },
    'columns': [  
      { data: "Name"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Sources\', \'Sources\', \''+row.ID+'\');" type="checkbox" '+checked+' id="source-status-switch-'+row.ID+'">\
            <label for="source-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'sources\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'sources\', \'Sources\', \''+data+'\');"></i>\
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
    "iDisplayLength": 500
  };

  table.dataTable(settings);
</script>

<script type="text/javascript">
$(function(){
  var table = $("#tableSubSources").DataTable({
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/sub-sources/server'
    },
    'columns': [  
      { data: "Name"},
      { data: "Sources"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Sub_Sources\', \'SubSources\', \''+row.ID+'\');" type="checkbox" '+checked+' id="reason-status-switch-'+row.ID+'">\
            <label for="reason-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'reasons\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'reasons\', \'SubSources\', \''+data+'\');"></i>\
          </div>'
        }
      },
    ],
    "sDom": "<t><'row'<p i>>",
    "oLanguage": { "sEmptyTable": "Please add Reason!" },
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 500
    });
});
</script>

<script type="text/javascript">
  var table = $('#tableLeadAssignment');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/lead-assignments/server',
    },
    'columns': [  
      { data: "Name"},
      { data: "Description"},
      { data: "Course"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Stages\', \'Stages\', \''+row.ID+'\');" type="checkbox" '+checked+' id="stage-status-switch-'+row.ID+'">\
            <label for="stage-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'stages\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'stages\', \'Stages\', \''+data+'\');"></i>\
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
    "iDisplayLength": 500
  };

  table.dataTable(settings);
</script>
<?php include($_SERVER['DOCUMENT_ROOT'].'/includes/footer-bottom.php'); ?>
        