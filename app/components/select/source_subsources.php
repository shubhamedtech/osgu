<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';
    $id = intval($_GET['id']);
  ?>
    <option value="">Select</option>
  <?php
    $subsources = $conn->query("SELECT ID,Name FROM Sub_Sources WHERE Status = 1 AND Source_ID = $id");
    while ($subsource = mysqli_fetch_array($subsources)){ ?>
      <option value="<?php echo $subsource[0] ?>"><?php echo $subsource[1] ?></option>
    <?php }
  }
