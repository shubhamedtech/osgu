<?php
if (isset($_POST['ids'])) {
  include '../../includes/db-config.php';
  $ids = $_POST['ids'];

  $students = $conn->query("SELECT TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)) as Name, Unique_ID, RIGHT(CONCAT('000000', Students.ID), 6) as Student_ID FROM Students WHERE ID IN (" . $ids . ")");
?>
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5><span class="semi-bold"></span>Students</h5>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Student ID</th>
              <th>Name</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($students->num_rows == 0) { ?>
              <tr>
                <td colspan="3">
                  <center>No record!</center>
                </td>
              </tr>
              <?php } else {
              $counter = 1;
              while ($student = $students->fetch_assoc()) { ?>
                <tr>
                  <td><?= $counter++ ?></td>
                  <td><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['Student_ID'] ?></td>
                  <td><?= $student['Name'] ?></td>
                </tr>
            <?php }
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php
}
