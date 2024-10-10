<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingReasons">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseReasons" aria-expanded="false" aria-controls="collapseReasons">
          Reasons
        </a>
    </div>
  </div>
  <div id="collapseReasons" class="collapse" role="tabcard" aria-labelledby="headingReasons">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('reasons', 'md', '')">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableReasons">
              <thead>
                <tr>
                  <th width="50%">Name</th>
                  <th>Stage</th>
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
