<?php if(isset($_GET['id'])){
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);
  $structure = $conn->query("SELECT * FROM Fee_Structures WHERE ID = $id");
  $structure = mysqli_fetch_assoc($structure);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Add <span class="semi-bold">Fee Structure</span></h6>
  </div>
  <form role="form" id="form-edit-fee-structures" action="/app/components/fee-structures/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="ex: Course Fee" value="<?=$structure['Name']?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Sharing?</label>
            <select class="full-width" style="border: transparent;" name="sharing">
              <option value="">Choose</option>
              <option value="1" <?php print $structure['Sharing']==1 ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?php print $structure['Sharing']==0 ? 'selected' : '' ?>>No</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Constant?</label>
            <select class="full-width" style="border: transparent;" name="constant">
              <option value="">Choose</option>
              <option value="1" <?php print $structure['Is_Constant']==1 ? 'selected' : '' ?>>Yes</option>
              <option value="0" <?php print $structure['Is_Constant']==0 ? 'selected' : '' ?>>No</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Applicable on</label>
            <select class="full-width" style="border: transparent;" name="applicable">
              <option value="">Choose</option>
              <?php
                $applicables = $conn->query("SELECT ID, Name FROM Fee_Applicables");
                while($applicable = $applicables->fetch_assoc()) { ?>
                  <option value="<?=$applicable['ID']?>" <?php print $structure['Fee_Applicable_ID']==$applicable['ID'] ? 'selected' : '' ?>><?=$applicable['Name']?></option>
              <?php } ?>
            </select>
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
      $('#form-edit-fee-structures').validate({
        rules: {
          name: {required:true},
          sharing: {required:true},
          constant: {required:true},
          applicable: {required:true},  
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

    $("#form-edit-fee-structures").on("submit", function(e){
      if($('#form-edit-fee-structures').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?=$id?>');
        formData.append('university_id', '<?=$structure['University_ID']?>');
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
              $('#tableFeeStructures').DataTable().ajax.reload(null, false);
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
<?php } ?>
