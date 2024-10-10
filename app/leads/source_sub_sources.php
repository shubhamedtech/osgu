<?php
  if(isset($_GET['source_id'])){
    require '../../includes/db-config.php';

    $source_id = intval($_GET['source_id']);

    echo '<option value="">Choose</option>';
    $sources = $conn->query("SELECT ID, Name FROM Sub_Sources WHERE Status = 1 AND Source_ID = $source_id");
    while($source = $sources->fetch_assoc()){ 
      echo '<option value="'.$source['ID'].'">'.$source['Name'].'</option>';
    }
  }
