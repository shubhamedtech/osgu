<?php
  if(isset($_GET['course_id']) && isset($_GET['semester'])){
    require '../../includes/db-config.php';
    session_start();

    $sub_course_id = intval($_GET['course_id']);
    $semester = explode("|", $_GET['semester']);
    $scheme = $semester[0];
    $semester = $semester[1];

    $syllabus = $conn->query("SELECT * FROM Syllabi WHERE Sub_Course_ID = $sub_course_id AND Scheme_ID = $scheme AND Semester = $semester AND Paper_Type = 'Theory'");
?>
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Credit</th>
            <th>Paper Type</th>
            <th>Min/Max Marks</th>
            <th>Assignment</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $syllabus->fetch_assoc()) { ?>
            <tr>
              <td><?=$row['Code']?></td>
              <td><?=$row['Name']?></td>
              <td><?=$row['Credit']?></td>
              <td><?=$row['Paper_Type']?></td>
              <td><?=$row['Min_Marks']?>/<?=$row['Max_Marks']?></td>
              <td>
                <?php if(!is_null($row['Assignment']) && !empty($row['Assignment'])){ 
                  $files = explode("|", $row['Assignment']);
                  foreach($files as $file){?>
                    <a href="<?=$file?>" target="_blank" download="<?=$row['Code']?>">Download</a>
                <?php }} ?>
                <?php if(in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])){ ?><span class="text-primary cursor-pointer" onclick="uploadFile('Syllabi', 'Assignment', <?=$row['ID']?>)">Upload</span><?php } ?>
              </td>
            </tr>
            <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
<?php
  }