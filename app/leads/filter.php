<?php session_start(); ?>
<style>
  .select2-selection--multiple{
    overflow: hidden !important;
    height: auto !important;
  }
</style>
<div class="modal-header">
  <h5 class="modal-title" id="myCenterModalLabel">Filter</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="filter_form" action="ajax_admin/ajax_leads/store_filter">
  <div class="modal-body">
    <div class="row filter-options pb-2" id="filter_options_1">
      <div class="col-md-2 pb-1">
        <select class="form-control custom-select" name="filter_for[1]" id="filter_for_1" onchange="getFilterValue(1); modifyFilterOperator(this.value, 1);">
          <option value="">Select</option>
          <option value="Departments"><?=$_SESSION['Departments']?></option>
          <option value="Categories"><?=$_SESSION['Categories']?></option>
          <option value="Sub_Categories"><?=$_SESSION['Sub-Categories']?></option>
          <option value="Stages">Stage</option>
          <option value="Reasons">Reason</option>
          <option value="Sources">Source</option>
          <option value="Sub_Sources">Sub-Source</option>
          <option value="Users">User</option>
          <option value="Created_On">Created On</option>
          <option value="Updated_On">Updated On</option>
        </select>
      </div>
      <div class="col-md-2 pb-1">
        <select class="form-control custom-select" name="filter_operator[1]" id="filter_operator_1" onchange="getFilterValue(1)">
          <option value="">Select</option>
          <optgroup label="Single Keyword">
            <option value="equal_to">Equal to</option>
            <option value="not_equal_to">Not Equal to</option>
            <option value="less_than">Less than</option>
            <option value="greater_than">Greater than</option>
            <option value="less_or_equal_to">Less or Equal to</option>
            <option value="greater_or_equal_to">Greater or Equal to</option>
          </optgroup>
          <optgroup label="Multiple Keyword" id="multi_group_1">
            <option value="in">In</option>
            <option value="not_in">Not In</option>
          </optgroup>
          <optgroup label="Search your own keywords" id="text_group_1">
            <option value="has_prefix">Has Prefix</option>
            <option value="has_suffix">Has Suffix</option>
            <option value="contains">Contains</option>
            <option value="not_contains">Not Contains</option>
          </optgroup>
        </select>
      </div>
      <div class="col-md-7 pb-1" id="filter_value_1">
        <input type="text" class="form-control" placeholder="Filter Values" autocomplete="off">
      </div>
      <div class="col-md-1 text-center pt-1">
        <i class="uil uil-minus-square-full cursor-pointer" onclick="removeFilter(1)" style="margin-right:10px; font-size:18px;"></i><i class="uil uil-plus-square cursor-pointer" style="font-size:18px;" onclick="addMoreFilter()"></i>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" onclick="resetFilter()">Reset</button>
    <button type="submit" class="btn btn-primary">Apply</button>
  </div>
</form>

<script type="text/javascript">
  function modifyFilterOperator(value, id){
    var date_fields = ['Created_On', 'Updated_On'];
    if(date_fields.includes(value)){
      $('#filter_operator_'+id+' optgroup#multi_group_'+id).prop('disabled', true);
      $('#filter_operator_'+id+' optgroup#text_group_'+id).prop('disabled', true);
    }else{
      $('#filter_operator_'+id+' optgroup#multi_group_'+id).prop('disabled', false);
      $('#filter_operator_'+id+' optgroup#text_group_'+id).prop('disabled', false);
    }
  }

  function getFilterValue(id){
    var multiple = ['in', 'not_in'];
    var date_fields = ['Created_On', 'Updated_On'];
    var filter_for = $('#filter_for_'+id).val();
    var operator = $('#filter_operator_'+id).val();
    if(filter_for.length > 0 && operator.length > 0){
      $.ajax({
        url: 'ajax_admin/ajax_filter/value?id='+id+'&data='+filter_for+'&operator='+operator,
        type: 'GET',
        success: function(data) {
          $('#filter_value_'+id).html(data);
          if(multiple.includes(operator)){
            $('#multi_select_'+id).select2({
              dropdownParent: $('#xlmodal')
            });
            if (localStorage.getItem("lead_filter") != null) {
              var filters = JSON.parse(localStorage.getItem("lead_filter"));
              $.each(filters.filter_for, function (i) {
                if(multiple.includes(operator)){
                  if(i==1){
                    $('#multi_select_1').select2({
                      dropdownParent: $('#xlmodal')
                    }).val(filters.filter_value[i]).trigger("change"); 
                  }else{
                    $('#multi_select_'+i).select2({
                      dropdownParent: $('#xlmodal')
                    }).val(filters.filter_value[i]).trigger("change");
                  }
                }
              });
            }
          }
          if(date_fields.includes(filter_for)){
            $('#datepicker_'+id).flatpickr({
              dateFormat: "d-m-Y",
            });
            if (localStorage.getItem("lead_filter") != null) {
              var filters = JSON.parse(localStorage.getItem("lead_filter"));
              $.each(filters.filter_for, function (i) {
                if(date_fields.includes(filter_for)){
                  if(i==1){
                    filters.filter_value[i].forEach(function(element){
                      $('#datepicker_1').flatpickr({
                        dateFormat: "d-m-Y",
                        defaultDate: element
                      });
                    });
                  }else{
                    filters.filter_value[i].forEach(function(element){
                      $('#datepicker_'+i).flatpickr({
                        dateFormat: "d-m-Y",
                        defaultDate: element
                      });
                    });
                  }
                }
              });
            }
          }
          if (localStorage.getItem("lead_filter") != null) {
            var filters = JSON.parse(localStorage.getItem("lead_filter"));
            $.each(filters.filter_for, function (i) {
              if(!multiple.includes(operator) && !date_fields.includes(filter_for)){
                if(i==1){
                  filters.filter_value[i].forEach(function(element){
                    $('#filter_values_1').val(element);
                  });
                }else{
                  filters.filter_value[i].forEach(function(element){
                    $('#filter_values_'+i).val(element);
                  });
                }
              }
            });
          }
        }
      })
    }
  }
</script>

<script type="text/javascript">
  function addMoreFilter(){
    var filter_id = $('.filter-options').length+1;
    $('.modal-body').append('<div class="row filter-options pb-2" id="filter_options_'+filter_id+'">\
    <div class="col-md-2 pb-1">\
      <select class="form-control custom-select" name="filter_for['+filter_id+']" id="filter_for_'+filter_id+'" onchange="getFilterValue('+filter_id+'); modifyFilterOperator(this.value, '+filter_id+');">\
        <option value="">Select</option>\
        <option value="Departments"><?=$_SESSION['Departments']?></option>\
        <option value="Categories"><?=$_SESSION['Categories']?></option>\
        <option value="Sub_Categories"><?=$_SESSION['Sub-Categories']?></option>\
        <option value="Stages">Stage</option>\
        <option value="Reasons">Reason</option>\
        <option value="Sources">Source</option>\
        <option value="Sub_Sources">Sub-Source</option>\
        <option value="Users">User</option>\
        <option value="Created_On">Created On</option>\
        <option value="Updated_On">Updated On</option>\
      </select>\
    </div>\
    <div class="col-md-2 pb-1">\
      <select class="form-control custom-select" name="filter_operator['+filter_id+']" id="filter_operator_'+filter_id+'" onchange="getFilterValue('+filter_id+')">\
        <option value="">Select</option>\
        <optgroup label="Single Keyword">\
          <option value="equal_to">Equal to</option>\
          <option value="not_equal_to">Not Equal to</option>\
          <option value="less_than">Less than</option>\
          <option value="greater_than">Greater than</option>\
          <option value="less_or_equal_to">Less or Equal to</option>\
          <option value="greater_or_equal_to">Greater or Equal to</option>\
        </optgroup>\
        <optgroup label="Multiple Keyword" id="multi_group_'+filter_id+'">\
          <option value="in">In</option>\
          <option value="not_in">Not In</option>\
        </optgroup>\
        <optgroup label="Search your own keywords" id="text_group_'+filter_id+'">\
          <option value="has_prefix">Has Prefix</option>\
          <option value="has_suffix">Has Suffix</option>\
          <option value="contains">Contains</option>\
          <option value="not_contains">Not Contains</option>\
        </optgroup>\
      </select>\
    </div>\
    <div class="col-md-7 pb-1" id="filter_value_'+filter_id+'">\
      <input type="text" class="form-control" placeholder="Filter Values" autocomplete="off">\
    </div>\
    <div class="col-md-1 text-center pt-1">\
      <i class="uil uil-minus-square-full cursor-pointer" onclick="removeFilter('+filter_id+')" style="margin-right:10px; font-size:18px;"></i><i class="uil uil-plus-square cursor-pointer" style="font-size:18px;" onclick="addMoreFilter()"></i>\
    </div>\
  </div>');
  }

  function removeFilter(id){
    $('#filter_options_'+id).remove();
  }
</script>

<script>
  $(function(){
    $("#filter_form").on("submit", function(e){
      e.preventDefault();
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
            $('.filter-button').removeClass('btn-primary');
            $('.filter-button').addClass('btn-success');
            localStorage.setItem("lead_filter", JSON.stringify(data.filter_data));
            $('#leads-table').DataTable().ajax.reload(null, false);
          }else{
            toastr.error(data.message);
          }
        }
      });
    });
  });
</script>

<script type="text/javascript">
  $(function() {
    if (localStorage.getItem("lead_filter") != null) {
      var filters = JSON.parse(localStorage.getItem("lead_filter"));
      $.each(filters.filter_for, function (i) {
        if(i==1){
          $('#filter_for_1').val(filters.filter_for[i]);
          $('#filter_operator_1').val(filters.filter_operator[i]);
          getFilterValue(i);
        }else{
          addMoreFilter();
          $('#filter_for_'+i).val(filters.filter_for[i]);
          $('#filter_operator_'+i).val(filters.filter_operator[i]);
          getFilterValue(i);
        }
      });
    }
  });
</script>

<script type="text/javascript">
  function resetFilter(){
    localStorage.removeItem("lead_filter");
    <?php unset($_SESSION["lead_filter_query"]); ?>
    $('#leads-table').DataTable().ajax.reload(null, false);
    $('.filter-button').removeClass('btn-success');
    $('.filter-button').addClass('btn-primary');
    openFilter();
  }
</script>
