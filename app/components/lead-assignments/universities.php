<?php 
  if(isset($_GET['course'])){
    include '../../../includes/db-config.php';
    session_start();

    $course = mysqli_real_escape_string($conn, $_GET['course']);

    $universities = $conn->query("SELECT Universities.ID, Universities.Name FROM Courses LEFT JOIN Universities ON Courses.University_ID = Universities.ID WHERE Courses.Name = '$course' GROUP BY Universities.Name");
    while($university = $universities->fetch_assoc()){ ?>
      <div class="row">
      <div class="col-md-12 mb-2"></div>
        <div class="col-md-12 mb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="departments[]" id="department_<?=$university['ID']?>" value="<?=$university['ID']?>">
            <label class="form-check-label" for="department_<?=$university['ID']?>"><?=$university['Name']?></label>
          </div>
        </div>
        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>Channel(s)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="source_<?=$university['ID']?>" name="sources[<?=$university['ID']?>][]" onchange="getSubSources('<?=$university['ID']?>')">
              <option value="All">All</option>
              <?php
                $sources = $conn->query("SELECT ID, Name FROM Sources WHERE Status = 1");
                while($source = $sources->fetch_assoc()){ ?>
                  <option value="<?=$source['ID']?>"><?=$source['Name']?></option>
              <?php }
              ?>  
            </select>
          </div>
        </div>
        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>Sub-Channel(s)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="sub_source_<?=$university['ID']?>" name="sub_sources[<?=$university['ID']?>][]">
              <option value="All">All</option>
              
            </select>
          </div>
        </div>
        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>Country(ies)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="country_<?=$university['ID']?>" name="countries[<?=$university['ID']?>][]" onchange="getStates('<?=$university['ID']?>')">
              <option value="All">All</option>
              <?php 
                $countries = $conn->query("SELECT ID, Name FROM Countries ORDER BY Name ASC");
                while($country = $countries->fetch_assoc()){ ?>
                  <option value="<?=$country['ID']?>"><?=$country['Name']?></option>
              <?php }
              ?>
            </select>
          </div>
        </div>
        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>State(s)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="state_<?=$university['ID']?>" name="states[<?=$university['ID']?>][]" onchange="getCities('<?=$university['ID']?>')">
              <option value="All">All</option>
              
            </select>
          </div>
        </div>

        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>City(ies)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="city_<?=$university['ID']?>" name="city[<?=$university['ID']?>][]">
              <option value="All">All</option>
              
            </select>
          </div>
        </div>
        
        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default required">
            <label>Day Count</label>
            <input type="number" min="0" autocomplete="off" id="show_after_days_<?=$university['ID']?>" name="show_after_days[<?=$university['ID']?>]" placeholder="then Lead will show after _ day(s)" class="form-control">
          </div>
        </div>

        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default required">
            <label>Hour(s)</label>
            <input type="number" min="0" autocomplete="off" id="show_after_time_<?=$university['ID']?>" name="show_after_time[<?=$university['ID']?>]" placeholder="and _ hour(s)" class="form-control">
          </div>
        </div>

        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>Role(s)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="user_role_<?=$university['ID']?>" name="user_roles[<?=$university['ID']?>][]" onchange="getUsers('<?=$university['ID']?>');">
              <option value="All">All</option>
              <option value="University Head">Universty Head</option>
              <option value="Counsellor">Counsellor/Manager</option>
              <option value="Sub-Counsellor">Sub-Counsellor/Asst. Manager</option>
              <option value="Center">Center/Team Lead</option>
              <option value="Sub-Center">Sub-Center/Counsellor</option>
            </select>
          </div>
        </div>

        <div class="col-md-4 pb-2">
          <div class="form-group form-group-default form-group-default-select2">
            <label>User(s)</label>
            <select class=" full-width" data-init-plugin="select2" multiple id="user_<?=$university['ID']?>" name="users[<?=$university['ID']?>][]">
              <option value="All">All</option>
              
            </select>
          </div>
        </div>

      </div>

      <script type="text/javascript">

        $(function() {
          $('#source_<?=$university['ID']?>').select2({
            placeholder: "If Source(s)",
          });
          $('#sub_source_<?=$university['ID']?>').select2({
            placeholder: "And Sub-Source(s)",
          });
          $('#country_<?=$university['ID']?>').select2({
            placeholder: "And Countries",
          });
          $('#state_<?=$university['ID']?>').select2({
            placeholder: "And State(s)",
          });
          $('#city_<?=$university['ID']?>').select2({
            placeholder: "And City(s)",
          });
          $('#user_role_<?=$university['ID']?>').select2({
            placeholder: "and assign between Role(s)",
          });
          $('#user_<?=$university['ID']?>').select2({
            placeholder: "User(s)",
          });
        });
      </script>
  <?php } ?>

  <script type="text/javascript">
    function getSubSources(id){
      $('#sub_source_'+id).html('');
      $('#sub_source_'+id).html('<option value="All">All</option>');
      var value = $('#source_'+id).val();
      value.forEach(function(element) {
        $.ajax({
          url: '/app/components/select/source_subsources?id='+element,
          type: 'GET',
          success: function(data){
            $('#sub_source_'+id).append(data);
          }
        })
      })
    }

    function getStates(id){
      $('#state_'+id).html('');
      $('#state_'+id).html('<option value="All">All</option>');
      var value = $('#country_'+id).val();
      value.forEach(function(element) {
        $.ajax({
          url: '/app/components/select/country_states?id='+element,
          type: 'GET',
          success: function(data){
            $('#state_'+id).append(data);
          }
        })
      })
    }

    function getCities(id){
      $('#city_'+id).html('');
      $('#city_'+id).html('<option value="All">All</option>');
      var state = $('#state_'+id).val();
      state.forEach(function(element) {
        $.ajax({
          url: '/app/components/select/state_cities?id='+element,
          type: 'GET',
          success: function(data){
            $('#city_'+id).append(data);
          }
        })
      })
    }

    function getUsers(id){
      $('#user_'+id).html('');
      $('#user_'+id).html('<option value="All">All</option>');
      var value = $('#user_role_'+id).val();
      value.forEach(function(element) {
        $.ajax({
          url: '/app/components/select/role_users?role='+element+'&university_id='+id,
          type: 'GET',
          success: function(data){
            $('#user_'+id).append(data);
          }
        })
      })
    }
  </script>

<?php }
?>
