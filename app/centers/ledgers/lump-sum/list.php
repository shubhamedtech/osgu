<?php
if (isset($_GET['id'])) {
  require '../../../../includes/db-config.php';
  require '../../../../includes/helpers.php';

  $id = $_GET['id'];
  $students = $conn->query("SELECT Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.ID, Students.Unique_ID, Invoices.Duration FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Invoices.ID IN ($id)");
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5><span class="semi-bold">Students</span></h5>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Duration</th>
                <th>Payable</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($student = $students->fetch_assoc()) {
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name']));
                $student_id = empty($student['Unique_ID']) ? $student['ID'] : $student['Unique_ID'];
              ?>
                <tr>
                  <td><?= implode(" ", $student_name) . " (" . $student_id . ")" ?></td>
                  <td><?= $student['Duration'] ?></td>
                  <td><?= "&#8377; " . (-1) * balanceAmount($conn, $student['ID'], $student['Duration']) ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
