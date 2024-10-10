<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
  ?>
    <option value="">Select</option>
  <?php
    $cities = $conn->query("SELECT ID, Name FROM Cities WHERE State_ID = $id");
    while ($city = mysqli_fetch_array($cities)){ ?>
      <option value="<?php echo $city[0] ?>"><?php echo $city[1] ?></option>
    <?php }
  }
