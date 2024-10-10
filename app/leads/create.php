<?php 
  require '../../includes/db-config.php';
  session_start();
?>
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold"></span>Lead</h5>
</div>
<form id="lead_form" method="POST" action="/app/leads/store">
  <div class="modal-body">
  
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>University</label>
          <select class="full-width" style="border: transparent;" name="university_id" id="university_id" onchange="getDepartmentDetails(this.value)">
            <option value="">Choose</option>
            <?php
              if($_SESSION['Role'] != 'Administrator'){
                $university_query = " AND Universities.ID = ".$_SESSION['university_id']."";
              }
              $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE Is_B2C != 0 ".$_SESSION['UniversityQuery']);
              while($university = $universities->fetch_assoc()) { ?>
                <option value="<?=$university['ID']?>"><?=$university['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <!-- Users -->
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Owner</label>
          <select class="full-width" style="border: transparent;" name="user" id="user">
            <option value="">Choose</option>
          </select>
        </div>
      </div>
    </div>
      
    <div class="row">
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Full Name</label>
          <input type="text" class="form-control" autocomplete="off" name="name" placeholder="Jhon Doe" required />
        </div>
      </div>
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Email</label>
          <input type="email" class="form-control" autocomplete="off" id="email" name="email" placeholder="jhon@example.com" onkeyup="checkLeadEmail(this.value, 'leadEmailError')" />
        </div>
        <p class="text-danger font-weight-bold" id="leadEmailError"></p>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Mobile</label>
          <input type="tel" class="form-control" autocomplete="off" maxlength="10" id="mobile" name="mobile" onkeypress="return isNumberKey(event)" placeholder="789654XXXX" required/>
        </div>
        <p class="text-danger font-weight-bold" id="leadMobileError"></p>
      </div>
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Alternate Mobile</label>
          <input type="tel" class="form-control" autocomplete="off" id="alternate_mobile" maxlength="10" name="alternate_mobile" onkeypress="return isNumberKey(event)" placeholder="789654XXXX" />
        </div>
        <p class="text-danger font-weight-bold" id="leadAltMobileError"></p>
      </div>
    </div>

    <div class="row">
      <!-- Stage -->
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Stage</label>
          <select class="full-width" style="border: transparent;" name="stage" id="stage" onchange="getReasons(this.value)" required>
            <option value="">Choose</option>
            <?php $stages = $conn->query("SELECT ID, Name FROM Stages WHERE Status = 1");
              while($stage = $stages->fetch_assoc()){?>
                <option value="<?=$stage['ID']?>"><?=$stage['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <!-- Reason -->
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Reason</label>
          <select class="full-width" style="border: transparent;" name="reason" id="reason" required>
            <option value="">Choose</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Source -->
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Source</label>
          <select class="full-width" style="border: transparent;" name="source" id="source" onchange="getSubSources(this.value)" required>
            <option value="">Choose</option>
            <?php $sources = $conn->query("SELECT ID, Name FROM Sources WHERE Status = 1"); 
              while($source = $sources->fetch_assoc()){?>
                <option value="<?=$source['ID']?>"><?=$source['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <!-- Sub-Source -->
      <div class="col-lg-6">
        <div class="form-group form-group-default">
          <label>Sub-Source</label>
          <select class="full-width" style="border: transparent;" name="sub_source" id="sub_source">
            <option value="">Choose</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="form-group form-group-default required">
          <label>Course</label>
          <select class="full-width" style="border: transparent;" name="course" id="course" onchange="getSubCourse(this.value)" required>
            <option value="">Choose</option>
          </select>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="form-group form-group-default">
          <label>Sub-Course</label>
          <select class="full-width" style="border: transparent;" name="sub_course" id="sub_course">
            <option value="">Choose</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6">
        <div class="form-group form-group-default">
          <label>Country</label>
          <select class="full-width" style="border: transparent;" name="country" id="country" onchange="getStates(this.value)">
            <option value="">Choose</option>
            <?php $countries = $conn->query("SELECT ID, Name FROM Countries");
              while ($country = $countries->fetch_assoc()){ ?>
                <option value="<?php echo $country['ID']; ?>"><?php echo $country['Name']; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="form-group form-group-default">
          <label>State</label>
          <select class="full-width" style="border: transparent;" name="state" id="state">
            <option value="">Choose</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="form-group form-group-default">
          <label>Extra Information</label>
          <textarea class="form-control" rows="3" placeholder="Birthday, Remark, etc" name="extra_info" id="extra_info"></textarea>
        </div>
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
  // Mobile Code
  // const phoneInputField = document.querySelector("#mobile");
  // const phoneInput = window.intlTelInput(phoneInputField, {
  //   initialCountry: "in",
  //   utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
  // });
  // const altPhoneInputField = document.querySelector("#alternate_mobile");
  // const altPhoneInput = window.intlTelInput(altPhoneInputField, {
  //   initialCountry: "in",
  //   utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
  // });

    // Form
  $("#lead_form").on("submit", function(e){
    var formData = new FormData(this);
    $.ajax({
      url: this.action,
      type: 'post',
      data: formData,
      cache:false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(data) {
        if(data.status==200){
          $('.modal').modal('hide');
          notification('success', data.message);
          $('#leads-table').DataTable().ajax.reload(null, false);
        }else{
          notification('danger', data.message);
        }
      }
    });
    e.preventDefault();
  });

  // mobile check
  $("#mobile").keyup(function(){
    var error_id = "leadMobileError";
    if(isMobile(value)){
      var university = $('#university_id').val();
      $.ajax({
        url: '/app/leads/check_lead_mobile?mobile='+value+'&university='+university,
        type: 'GET',
        dataType: 'JSON',
        success: function(data) {
          if(data.status==302){
            $('#leadMobileError').html(data.message);
            $(':input[type="submit"]').prop('disabled', true);
          }else{
            $(':input[type="submit"]').prop('disabled', false);
            $('#leadMobileError').html('');
          }
        }
      })
    }else{
      $(':input[type="submit"]').prop('disabled', false);
      $('#'+error_id).html('');
    }
  });

  // alternate_mobile check
  $("#alternate_mobile").keyup(function(){
    var error_id = "leadAltMobileError";
    var value = altPhoneInput.getNumber();
    if(value.includes('+91')){
      $("#alternate_mobile").attr('maxlength','10');
    }else{
      $("#alternate_mobile").attr('maxlength','13');
    }
    if(isMobile(value)){
      var university = $('#university_id').val();
      $.ajax({
        url: '/app/leads/check_lead_mobile?mobile='+value+'&university='+university,
        type: 'GET',
        dataType: 'JSON',
        success: function(data) {
          if(data.status==302){
            $('#leadAltMobileError').html(data.message);
            $(':input[type="submit"]').prop('disabled', true);
          }else{
            $(':input[type="submit"]').prop('disabled', false);
            $('#leadAltMobileError').html('');
          }
        }
      })
    }else{
      $(':input[type="submit"]').prop('disabled', false);
      $('#'+error_id).html('');
    }
  });
});
</script>

<script>
  function getDepartmentDetails(id){
    getUsers(id);
    getCourse(id);
    checkLeadEmail($('#email').val(), 'leadEmailError');
    $("#mobile").keyup()
    $("#alternate_mobile").keyup()
  }

  function getUsers(id){
    $.ajax({
      url:'/app/leads/university_users?id='+id,
      type:'GET',
      success: function(data){
        $('#user').html(data);
      }
    })
  }

  function getCourse(id){
    $.ajax({
      url:'/app/leads/courses?university_id='+id,
      type:'GET',
      success: function(data){
        $('#course').html(data);
        getSubCourse($('#course').val());
      }
    })
  }

  function getReasons(id){
    $.ajax({
      url:'/app/leads/stage_reasons?stage_id='+id,
      type:'GET',
      success: function(data){
        $('#reason').html(data);
      }
    })
  }
  
  function getSubSources(id){
    $.ajax({
      url:'/app/leads/source_sub_sources?source_id='+id,
      type:'GET',
      success: function(data){
        $('#sub_source').html(data);
      }
    })
  }

  function getSubCourse(id){
    $.ajax({
      url:'/app/leads/course_sub_courses?course_id='+id,
      type:'GET',
      success: function(data){
        $('#sub_course').html(data);
      }
    })
  }

  function getStates(id){
    $.ajax({
      url:'/app/leads/country_states?id='+id,
      type:'GET',
      success: function(data){
        $('#state').html(data);
      }
    })
  }
</script>

<script type="text/javascript">
  function checkLeadEmail(value, error_id){
    if(isEmail(value)){
      var university = $('#university_id').val();
      $.ajax({
        url: '/app/leads/check_lead_email?email='+value+'&university='+university,
        type: 'GET',
        dataType: 'JSON',
        success: function(data) {
          if(data.status==302){
            $('#'+error_id).html(data.message);
            $(':input[type="submit"]').prop('disabled', true);
          }else{
            $(':input[type="submit"]').prop('disabled', false);
            $('#'+error_id).html('');
          }
        }
      })
    }else{
      $(':input[type="submit"]').prop('disabled', false);
      $('#'+error_id).html('');
    }
  }
</script>
