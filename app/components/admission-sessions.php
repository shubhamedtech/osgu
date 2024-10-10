<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingAdmissionSessions">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAdmissionSessions" aria-expanded="false" aria-controls="collapseAdmissionSessions">
          Admission Sessions
        </a>
    </div>
  </div>
  <div id="collapseAdmissionSessions" class="collapse" role="tabcard" aria-labelledby="headingAdmissionSessions">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('admission-sessions', 'md', <?=$university_id?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableAdmissionSessions">
              <thead>
                <tr>
                  <th width="20%">Name</th>
                  <th>Exam Session</th>
                  <th>Scheme</th>
                  <th data-orderable="false">Status</th>
                  <th data-orderable="false">Current</th>
                  <th data-orderable="false">LE Status</th>
                  <th data-orderable="false">CT Status</th>
                  <th data-orderable="false"></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var table = $('#tableAdmissionSessions');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/admission-sessions/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    stateSave: true,
    stateSaveParams: function ( settings, data ) {
      data.columns[5].visible = settings.json.permissions.LE_Status;
      data.columns[6].visible = settings.json.permissions.CT_Status;
    },
    'columns': [  
      { data: "Name"},
      { data: "Exam_Session"},
      { data: "Scheme"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Admission-Sessions\', \'AdmissionSessions\', \''+row.ID+'\');" type="checkbox" '+checked+' id="session-status-switch-'+row.ID+'">\
            <label for="session-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Current_Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeCurrentStatus(\'<?=$university_id?>\', \''+row.ID+'\');" type="checkbox" '+checked+' id="current-session-status-switch-'+row.ID+'">\
            <label for="current-session-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "LE_Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeLEStatus(\''+row.ID+'\');" type="checkbox" '+checked+' id="le-status-switch-'+row.ID+'">\
            <label for="le-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "CT_Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeCTStatus(\''+row.ID+'\');" type="checkbox" '+checked+' id="ct-status-switch-'+row.ID+'">\
            <label for="ct-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'admission-sessions\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'admission-sessions\', \'AdmissionSessions\', \''+data+'\');"></i>\
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

  function changeCurrentStatus(university_id, id){
    $.ajax({
      url:'/app/components/admission-sessions/current?id='+id+'&university_id='+university_id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
          $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
        }else{
          notification('danger', data.message);
        }
      }
    })
  }

  function changeLEStatus(id){
    $.ajax({
      url:'/app/components/admission-sessions/le_status?id='+id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
          $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
        }else{
          notification('danger', data.message);
        }
      }
    })
  }

  function changeCTStatus(id){
    $.ajax({
      url:'/app/components/admission-sessions/ct_status?id='+id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if(data.status == 200){
          notification('success', data.message);
          $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
        }else{
          notification('danger', data.message);
        }
      }
    })
  }
</script>
