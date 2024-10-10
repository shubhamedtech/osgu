<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $whatsapps = $conn->query("SELECT ID, Name FROM Templates_WhatsApp WHERE Department_ID = $id");
  ?>
    <option value="">Select</option>
  <?php
    while($whatsapp = $whatsapps->fetch_assoc()){ ?>
    <option value="<?=$whatsapp['ID']?>"><?=$whatsapp['Name']?></option>
<?php }
  }
