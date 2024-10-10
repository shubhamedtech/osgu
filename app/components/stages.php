<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingStages">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseStages" aria-expanded="false" aria-controls="collapseStages">
          Stages
        </a>
    </div>
  </div>
  <div id="collapseStages" class="collapse" role="tabcard" aria-labelledby="headingStages">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('stages', 'md', '')">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableStages">
              <thead>
                <tr>
                  <th width="50%">Name</th>
                  <th data-orderable="false">Initial Stage</th>
                  <th data-orderable="false">Final Stage</th>
                  <th data-orderable="false">Re-Enquired Stage</th>
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
