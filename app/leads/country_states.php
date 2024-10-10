<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';

    $id = intval($_GET['id']);
  ?>
    <option value="">Choose</option>
  <?php
    $states = $conn->query("SELECT ID, Name FROM States WHERE Country_ID = $id ORDER BY Name ASC");
    while ($state = mysqli_fetch_array($states)){ ?>
      <option value="<?php echo $state[0] ?>"><?php echo $state[1] ?></option>
    <?php }
  }

