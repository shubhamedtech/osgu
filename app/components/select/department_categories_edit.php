<?php
  if(isset($_GET['id']) && isset($_GET['selected'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $selected = mysqli_real_escape_string($conn, $_GET['selected']);
    
    $categories = $conn->query("SELECT ID,Name FROM Categories WHERE Status = 1 AND Department_ID = $id");
    while ($category = mysqli_fetch_array($categories)){ ?>
      <option value="<?php echo $category[0] ?>" <?php print $category[0]==$selected ? "selected" : ""; ?>><?php echo $category[1] ?></option>
    <?php }
  }

