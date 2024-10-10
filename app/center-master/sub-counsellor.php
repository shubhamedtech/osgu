<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
  ?>
  <option value="">Select</option>
  <?php
    $sub_counsellors = $conn->query("SELECT Users.ID, CONCAT(Users.`Name`, ' (', Users.Code, ')') as Name FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE University_User.Reporting = $id");
    while($sub_counsellor = $sub_counsellors->fetch_assoc()){ ?>
      <option value="<?php echo $sub_counsellor['ID'] ?>"><?=$sub_counsellor['Name']?></option>
  <?php }
  }
