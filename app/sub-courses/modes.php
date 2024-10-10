<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
  ?>
  <option value="">Choose</option>
  <?php
    $modes = $conn->query("SELECT ID, Name FROM Modes WHERE University_ID = $id ORDER BY Name");
    while($mode = $modes->fetch_assoc()){ 
  ?>
      <option value="<?php echo $mode['ID'] ?>"><?php echo $mode['Name'] ?></option>
  <?php
    }
  }
