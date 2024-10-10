<?php 
if(isset($_POST['id'])){
  include '../../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $rule = $conn->query("SELECT Name, Description, Category FROM Assignment_Rules WHERE ID = $id");
  $rule = $rule->fetch_assoc();
?>
  <style>
    .select2-selection--multiple{
      overflow: hidden !important;
      height: auto !important;
    }
  </style>
  <div class="modal-header">
    <h5 class="modal-title" id="myCenterModalLabel">Add Lead Assignment Rule</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <form id="lead_assignment_form" method="POST" action="ajax_admin/ajax_lead_assignment/store">
    <div class="modal-body">
      <div class="form-group row mb-2">
        <div class="col-lg-6">
          <input type="text" class="form-control" autocomplete="off" id="rule_name" value="<?=$rule['Name']?>" name="rule_name" placeholder="Name" required />
        </div>
        <div class="col-md-6">
          <textarea class="form-control" name="description" id="description" rows="1" placeholder="Description"><?=$rule['Description']?></textarea>
        </div>
      </div>
      <div class="form-group row mb-2">
        <div class="col-md-12">
          <select class="form-control custom-select" id="category" name="category" onchange="getDepartments(this.value)" required>
            <?php 
              $categories = $conn->query("SELECT Name FROM Categories WHERE Name LIKE '".$rule['Category']."' GROUP BY Name ORDER BY Name");
              while ($category = $categories->fetch_assoc()){ ?>
                <option value="<?php echo $category['Name']; ?>"><?php echo $category['Name']; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="form-group row mt-3 mb-2" id="departments_edit">
        
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Add</button>
    </div>
  </form>

  <script>
    $(function(){
      $("#lead_assignment_form").on("submit", function(e){
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
                  toastr.success(data.message);
                  $('#lead-assignment-table').DataTable().ajax.reload(null, false);;
                }else{
                  toastr.error(data.message);
                }
              }
          });
          e.preventDefault();
      });
    });

    function getDepartments(category){
      $.ajax({
        url:'ajax_admin/ajax_lead_assignment/departments-edit?category='+category,
        type:'get',
        success: function(data) {
          $('#departments_edit').html(data);
        }
      })
    }

    getDepartments('<?=$rule['Category']?>');
  </script>
<?php } ?>
