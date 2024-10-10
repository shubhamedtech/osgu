<?php 
  if(isset($_GET['category'])){
    include '../../../includes/db-config.php';
    session_start();

    $category = mysqli_real_escape_string($conn, $_GET['category']);

    $data = array();
    $rules = $conn->query("SELECT * FROM Assignment_Rules WHERE Category = '$category'");
    while($rule = $rules->fetch_assoc()){
      $data['Departments'][] = $rule['Department_ID'];
      $data['Source'][$rule['Department_ID']] = $rule['Source'];
      $data['Sub_Source'][$rule['Department_ID']] = $rule['Sub_Source'];
      $data['Country'][$rule['Department_ID']] = $rule['Country'];
      $data['State'][$rule['Department_ID']] = $rule['State'];
      $data['City'][$rule['Department_ID']] = $rule['City'];
      $data['Role'][$rule['Department_ID']] = $rule['Role'];
      $data['User'][$rule['Department_ID']] = $rule['User'];
      $data['Day'][$rule['Department_ID']] = $rule['Day'];
      $data['Hour'][$rule['Department_ID']] = $rule['Hour'];
    }

    $departments = $conn->query("SELECT Departments.ID, Departments.Name FROM Categories LEFT JOIN Departments ON Categories.Department_ID = Departments.ID WHERE Categories.Name = '$category' GROUP BY Departments.Name");
    while($department = $departments->fetch_assoc()){ ?>
      <div class="row">
        <div class="col-md-12 mb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" name="departments[]" id="department_<?=$department['ID']?>" value="<?=$department['ID']?>">
            <label class="form-check-label" for="department_<?=$department['ID']?>"><?=$department['Name']?></label>
          </div>
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="source_<?=$department['ID']?>" name="sources[<?=$department['ID']?>][]" onchange="getSubSources('<?=$department['ID']?>')" >
            <option value="All">All</option>
            <?php
              $sources = $conn->query("SELECT ID, Name FROM Sources WHERE Status = 1");
              while($source = $sources->fetch_assoc()){ ?>
                <option value="<?=$source['ID']?>"><?=$source['Name']?></option>
            <?php }
            ?>
          </select>
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="sub_source_<?=$department['ID']?>" name="sub_sources[<?=$department['ID']?>][]" >
            <option value="All">All</option>
          </select>
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="country_<?=$department['ID']?>" name="countries[<?=$department['ID']?>][]" onchange="getStates('<?=$department['ID']?>')" >
            <option value="All">All</option>
            <?php 
              $countries = $conn->query("SELECT ID, Name FROM Countries ORDER BY Name ASC");
              while($country = $countries->fetch_assoc()){ ?>
                <option value="<?=$country['ID']?>"><?=$country['Name']?></option>
            <?php }
            ?>
          </select>
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="state_<?=$department['ID']?>" name="states[<?=$department['ID']?>][]" onchange="getCities('<?=$department['ID']?>')" >
            <option value="All">All</option>
          </select>
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="city_<?=$department['ID']?>" name="city[<?=$department['ID']?>][]" >
            <option value="All">All</option>
          </select>
        </div>
        <div class="col-md-4 pb-2">
          <input type="number" min="0" class="form-control" autocomplete="off" id="show_after_days_<?=$department['ID']?>" name="show_after_days[<?=$department['ID']?>]" placeholder="then Lead will show after _ day(s)"  />
        </div>
        <div class="col-md-4 pb-2">
          <input type="number" min="0" class="form-control" autocomplete="off" id="show_after_time_<?=$department['ID']?>" name="show_after_time[<?=$department['ID']?>]" placeholder="and _ hour(s)"  />
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="user_role_<?=$department['ID']?>" name="user_roles[<?=$department['ID']?>][]" onchange="getUsers('<?=$department['ID']?>');" >
            <option value="All">All</option>
            <option value="Administrator">Admin</option>
            <option value="Manager">Manager</option>
            <option value="Asst. Manager">Asst. Manager</option>
            <option value="Team Leader">Team Lead</option>
            <option value="Counsellor">Counsellor</option>
          </select>
        </div>
        <div class="col-md-4 pb-2">
          <select class="form-select small" multiple id="user_<?=$department['ID']?>" name="users[<?=$department['ID']?>][]" >
            <option value="All">All</option>
          </select>
        </div>
      </div>

      <script type="text/javascript">

        
        $(function() {
          var sub_source_data = [];
          
          sub_source_data[<?=$department['ID']?>] = <?php echo $data['Sub_Source'][$department['ID']] ?>;
          
          $('#department_<?=$department['ID']?>').prop('checked', true);
          $('#show_after_days_<?=$department['ID']?>').val('<?=$data['Day'][$department['ID']]?>');
          $('#show_after_time_<?=$department['ID']?>').val('<?=$data['Hour'][$department['ID']]?>');
          
          $('#source_<?=$department['ID']?>').select2({
            placeholder: "If Source(s)",
            dropdownParent: $('#xlmodal')
          }).val(<?php echo $data['Source'][$department['ID']] ?>).trigger('change');
          // $('#sub_source_<?=$department['ID']?>').select2({
          //   placeholder: "And Sub-Source(s)",
          //   dropdownParent: $('#xlmodal')
          // });
          $('#country_<?=$department['ID']?>').select2({
            placeholder: "And Countries",
            dropdownParent: $('#xlmodal')
          }).val(<?php echo $data['Country'][$department['ID']] ?>).trigger('change');
          $('#state_<?=$department['ID']?>').select2({
            placeholder: "And State(s)",
            dropdownParent: $('#xlmodal')
          }).val(<?php echo $data['State'][$department['ID']] ?>).trigger('change');
          $('#city_<?=$department['ID']?>').select2({
            placeholder: "And City(s)",
            dropdownParent: $('#xlmodal')
          }).val(<?php echo $data['City'][$department['ID']] ?>).trigger('change');
          $('#user_role_<?=$department['ID']?>').select2({
            placeholder: "and assign between Role(s)",
            dropdownParent: $('#xlmodal')
          }).val(<?php echo $data['Role'][$department['ID']] ?>).trigger('change');
          $('#user_<?=$department['ID']?>').select2({
            placeholder: "User(s)",
            dropdownParent: $('#xlmodal')
          }).val(<?php echo $data['User'][$department['ID']] ?>).trigger('change');
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
          url: 'ajax_admin/ajax_select/source_subsources?id='+element,
          type: 'GET',
          success: function(data){
            $('#sub_source_'+id).append(data);
            $('#sub_source_'+id).select2({
              placeholder: "And Sub-Source(s)",
              dropdownParent: $('#xlmodal')
            }).val(sub_source_data[id]);
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
          url: 'ajax_admin/ajax_select/country_states?id='+element,
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
          url: 'ajax_admin/ajax_select/state_cities?id='+element,
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
          url: 'ajax_admin/ajax_select/role_users?role='+element,
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
