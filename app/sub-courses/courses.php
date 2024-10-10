<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
  ?>
  <option value="">Choose</option>
  <?php
    $courses = $conn->query("SELECT Courses.ID, CONCAT(Short_Name, ' (', Course_Types.Name, ')') as Name FROM Courses LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID WHERE Courses.University_ID = $id ORDER BY Courses.Short_Name");
    while($course = $courses->fetch_assoc()){ 
  ?>
      <option value="<?php echo $course['ID'] ?>"><?php echo $course['Name'] ?></option>
  <?php
    }
  }
