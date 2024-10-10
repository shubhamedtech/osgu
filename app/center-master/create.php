<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold"></span>Center</h5>
</div>
<form role="form" id="form-add-center-master" action="/app/center-master/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div id="user-type-div" class="col-md-12">
        <div class="form-group form-group-default required">
          <label>User Type</label>
          <select class="full-width" style="border: transparent;" name="user_type" id="user_type" onchange="checkType(this.value)">
            <option value="1">Outsourced</option>
            <option value="0">Inhouse</option>
          </select>
        </div>
      </div>
      <div id="center_code_manual">
  
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Jhon Doe" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: JD" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Contact Person Name</label>
          <input type="text" name="contact_person_name" class="form-control" placeholder="ex: Jhon" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="ex: user@example.com" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Contact</label>
          <input type="tel" name="contact" class="form-control" maxlength="10" placeholder="ex: 9998777655" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default">
          <label>Alternate Contact</label>
          <input type="tel" name="alternate_contact" class="form-control" placeholder="ex: 01202123222" onkeypress="return isNumberKey(event)">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="form-group form-group-default required">
          <label>Address</label>
          <input type="text" name="address" class="form-control" placeholder="ex: 23 Street, California" required>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Pincode</label>
          <input type="tel" name="pincode" maxlength="6" class="form-control" placeholder="ex: 123456" onkeypress="return isNumberKey(event)" onkeyup="getRegion(this.value);" required>
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
      <span>Save</span>
      <span class="hidden-block">
        <i class="pg-icon">tick</i>
      </span>
    </button>
  </div>
</div>
</form>

<script>
  $(function(){
    $('#form-add-center-master').validate({
      rules: {
        user_type: {required:true},
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
        }
      });

      $.ajax({
        url: '/app/regions/districts?pincode='+pincode,
        type:'GET',
        success: function(data) {
          $('#district').html(data);
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

  function checkType(value){
    $('#center_code_manual').html('');
    if(value==0){
      $('#user-type-div').removeClass('col-md-12');
      $('#user-type-div').addClass('col-md-6');
      $('#center_code_manual').addClass('col-md-6');
      $('#center_code_manual').html('<div class="form-group form-group-default required">\
          <label>Code</label>\
          <input type="text" name="center_code" class="form-control" placeholder="ex: 0001" required>\
        </div>');
    }else{
      $('#user-type-div').removeClass('col-md-6');
      $('#user-type-div').addClass('col-md-12');
    }
  }

  $("#form-add-center-master").on("submit", function(e){
    if($('#form-add-center-master').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
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
