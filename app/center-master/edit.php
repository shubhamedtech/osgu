<?php 
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';

    $id = intval($_GET['id']);
    $user = $conn->query("SELECT Name, Short_Name, Contact_Name, Code, Email, Mobile, Alternate_Mobile, Address, Pincode, State, City, District FROM Users WHERE ID = $id");
    $user = mysqli_fetch_assoc($user);
  }
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Edit <span class="semi-bold"></span>Center</h5>
</div>
<form role="form" id="form-edit-center-master" action="/app/center-master/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
  <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Jhon Doe" value="<?=$user['Name']?>" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: JD" value="<?=$user['Short_Name']?>" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Contact Person Name</label>
          <input type="text" name="contact_person_name" class="form-control" placeholder="ex: Jhon" value="<?=$user['Contact_Name']?>" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="ex: user@example.com" value="<?=$user['Email']?>" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Contact</label>
          <input type="tel" name="contact" class="form-control" placeholder="ex: 9998777655" onkeypress="return isNumberKey(event)" value="<?=$user['Mobile']?>" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default">
          <label>Alternate Contact</label>
          <input type="tel" name="alternate_contact" class="form-control" placeholder="ex: 9998777656" onkeypress="return isNumberKey(event)" value="<?=$user['Alternate_Mobile']?>">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="form-group form-group-default required">
          <label>Address</label>
          <input type="text" name="address" class="form-control" placeholder="ex: 23 Street, California" value="<?=$user['Address']?>" required>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Pincode</label>
          <input type="tel" name="pincode" maxlength="6" class="form-control" placeholder="ex: 123456" onkeypress="return isNumberKey(event)" onkeyup="getRegion(this.value);" value="<?=$user['Pincode']?>" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>City</label>
          <select class="full-width" style="border: transparent;" name="city" id="city">
            
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>District</label>
          <select class="full-width" style="border: transparent;" name="district" id="district">
            
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>State</label>
          <input type="text" name="state" class="form-control" placeholder="ex: California" id="state" readonly required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label>Photo*</label>
        <input type="file" name="photo" accept="image/png, image/jpg, image/jpeg, image/svg">
      </div>
    </div> 
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Update</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function(){
    $('#form-edit-center-master').validate({
      rules: {
        name: {required:true},
        short_name: {required:true},
        contact_person_name: {required:true},
        email: {required:true},
        contact: {required:true},
        address: {required:true},
        pincode: {required:true},
        city: {required:true},
        district: {required:true},
        state: {required:true},
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  function getRegion(pincode){
    if(pincode.length==6){
      $.ajax({
        url: '/app/regions/cities?pincode='+pincode,
        type:'GET',
        success: function(data) {
          $('#city').html(data);
          <?php if(!empty($user['City'])){ ?>
            $('#city').val('<?=$user['City']?>');
          <?php } ?>
        }
      });

      $.ajax({
        url: '/app/regions/districts?pincode='+pincode,
        type:'GET',
        success: function(data) {
          $('#district').html(data);
          <?php if(!empty($user['District'])){ ?>
            $('#district').val('<?=$user['District']?>');
          <?php } ?>
        }
      });

      $.ajax({
        url: '/app/regions/state?pincode='+pincode,
        type:'GET',
        success: function(data) {
          $('#state').val(data);
        }
      })
    }
  }

  <?php if(!empty($user['Pincode'])){ ?>
    getRegion('<?=$user['Pincode']?>');
  <?php } ?>

  $("#form-edit-center-master").on("submit", function(e){
    if($('#form-edit-center-master').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      formData.append('id', '<?=$id?>');
      $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if(data.status==200){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#users-table').DataTable().ajax.reload(null, false);
            }else{
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
      });
      e.preventDefault();
    }
  });
</script>
