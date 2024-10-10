<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" media="screen" />
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
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              $sub_course_id = intval($_GET['sub_course_id']);
              ?>
              <div>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head'])) { ?>
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
        <div class="row">
          <div class="table-responsive">
            <table class="table table-striped position-relative" id="userTable" style="width:100%">
              <thead class="text-center">
                <tr>
                  <th>Student Name</th>
                  <th>Student Photo</th>
                  <th>Paper Name</th>
                  <th>Attendance</th>
                  <th>Attempted / Total Questions</th>
                  <th>Correct Ans</th>
                  <th>Submited In</th>
                  <th>Exam Time</th>
                  <th>Result</th>
                </tr>
              </thead>
              <tbody class="text-center">
                <?php
                if (isset($_GET['sub_course_id'])) {
                  $sub_course_id = intval($_GET['sub_course_id']);
                  $_SESSION['subii_id'] = $sub_course_id;
                  $table_paper_name = '';
                  $table_paper_date = '';
                  $total_question = 0;
                  $attempt_question = 0;
                  $correct = 0;

                  $exam_results = $conn->query("SELECT Exam_Students.*, Syllabi.Name as Syllabi_name FROM Exam_Students LEFT JOIN Syllabi ON Exam_Students.Sub_Course = Syllabi.Sub_Course_ID WHERE Syllabi.ID = '".$sub_course_id."'");
                  $paper_date = $conn->query("SELECT Exam_Date FROM Date_Sheets WHERE Syllabus_ID = ".$sub_course_id." ");
                  $table_paper_date = $paper_date->fetch_assoc();
                  while ($exam_result = $exam_results->fetch_assoc()) {
                    $exam_attend = $conn->query("SELECT * FROM Exam_Attempts_By_Exam_Students WHERE Student_ID = '" . $exam_result['ID'] . "' LIMIT 1");
                    $start = $exam_attend->fetch_assoc();
                    if (empty($start)) {
                      $start['Start_Time'] = 'Not Attend';
                    }

                    $exam_submit = $conn->query("SELECT * FROM Exam_Students_Final_Submit WHERE Student_ID = '" . $exam_result['ID'] . "' LIMIT 1");
                    $end = $exam_submit->fetch_assoc();
                    if (empty($end)) {
                      $end['Submited_At'] = 'NA';
                    } else {
                      $start_at = date("H:i:s", strtotime($start['Start_Time']));
                      $time1 = new DateTime($start_at);
                      $time2 = new DateTime($end['Submited_At']);
                      $interval = $time1->diff($time2);
                      $end['Submited_At'] =  $interval->format('%H : %i : %s');
                    }

                    $total_questions =  $conn->query("SELECT * FROM Exam_Students_Answers WHERE Student_ID = '" . $exam_result['ID'] . "' AND  Syllabus_ID = '" . $_SESSION['subii_id'] . "' ");
                    $attempt_question =  $conn->query("SELECT * FROM Exam_Students_Answers WHERE Student_ID = '" . $exam_result['ID'] . "' AND  Syllabus_ID = '" . $_SESSION['subii_id'] . "' AND Answer IS NOT NULL");

                    if ($total_questions->num_rows > 0) {
                      while ($total_question = $total_questions->fetch_assoc()) {
                        $correct_answers = $conn->query("SELECT * FROM MCQs WHERE ID = '" . $total_question['Question_ID'] . "'");
                        while ($correct_answer = $correct_answers->fetch_assoc()) {
                          if ($correct_answer['Answer'] == $total_question['Answer']) {
                            $correct = $correct + 1;
                          }
                        }
                      }
                    } else {
                      $correct = 0;
                    }
                  $table_paper_name = $exam_result['Syllabi_name'];
                  
                ?>
                    <tr>
                      <td><?= $exam_result['Name'] ?></td>
                      <td><?= $table_paper_date['Exam_Date']?></td>
                      <td><?= $exam_result['Syllabi_name'] ?></td>
                      <td><?= $exam_attend->num_rows > 0 ? "Attend" : "Not Attend" ?> </td>
                      <td><?= ($total_questions->num_rows > 0) ? $attempt_question->num_rows . " / " . $total_questions->num_rows : "NA" ?></td>
                      <td><?= $correct ?></td>
                      <td><?= $end['Submited_At'] ?></td>
                      <td><?= $start['Start_Time'] ?></td>
                      <td><button onclick="getStudentResults(<?= $exam_result['ID'] ?>, '<?= $exam_result['Name'] ?>')" class="btn btn-primary" <?php echo $end['Submited_At'] == "NA" ? 'disabled' : ''; ?> >View Result</button></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
      function getStudentResults(id, name) {
        var exam_id = id;
        window.location.replace('one-student-result?id=' + exam_id + '&&name=' + name);
      }
    </script>
    <script>
      $(document).ready(function() {
        var exam_date = 'Exam date: <?=$table_paper_date['Exam_Date']?>';
        var file_name = '<?=$table_paper_name?>';
        var sub_name = 'Exam paper: <?=$table_paper_name?>';
        $('#print_table').hide();
        $('#userTable').DataTable({
          dom: 'Bfrtip',
          "buttons": [
				{
					text: 'Download PDF',
					extend: 'pdfHtml5',
					filename: file_name,
					orientation: 'portrait', //portrait
					pageSize: 'A4', //A3 , A5 , A6 , legal , letter
					exportOptions: {
						columns: ':visible',
						search: 'applied',
						order: 'applied'
					},
					customize: function (doc) {
						doc.content.splice(0,1);
						var now = new Date();
						var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
            var logo = 'data:image/png;base64,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';

						doc.pageMargins = [40,60, 20,20];
						// Set the font size fot the entire document
						doc.defaultStyle.fontSize = 7;
						// Set the fontsize for the table header
						doc.styles.tableHeader.fontSize = 7;
						doc['header']=(function() {
							return {
								columns: [
									{
										image: logo,
										width: 100
									},
									{
										alignment: 'left',
										italics: true,
										text: sub_name,
										fontSize: 11,
										margin: [40,0]
									},
                  {
										alignment: 'right',
										fontSize: 11,
										text: exam_date,
                    margin: [50,0]
									}
								],
								margin: [40,20, 20,20]
							}
						});

						doc['footer']=(function(page, pages) {
							return {
								columns: [
									{
										alignment: 'left',
										text: ['Created on: ', { text: jsDate.toString() }]
									},
									{
										alignment: 'right',
										text: ['page ', { text: page.toString() },	' of ',	{ text: pages.toString() }]
									}
								],
								margin: 20
							}
						});

						var objLayout = {};
						objLayout['hLineWidth'] = function(i) { return .5; };
						objLayout['vLineWidth'] = function(i) { return .5; };
						objLayout['hLineColor'] = function(i) { return '#aaa'; };
						objLayout['vLineColor'] = function(i) { return '#aaa'; };
						objLayout['paddingLeft'] = function(i) { return 4; };
						objLayout['paddingRight'] = function(i) { return 4; };
						doc.content[0].layout = objLayout;
				}
				}]
        });
      });
    </script>