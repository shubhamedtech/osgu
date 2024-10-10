<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
  ?>
    <option value="">Select</option>
  <?php
    $reasons = $conn->query("SELECT ID,Name FROM Reasons WHERE Status = 1 AND Stage_ID = $id");
    while ($reason = mysqli_fetch_array($reasons)){ ?>
      <option value="<?php echo $reason[0] ?>"><?php echo $reason[1] ?></option>
    <?php }
  }

