<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
  ?>
  <?php
    $users = $conn->query("SELECT ID,Employee_ID,Name FROM Users WHERE Status = 1 AND ID IN (SELECT `User_ID` FROM Users_Departments WHERE Department_ID = $id)");
    while ($user = mysqli_fetch_array($users)){ ?>
      <option value="<?php echo $id."|".$user[0] ?>"><?php echo $user[2]." (".$user[1].")" ?></option>
    <?php }
  }

