<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingPageAccess">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsePageAccess" aria-expanded="false" aria-controls="collapsePageAccess">
          Page Access
        </a>
    </div>
  </div>
  <div id="collapsePageAccess" class="collapse" role="tabcard" aria-labelledby="headingPageAccess">
    <div class="card-body">
      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tablePageAccess">
              <thead>
                <tr>
                  <th width="30%">Name</th>
                  <th data-orderable="false">Inhouse</th>
                  <th data-orderable="false">Center</th>
                  <th data-orderable="false">Sub-Center</th>
                  <th data-orderable="false">Student</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
  $lms = false;
  $has_lms = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Has_LMS = 1");
  if($has_lms->num_rows>0){
    $lms = true;
  }
?>

<script type="text/javascript">
  var hasLMS = '<?=$lms?>';
  var table = $('#tablePageAccess');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/page-access/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    'columns': [  
      { data: "Name"},
      { data: "Inhouse",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeInhouseStatus(\''+row.ID+'\');" type="checkbox" '+checked+' id="inhouse-status-switch-'+row.ID+'">\
            <label for="inhouse-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Center",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeCenterStatus(\''+row.ID+'\');" type="checkbox" '+checked+' id="center-status-switch-'+row.ID+'">\
            <label for="center-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Sub_Center",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeSubCenterStatus(\''+row.ID+'\');" type="checkbox" '+checked+' id="sub-center-status-switch-'+row.ID+'">\
            <label for="sub-center-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Student",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeStudentStatus(\''+row.ID+'\');" type="checkbox" '+checked+' id="student-status-switch-'+row.ID+'">\
            <label for="student-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        },
        visible: hasLMS==1 ? true : false
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
    "iDisplayLength": 100
  };

  table.dataTable(settings);
</script>

<script type="text/javascript">
  function changeInhouseStatus(id){
    $.ajax({
      url:'/app/components/page-access/inhouse?id='+id+'&university_id=<?=$university_id?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
        }else{
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }

  function changeCenterStatus(id){
    $.ajax({
      url:'/app/components/page-access/center?id='+id+'&university_id=<?=$university_id?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
        }else{
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }

  function changeSubCenterStatus(id){
    $.ajax({
      url:'/app/components/page-access/sub-center?id='+id+'&university_id=<?=$university_id?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
        }else{
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }

  function changeStudentStatus(id){
    $.ajax({
      url:'/app/components/page-access/student?id='+id+'&university_id=<?=$university_id?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
        }else{
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }
</script>
