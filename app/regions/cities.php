<?php
  if(isset($_GET['pincode'])){
    require '../../includes/db-config.php';

    $pincode = intval($_GET['pincode']);
    $cities = $conn->query("SELECT City FROM Regions WHERE Pincode = $pincode");
    while($city = $cities->fetch_assoc()){ ?>
      <option value="<?=strtoupper(strtolower($city['City']))?>"><?=$city['City']?></option>
  <?php }
  }
