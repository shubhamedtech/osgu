<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingFeeStructures">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFeeStructures" aria-expanded="false" aria-controls="collapseFeeStructures">
        Fee Structures
      </a>
    </div>
  </div>
  <div id="collapseFeeStructures" class="collapse" role="tabcard" aria-labelledby="headingFeeStructures">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('fee-structures', 'md', <?=$university_id?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableFeeStructures">
              <thead>
                <tr>
                  <th width="20%">Name</th>
                  <th data-orderable="false">Sharing</th>
                  <th data-orderable="false">Constant</th>
                  <th data-orderable="false">Applicable on</th>
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
  var table = $('#tableFeeStructures');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/fee-structures/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    'columns': [  
      { data: "Name"},
      { data: "Sharing"},
      { data: "Is_Conctant"},
      { data: "Applicable"},
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Fee-Structures\', \'FeeStructures\', \''+row.ID+'\');" type="checkbox" '+checked+' id="fee-structure-switch-'+row.ID+'">\
            <label for="fee-structure-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'fee-structures\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'fee-structures\', \'FeeStructures\', \''+data+'\');"></i>\
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
