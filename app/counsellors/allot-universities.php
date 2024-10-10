<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
    $id = intval($_GET['id']);
    $alloted = array();
    $reporting = array();
    $alloted_universities = $conn->query("SELECT University_ID, Reporting FROM University_User WHERE `User_ID` = $id");
    while($alloted_university = $alloted_universities->fetch_assoc()){
      $alloted[] = $alloted_university['University_ID'];
      $reporting[$alloted_university['University_ID']] = $alloted_university['Reporting'];
    }
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Allot <span class="semi-bold"></span>Universities</h5>
  </div>
  <form role="form" id="form-allot-universities" action="/app/counsellors/allot" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <?php 
        if($_SESSION['Role']=='Administrator'){
          $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities");
        }else{
          $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities LEFT JOIN University_User ON Universities.ID = University_User.University_ID WHERE `User_ID` = ".$_SESSION['ID']."");
        }
          while($university = $universities->fetch_assoc()){ ?>
            <div class="row">
              <div class="col-md-6">
                <div class="form-check complete mt-3">
                  <input type="checkbox" id="allot-university-<?=$university['ID']?>" <?php print in_array($university['ID'], $alloted) ? 'checked' : '' ?> name="allot[]" value="<?=$university['ID']?>">
                  <label for="allot-university-<?=$university['ID']?>" class="font-weight-bold">
                    <?=$university['Name']?>
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group form-group-default required">
                  <label>RM</label>
                  <select class="full-width" style="border: transparent;" name="reporting[<?=$university['ID']?>]">
                    <?php
                      $heads = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name, CONCAT(' - ', Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID LEFT JOIN Universities ON University_User.University_ID = Universities.ID WHERE Role = 'University Head' AND University_User.University_ID = ".$university['ID']."");
                      while($head = $heads->fetch_assoc()) { ?>
                        <option value="<?=$head['ID']?>" <?php print array_key_exists($university['ID'], $reporting) && $reporting[$university['ID']]==$head['ID'] ? ' selected' : '' ?>><?=$head['Name']?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
        <?php }
        ?>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Allot</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>

  <script>
    $(function(){
      $('#form-allot-universities').validate({
        rules: {
          allot: {required:true}
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

    $("#form-allot-universities").on("submit", function(e){
      if($('#form-allot-universities').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?=$id?>');
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
<?php } ?>
