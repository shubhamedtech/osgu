<?php
  if(isset($_GET['pincode'])){
    require '../../includes/db-config.php';

    $pincode = intval($_GET['pincode']);
    $districts = $conn->query("SELECT District FROM Regions WHERE Pincode = $pincode GROUP BY District ORDER BY ID DESC");
    while($district = $districts->fetch_assoc()){ ?>
      <option value="<?=strtoupper(strtolower($district['District']))?>"><?=$district['District']?></option>
  <?php }
  }
