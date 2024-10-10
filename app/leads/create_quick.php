<?php 
  require '../../includes/db-config.php';
  session_start(); 
?>
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold"></span>Lead</h5>
</div>
<form id="quick_lead_form" method="POST" action="/app/leads/store_quick">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>University</label>
          <select class="full-width" style="border: transparent;" name="university_id" id="quick_university_id" onchange="checkQuickDetail()">
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
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Full Name</label>
          <input type="text" name="name" autocomplete="off" class="form-control" placeholder="ex: Jhon Doe" required />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Email</label>
          <input type="email" class="form-control" autocomplete="off" name="email" id="quick_email" placeholder="jhon@example.com" onkeyup="checkEmail(this.value, 'quickEmailError')" />
        </div>
        <p class="text-danger error font-weight-bold" id="quickEmailError"></p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Mobile</label>
          <input type="tel" class="form-control col-md-12" autocomplete="off" name="mobile" maxlength="10" id="quick_mobile" onkeypress="return isNumberKey(event)" placeholder="99999XXXXX" onkeyup="checkMobile(this.value, 'quickMobileError')" required />
        </div>
        <p class="text-danger error font-weight-bold" id="quickMobileError"></p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Channel</label>
          <select class="full-width" style="border: transparent;" name="source" id="source">
            <option value="">Choose</option>
            <?php $sources = $conn->query("SELECT ID, Name FROM Sources WHERE Status = 1"); 
              while($source = $sources->fetch_assoc()){?>
                <option value="<?=$source['ID']?>"><?=$source['Name']?></option>
            <?php } ?>
          </select>
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
    // const phoneInputField = document.querySelector("#quick_mobile");
    // const phoneInput = window.intlTelInput(phoneInputField, {
    //   initialCountry: "in",
    //   utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    // });

    // $('#quick_mobile').on("keyup", function(){
    //   value = phoneInput.getNumber();
    //   if(value.includes('+91')){
    //     $("#quick_mobile").attr('maxlength','10');
    //   }else{
    //     $("#quick_mobile").attr('maxlength','13');
    //   }
    // })

    $("#quick_lead_form").on("submit", function(e){
        var formData = new FormData(this);
        // formData.set('mobile', phoneInput.getNumber());
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
                $('#leads-table').DataTable().ajax.reload(null, false);;
              }else{
                notification('error', data.message);
              }
            }
        });
        e.preventDefault();
    });
});

function checkQuickDetail(){
  checkEmail($('#quick_email').val(), 'quickEmailError');
  checkMobile($('#quick_mobile').val(), 'quickMobileError');
}
</script>
