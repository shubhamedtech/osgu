<?php 
  require '../../includes/db-config.php';
  session_start();
  $id = str_replace("W1Ebt1IhGN3ZOLplom9I", "", base64_decode($_POST['id']));
  $university_id = intval($_POST['university_id']);
  $get_lead_details = $conn->query("SELECT Leads.*, Lead_Status.* FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.ID = $id");
  if($get_lead_details->num_rows>0){
    $lead = mysqli_fetch_assoc($get_lead_details);
  }
?>
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Edit <span class="semi-bold"></span>Lead</h5>
</div>
<form id="edit_lead_form" method="POST" action="/app/leads/update">
  <div class="modal-body">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Full Name</label>
          <input type="text" class="form-control" autocomplete="off" name="name" placeholder="Jhon Doe" value="<?=$lead['Name']?>" required />
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Email</label>
          <input type="email" class="form-control" autocomplete="off" id="email" name="email" placeholder="jhon@example.com" value="<?=$lead['Email']?>" />
        </div>
        <p class="text-danger font-weight-bold" id="leadEmailError"></p>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Alternate Email</label>
          <input type="email" class="form-control" autocomplete="off" id="alternate_email" name="alternate_email" placeholder="jhon@example.com" value="<?=$lead['Alternate_Email']?>" />
        </div>
        <p class="text-danger font-weight-bold" id="leadAltEmailError"></p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Mobile</label>
          <input type="tel" class="form-control" autocomplete="off" maxlength="10" id="mobile" name="mobile" value="<?=$lead['Mobile']?>" onkeypress="return isNumberKey(event)" placeholder="789654XXXX" required disabled/>
        </div>
        <p class="text-danger font-weight-bold" id="leadMobileError"></p>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Alternate Mobile</label>
          <input type="tel" class="form-control" autocomplete="off" id="alternate_mobile" maxlength="10" name="alternate_mobile" value="<?=$lead['Alternate_Mobile']?>" onkeypress="return isNumberKey(event)" placeholder="789654XXXX" />
        </div>
        <p class="text-danger font-weight-bold" id="leadAltMobileError"></p>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Course</label>
          <select class="full-width" style="border: transparent;" name="course" id="course" onchange="getSubCourses(this.value)" required>
            <option value="">Choose</option>
            
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Sub-Course</label>
          <select class="full-width" style="border: transparent;" name="sub_course" id="sub_course">
            <option value="">Choose</option>
            
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default">
          <label>Country</label>
          <select class="full-width" style="border: transparent;" name="country" id="country" onchange="getStates(this.value)">
            <option value="">Choose</option>
            <?php $countries = $conn->query("SELECT ID, Name FROM Countries");
              while ($country = $countries->fetch_assoc()){ ?>
                <option value="<?php echo $country['ID']; ?>" <?php print $country['ID']==$lead['Country_ID'] ? 'selected':'' ?>><?php echo $country['Name']; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default">
          <label>State</label>
          <select class="full-width" style="border: transparent;" name="state" id="state">
            <option value="">Choose</option>
            
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default">
          <label>Extra Information</label>
          <textarea class="form-control" rows="3" placeholder="Birthday, Remark, etc" name="extra_info" id="extra_info"><?=$lead['Extra']?></textarea>
        </div>
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
  // Form
  $("#edit_lead_form").on("submit", function(e){
    var formData = new FormData(this);
    formData.append('id', '<?=$id?>');
    formData.append('user_id', '<?=$lead['User_ID']?>');
    formData.append('university_id', '<?=$lead['University_ID']?>');
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
            if($('#lead_details_page').length>0){
              window.location.reload(true);
            }
            $('#leads-table').DataTable().ajax.reload(null, false);
          }else{
            notification('danger', data.message);
          }
        }
    });
    e.preventDefault();
  });
})
</script>

<script>

  function getCourse(id){
    $.ajax({
      url:'/app/leads/university_course?university_id='+id,
      type:'GET',
      success: function(data){
        $('#course').html(data);
        $('select[name^="course"] option[value="<?=$lead['Course_ID']?>"]').attr("selected","selected").change();
      }
    })
  }
  getCourse('<?=$lead['University_ID']?>');
  
  function getSubCourses(id){
    var university_id = '<?=$lead['University_ID']?>';
    $.ajax({
      url:'/app/leads/course_sub_courses?course_id='+id+'&university_id='+university_id,
      type:'GET',
      success: function(data){
        $('#sub_course').html(data);
        $('select[name^="sub_course"] option[value="<?=$lead['Sub_Course_ID']?>"]').attr("selected","selected");
      }
    })
  }

  function getStates(id){
    $.ajax({
      url:'/app/leads/country_states?id='+id,
      type:'GET',
      success: function(data){
        $('#state').html(data);
        <?php if($lead['State_ID']!=0){ ?>
          $('#state').val('<?=$lead['State_ID']?>');
        <?php } ?>
      }
    })
  }
  <?php if($lead['Country_ID']!=0){ ?>
    getStates('<?=$lead['Country_ID']?>');
  <?php } ?>
</script>
