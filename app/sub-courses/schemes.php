<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
  ?>
  <option value="">Choose</option>
  <?php
    $schemes = $conn->query("SELECT ID, Name FROM Schemes WHERE University_ID = $id ORDER BY Name");
    while($scheme = $schemes->fetch_assoc()){ 
  ?>
      <option value="<?php echo $scheme['ID'] ?>"><?php echo $scheme['Name'] ?></option>
  <?php
    }
  }
