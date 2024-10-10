<style>
  .profile_img {
    width: 150px;
    height: 150px;
    object-fit: fill;
    margin: 10px auto;
    border: 5px solid #ccc;
    border-radius: 50%;
  }
  table, tr, th, th {
    border: none !important;
  }
</style>
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-transparent text-center separator">
        <img class="profile_img" src="<?=$_SESSION['Photo']?>" alt="">
        <h3><?=$_SESSION['Name']?></h3>
      </div>
      <div class="card-body m-t-10">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th width="30%">Student ID</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Unique_ID']?></th>
            </tr>
            <tr>
              <th width="30%">Adm. Session	</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Admission_Session']?></th>
            </tr>
            <tr>
              <th width="30%">Adm. Sem</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Duration']?></th>
            </tr>
            <tr>
              <th width="30%">Enrollment No</th>
              <th width="2%">:</th>
              <th><?php echo empty($_SESSION['Enrollment_No']) ? 'Document under verification' : $_SESSION['Enrollment_No']?></th>
            </tr>
            <tr>
              <th width="30%">Course Type</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Course_Type']?></th>
            </tr>
            <tr>
              <th width="30%">Course</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Course']?></th>
            </tr>  
            <tr>
              <th width="30%">Specialization</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Sub_Course']?></th>
            </tr>  
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header separator">
        <h5>Basic Details</h5>
      </div>
      <div class="card-body m-t-10">
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th width="30%">Father's Name</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Father_Name']?></th>
            </tr>
            <tr>
              <th width="30%">Mother's Name</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Mother_Name']?></th>
            </tr>
            <tr>
              <th width="30%">DOB</th>
              <th width="2%">:</th>
              <th><?=date("d-m-Y", strtotime($_SESSION['DOB']))?></th>
            </tr>
            <tr>
              <th width="30%">Age</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Age']?></th>
            </tr>
            <tr>
              <th width="30%">Gender</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Gender']?></th>
            </tr>  
            <tr>
              <th width="30%">Category</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Category']?></th>
            </tr>  
            <tr>
              <th width="30%">Nationality</th>
              <th width="2%">:</th>
              <th><?=$_SESSION['Nationality']?></th>
            </tr>  
            <tr>
              <th width="30%">Address</th>
              <th width="2%">:</th>
              <th><?php $address = json_decode($_SESSION['Address']); echo $address->present_address ?></th>
            </tr>  
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
