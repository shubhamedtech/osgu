<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
  ?>
  <option value="">Choose</option>
  <?php
    $types = $conn->query("SELECT ID, Name FROM Departments WHERE University_ID = $id ORDER BY Name");
    while($type = $types->fetch_assoc()){ 
  ?>
      <option value="<?php echo $type['ID'] ?>"><?php echo $type['Name'] ?></option>
  <?php
    }
  }
