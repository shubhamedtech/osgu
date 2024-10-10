<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingSchemes">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSchemes" aria-expanded="false" aria-controls="collapseSchemes">
          Schemes
        </a>
    </div>
  </div>
  <div id="collapseSchemes" class="collapse" role="tabcard" aria-labelledby="headingSchemes">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('schemes', 'md', <?=$university_id?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableSchemes">
              <thead>
                <tr>
                  <th width="30%">Name</th>
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
  var table = $('#tableSchemes');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/schemes/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    'columns': [  
      { data: "Name"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Schemes\', \'Schemes\', \''+row.ID+'\');" type="checkbox" '+checked+' id="scheme-status-switch-'+row.ID+'">\
            <label for="scheme-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'schemes\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'schemes\', \'Schemes\', \''+data+'\');"></i>\
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
</script>
