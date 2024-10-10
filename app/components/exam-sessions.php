<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingExamSessions">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseExamSessions" aria-expanded="false" aria-controls="collapseExamSessions">
        Exam Sessions
      </a>
    </div>
  </div>
  <div id="collapseExamSessions" class="collapse" role="tabcard" aria-labelledby="headingExamSessions">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('exam-sessions', 'lg', <?= $university_id ?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableExamSessions">
              <thead>
                <tr>
                  <th width="20%">Name</th>
                  <th>Admission Sessions</th>
                  <th data-orderable="false">Re-Reg</th>
                  <th data-orderable="false">Back-Paper</th>
                  <th data-orderable="false">Status</th>
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
  var table = $('#tableExamSessions');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url': '/app/components/exam-sessions/server',
      type: 'POST',
      "data": function(data) {
        data.university_id = '<?= $university_id ?>';
      },
    },
    'columns': [{
        data: "Name"
      },
      {
        data: "Admission_Session"
      },
      {
        data: "RR_Status",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          var statusSwitch = '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeRRStatus(\'' + row.ID + '\');" type="checkbox" ' + checked + ' id="le-status-switch-' + row.ID + '">\
            <label for="le-status-switch-' + row.ID + '">' + active + '</label>\
          </div>';
          var lastDate = row.RR_Last_Date.length > 0 ? row.RR_Last_Date : 'N/A';
          var lastDateEditButton = '<i class="uil uil-edit m-l-10 cursor-pointer" onclick="updateLastDate(&#39;RR&#39;, ' + row.ID + ')"></i>'
          var lastDateForRR = '<div>\
          <span>Last Date: ' + lastDate + lastDateEditButton + '</span>\
          </div>';
          return statusSwitch + lastDateForRR;
        }
      },
      {
        data: "BP_Status",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          var statusSwitch = '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeBPStatus(\'' + row.ID + '\');" type="checkbox" ' + checked + ' id="ct-status-switch-' + row.ID + '">\
            <label for="ct-status-switch-' + row.ID + '">' + active + '</label>\
          </div>';
          var lastDate = row.BP_Last_Date.length > 0 ? row.BP_Last_Date : 'N/A';
          var lastDateEditButton = '<i class="uil uil-edit m-l-10 cursor-pointer" onclick="updateLastDate(&#39;BP&#39;, ' + row.ID + ')"></i>'
          var lastDateForBP = '<div>\
          <span>Last Date: ' + lastDate + lastDateEditButton + '</span>\
          </div>';
          return statusSwitch + lastDateForBP;
        }
      },
      {
        data: "Status",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Exam_Sessions\', \'ExamSessions\', \'' + row.ID + '\');" type="checkbox" ' + checked + ' id="exam-session-status-switch-' + row.ID + '">\
            <label for="exam-session-status-switch-' + row.ID + '">' + active + '</label>\
          </div>';
        }
      },
      {
        data: "ID",
        "render": function(data, type, row) {
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'exam-sessions\', \'' + data + '\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'exam-sessions\', \'ExamSessions\', \'' + data + '\');"></i>\
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

  function changeRRStatus(id) {
    $.ajax({
      url: '/app/components/exam-sessions/rr_status?id=' + id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
          $('#tableExamSessions').DataTable().ajax.reload(null, false);
        } else {
          notification('danger', data.message);
        }
      }
    })
  }

  function changeBPStatus(id) {
    $.ajax({
      url: '/app/components/exam-sessions/bp_status?id=' + id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
          $('#tableExamSessions').DataTable().ajax.reload(null, false);
        } else {
          notification('danger', data.message);
        }
      }
    })
  }

  function updateLastDate(type, id) {
    $.ajax({
      url: '/app/components/exam-sessions/create-last-date',
      type: 'POST',
      data: {
        id,
        type
      },
      success: function(data) {
        $("#md-modal-content").html(data);
        $("#mdmodal").modal("show");
      }
    })
  }
</script>