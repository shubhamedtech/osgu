<?php if(isset($_GET['id'])){
  require '../../includes/db-config.php';
  $university = $conn->query("SELECT Name, Short_Name, Vertical, Address, Logo, Is_B2C FROM Universities WHERE ID = '".$_GET['id']."'");
  $university = mysqli_fetch_assoc($university);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Update <span class="semi-bold"></span>University</h5>
  </div>
  <form role="form" id="form-edit-university" action="/app/universities/update" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>University Dealing with</label>
            <select class="full-width" style="border: transparent;" name="university_type" id="university_type">
              <option value="0" <?php print $university['Is_B2C']==0 ? 'selected' : '' ?>>Outsourced Partners</option>
              <option value="1" <?php print $university['Is_B2C']==1 ? 'selected' : '' ?>>Inhouse i.e. Students</option>
              <option value="2" <?php print $university['Is_B2C']==2 ? 'selected' : '' ?>>Both</option>
            </select>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $university['Name'] ?>" placeholder="ex: XYZ University" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Short Name</label>
            <input type="text" name="short_name" class="form-control" value="<?php echo $university['Short_Name'] ?>" placeholder="ex: XYZU" required>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Vertical</label>
            <input type="text" name="vertical" class="form-control" value="<?php echo $university['Vertical'] ?>" placeholder="ex: Technical" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="2" placeholder="ex: 23 Street, California, USA 681971" required><?php echo $university['Address'] ?></textarea>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <label>Logo</label>
          <input type="file" name="logo" accept="image/png, image/jpg, image/jpeg, image/svg">
        </div>

        <div class="col-md-6" id="logo-view">
          <img src="<?php echo  $university['Logo'] ?>" width="60px">
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
      $('#form-edit-university').validate({
        rules: {
          name: {required:true},
          short_name: {required:true},
          vertical: {required:true},
          address: {required:true},
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

    $("#form-edit-university").on("submit", function(e){
      if($('#form-edit-university').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?=$_GET['id']?>');
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
                  $('#universities-table').DataTable().ajax.reload(null, false);
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
