<?php
  if(isset($_GET['id'])){
    include '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
  ?>
    <option value="">Select</option>
  <?php
    $subcategories = $conn->query("SELECT ID,Name FROM Sub_Categories WHERE Status = 1 AND Category_ID = $id");
    while ($subcategory = mysqli_fetch_array($subcategories)){ ?>
      <option value="<?php echo $subcategory[0] ?>"><?php echo $subcategory[1] ?></option>
    <?php }
  }

