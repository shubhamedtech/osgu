<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
  ?>
    <option value="">Select</option>
  <?php
    $categories = $conn->query("SELECT ID, Name FROM Categories WHERE Status = 1 AND Department_ID = $id");
    while ($category = mysqli_fetch_assoc($categories)){ ?>
      <option value="<?php echo $category['ID'] ?>"><?php echo $category['Name'] ?></option>
    <?php }
  }

