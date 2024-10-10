<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingAdmissionType">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseAdmissionType" aria-expanded="false" aria-controls="collapseAdmissionType">
          Admission Types
        </a>
    </div>
  </div>
  <div id="collapseAdmissionType" class="collapse" role="tabcard" aria-labelledby="headingAdmissionType">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('admission-types', 'md', <?=$university_id?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableAdmissionType">
              <thead>
                <tr>
                  <th width="50%">Name</th>
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
  var table = $('#tableAdmissionType');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/admission-types/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    'columns': [  
      { data: "Name"},
      { data: "ID",
        "render": function(data, type, row){
          return '<div class="text-end">\
            <i class="uil uil-edit icon-xs cursor-pointer" onclick="editComponents(\'admission-types\', \''+data+'\', \'md\');"></i>\
            <i class="uil uil-trash icon-xs cursor-pointer" onclick="destroyComponents(\'admission-types\', \'AdmissionType\', \''+data+'\');"></i>\
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
