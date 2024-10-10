<?php
  if(isset($_GET['pincode'])){
    require '../../includes/db-config.php';

    $pincode = intval($_GET['pincode']);
    $state = $conn->query("SELECT State FROM Regions WHERE Pincode = $pincode");
    $state = mysqli_fetch_assoc($state);
    echo $state['State'];
  }
