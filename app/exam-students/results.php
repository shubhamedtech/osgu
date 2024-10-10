<?php
ini_set('display_errors', 1);
if (isset($_GET['course_id']) && isset($_GET['semester'])) {
  require '../../includes/db-config.php';
  session_start();

  $sub_course_id = intval($_GET['course_id']);
  $semester = explode("|", $_GET['semester']);
  $scheme = $semester[0];
  $semester = $semester[1];

  $syllabus_ids = array();
  $codes = $conn->query("SELECT ID FROM Syllabi WHERE Sub_Course_ID = " . $sub_course_id . " AND Semester = " . $semester . " AND Scheme_ID = " . $scheme . "");

  if ($codes->num_rows > 0) {
    while ($row = $codes->fetch_assoc()) {
      $syllabus_ids[] = $row['ID'];
    }

    $date_sheets = $conn->query("SELECT Date_Sheets.*, Exam_Sessions.Name as Exam_Session, Syllabi.Sub_Course_ID as Sub_Course_ID, Syllabi.Name, Syllabi.ID as Syllabi_id, Syllabi.Code FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID LEFT JOIN Exam_Sessions ON Date_Sheets.Exam_Session_ID = Exam_Sessions.ID WHERE Syllabus_ID IN (" . implode(",", $syllabus_ids) . ") ORDER BY Exam_Date ASC");
    if ($date_sheets->num_rows == 0) {
      echo '<center><h1>Date Sheet Not Available</h1></center>';
    } else {
?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Exam Session</th>
              <th>Paper Code</th>
              <th>Paper Name</th>
              <th>Date</th>
              <th>Time</th>
              <th>View Result</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($date_sheet = $date_sheets->fetch_assoc()) { ?>
              <tr>
                <td><?= $date_sheet['Exam_Session'] ?></td>
                <td><?= $date_sheet['Code'] ?></td>
                <td><?= $date_sheet['Name'] ?></td>
                <td><?= date("l, dS M, Y", strtotime($date_sheet['Exam_Date'])) ?></td>
                <td><?= date("h:i A", strtotime($date_sheet['Start_Time'])) . " to " . date("h:i A", strtotime($date_sheet['End_Time'])) ?></td>
                <td><i class="uil uil-eye cursor-pointer" onclick="getExamResults(<?= $date_sheet['Syllabi_id'] ?>)"></i></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <script>
        function getExamResults(id) {
            var exam_id = id ;
            window.location.replace('view-results?sub_course_id='+exam_id);
        }
      </script>
<?php }
  } else {
    // No Date Sheet Available
    echo '<center><h1>Result Not Available</h1></center>';
  }
}
?>
