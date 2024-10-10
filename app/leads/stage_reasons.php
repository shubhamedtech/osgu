<?php
  if(isset($_GET['stage_id'])){
    require '../../includes/db-config.php';

    $stage_id = intval($_GET['stage_id']);

    echo '<option value="">Choose</option>';
    $reasons = $conn->query("SELECT ID, Name FROM Reasons WHERE Status = 1 AND Stage_ID = $stage_id");
    while($reason = $reasons->fetch_assoc()){ 
      echo '<option value="'.$reason['ID'].'">'.$reason['Name'].'</option>';
    }
  }
