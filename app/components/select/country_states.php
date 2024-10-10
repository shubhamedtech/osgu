<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
  ?>
    <option value="">Select</option>
  <?php
    $states = $conn->query("SELECT ID, Name FROM States WHERE Country_ID = $id");
    while ($state = mysqli_fetch_array($states)){ ?>
      <option value="<?php echo $state[0] ?>"><?php echo $state[1] ?></option>
    <?php }
  }

