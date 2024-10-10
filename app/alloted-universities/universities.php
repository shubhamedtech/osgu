<?php
    session_start();
    require '../../includes/db-config.php';
    $id = $_SESSION['Role'] == 'Administrator' ? 0 : $_SESSION['ID'];
    $alloted = array();
    if(!empty($id)){
      if($_SESSION['Role'] == 'Center'){
        $alloted_universities = $conn->query("SELECT University_ID FROM Alloted_Center_To_Counsellor WHERE `Code` = $id");
        while($alloted_university = $alloted_universities->fetch_assoc()){
          $alloted[] = $alloted_university['University_ID'];
        }
      }else{
        $alloted_universities = $conn->query("SELECT University_ID FROM University_User WHERE `User_ID` = $id");
        while($alloted_university = $alloted_universities->fetch_assoc()){
          $alloted[] = $alloted_university['University_ID'];
        }
      }
    }
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Select <span class="semi-bold"></span>University</h5>
  </div>
  <div class="modal-body">
    <div class="row">
      <?php
        $alloted_query = !empty($alloted) ? " WHERE ID IN (".implode(',', $alloted).")" : "";
        $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name, Logo, Has_Unique_Center, Is_B2C, Is_Vocational, Has_LMS FROM Universities $alloted_query");
        while($university = $universities->fetch_assoc()){ ?>
          <div class="col-md-6 cursor-pointer" onclick="setSessionUniversity('<?=$university['ID']?>', '<?=$university['Name']?>', '<?=$university['Logo']?>', '<?=$university['Has_Unique_Center']?>', '<?=$university['Is_B2C']?>', '<?=$university['Is_Vocational']?>', '<?=$university['Has_LMS']?>');">
            <center>
              <img src="<?=$university['Logo']?>" alt="logo" data-src="<?=$university['Logo']?>" data-src-retina="<?=$university['Logo']?>" style="max-width:100%;" height="70px">
              <p class="bold mt-2"><?=$university['Name']?></p>
            </center>
          </div>
      <?php }
      ?>
    </div>
  </div>

  <script type="text/javascript">
    function setSessionUniversity(id, name, logo, unique_center, is_b2c, is_vocational, has_lms){
      $.ajax({
        url: '/app/login/change-university',
        type: 'POST',
        data:{id, name, logo, unique_center, is_b2c, is_vocational, has_lms},
        dataType: 'json',
        success: function(data) {
          if(data.status==200){
            $('.modal').modal('hide');
            notification('success', data.message);
            window.location.reload(true);
          }
        }
      })
    }
  </script>
