<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<?php date_default_timezone_set("Asia/Kolkata"); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                ini_set('display_errors', 1); 

              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head'])) { ?>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="upload('datesheets', 'md')"> <i class="uil uil-export"></i></button>
                <?php } ?>
              </div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->

        <div class="d-flex justify-content-center">
          <?php
          if (isset($_GET['name'])) { ?>
            <p><?= $_GET['name'] ?> Results</p>
          <?php  } ?>
        </div>
        <div class="row">
          <table class="table table-striped">
            <thead class="text-center">
              <tr>
                <th>Subjects</th>
                <th>Total Question</th>
                <th>Attend Question</th>
                <th>Obtained mark</th>
                <th>Total mark</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody class="text-center">
              <?php
              if (isset($_GET['id'])) {
                $attempt_question = 0;
                $correct_question = 0;
                $total_mark = 0;
                $status =  "A";
                $exam_submits = $conn->query("SELECT Exam_Students_Final_Submit.* , Syllabi.Name as subject_name FROM Exam_Students_Final_Submit LEFT JOIN Syllabi ON Exam_Students_Final_Submit.Syllabus_ID = Syllabi.ID WHERE Student_ID = '" . $_GET['id'] . "' ");
                if($exam_submits->num_rows > 0){
                while ($exam_submit = $exam_submits->fetch_assoc()) {
                  $student_answers = $conn->query("SELECT * FROM Exam_Students_Answers WHERE Student_ID = '" . $exam_submit['Student_ID'] . "'AND Syllabus_ID = '" . $exam_submit['Syllabus_ID'] . "' AND Date_Sheet_ID = '" . $exam_submit['Date_Sheet_ID'] . "' ");
                  $attempt_question = 0;
                  $correct_question = 0;
                  $total_mark = 0;
                  while ($student_answer = $student_answers->fetch_assoc()) {
                    if (!empty($student_answer['Answer'])) {
                      $attempt_question = $attempt_question + 1;
                    }

                    $questions = $conn->query("SELECT * FROM MCQs WHERE Date_Sheet_ID = '" . $student_answer['Date_Sheet_ID'] . "' AND 	Syllabus_ID = '" . $student_answer['Syllabus_ID'] . "' AND 	ID = '" . $student_answer['Question_ID'] . "' ");
                    while ($question = $questions->fetch_assoc()) {
                      if ($question['Answer'] == $student_answer['Answer']) {
                        $correct_question = $correct_question + 1;
                      }
                    }

                    $total_number = $conn->query("SELECT sum(Marks) as marks FROM MCQs WHERE Date_Sheet_ID = '" . $student_answer['Date_Sheet_ID'] . "' AND 	Syllabus_ID = '" . $student_answer['Syllabus_ID'] . "' ");
                    $total_mark = $total_number->fetch_assoc()['marks'];
                  }

                  if($correct_question > 0){
                    if ((($correct_question / $total_mark) * 100) > 33) {
                      $status = "P";
                    } else {
                      $status = "F";
                    }
                  }else{
                    $status = "A";
                  }
              ?>
                  <tr>
                    <td><?= $exam_submit['subject_name'] ?></td>
                    <td><?= $student_answers->num_rows ?></td>
                    <td><?= $attempt_question ?></td>
                    <td><?= $correct_question ?></td>
                    <td><?= $total_mark ?></td>
                    <td><?= $status ?></td>
                    <td></td>
                  </tr>
              <?php }
                  }else{
                  echo "<tr><td></td><td></td><td>Exam not submitted Yet</td><td></td><td></td><td></td></tr>";
                }
              } ?>
            </tbody>
          </table>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
    <script>
      function getStudentResults(id) {
        var exam_id = id;
        window.location.replace('one-student-result?id=' + exam_id);
      }
    </script>