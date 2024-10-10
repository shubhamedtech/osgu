<?php
ini_set('display_errors', 1); 
if (isset($_GET['syllabus_id']) && isset($_GET['id']) && isset($_GET['date_sheet'])) {
  require '../../includes/db-config.php';
  session_start();
  date_default_timezone_set("Asia/Kolkata");

  
  if ($_SESSION['Status'] == 0) {
    echo '<center><h3>Please contact your Co-ordinator.</h3></center>';
    exit;
  }

  $date_sheet_id = intval($_GET['date_sheet']);
  $syllabus_id = intval($_GET['syllabus_id']);
  $student_id = intval($_GET['id']);

  if (empty($date_sheet_id) || empty($syllabus_id) || empty($student_id)) {
    echo '<center><h3>Please contact your Co-ordinator.</h3></center>';
    exit;
  }

  $conn->query("INSERT INTO Exam_Attempts_By_Exam_Students (Student_ID, Date_Sheet_ID, Start_Time, End_Time) VALUES ($student_id, $date_sheet_id, now(), now())");

  $date_sheet = $conn->query("SELECT * FROM Date_Sheets WHERE ID = $date_sheet_id");
  $date_sheet = $date_sheet->fetch_assoc();

  $total_seconds = ((date("H", strtotime($date_sheet['End_Time'])) - date("H")) *60*60) + ((date("i", strtotime($date_sheet['End_Time'])) - date("i")) *60) + (date("s", strtotime($date_sheet['End_Time'])) - date("s"));

  if (strtotime($date_sheet['End_Time']) < strtotime(date('H:i:s'))) {
    echo '<center><h3>Exam Over.</h3></center>';
    exit;
  }

  $questions = $conn->query("SELECT * FROM MCQs WHERE Syllabus_ID = $syllabus_id ORDER BY RAND()");

  if ($questions->num_rows == 0) {
    echo '<center><h3>Please contact your Co-ordinator.</h3></center>';
    exit;
  }

  // Check Assigned Questions
  $assigned = $conn->query("SELECT Question_ID, Answer FROM Exam_Students_Answers WHERE Student_ID = " . $_SESSION['ID'] . " AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id");
  if ($assigned->num_rows == 0) {
    while ($question = $questions->fetch_assoc()) {
        $assign = $conn->query("INSERT INTO `Exam_Students_Answers` (`Student_ID`, `Date_Sheet_ID`, `Syllabus_ID`, `Question_ID`) VALUES (" . $_SESSION['ID'] . ", $date_sheet_id, $syllabus_id, " . $question['ID'] . ")");
    }
  }

  $question_count = 0;
  $remening_question = 0;
  $attempted_question = 0;
  $answers = $conn->query("SELECT * FROM Exam_Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id ");
  while ($answer = $answers->fetch_assoc()) {
    if($answer['Answer']){
      $attempted_question++;
    }
  }

  $remening_question = $answers->num_rows - $attempted_question;

  $assigned_question = $conn->query("SELECT Question_ID FROM Exam_Students_Answers WHERE Student_ID = " . $_SESSION['ID'] . " AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id LIMIT 1");

  $questions = $conn->query("SELECT * FROM MCQs WHERE ID = ".$assigned_question->fetch_assoc()['Question_ID']." ");
?>
<style>
.timer_class{
	margin-bottom: 10px;
    font-size: 15px;
    color: red;
    font-weight: 600;
  	text-align:center;
  } 
  .border-right {
    border-right: 2px solid #111!important;
}
</style>
  <div class="row">
    <div class="col-md-9">
      <center>
        <h6 style="color:red;">Note: Please do not refresh the page!</h6>
      </center>
    </div>
    <div class="col-md-3 timer_class">
        <h6 id="count" class="text-dark">Time Remaining: </h6>
        <span id="timer" class="timer"></span>
    </div>
  </div>
  <form role="form" id="exam-form" action="/app/exam-students/answers" method="post" enctype="multipart/form-data">
    <div class="row">
      <div class="col-md-9">
        <div class="card flex-row" style="padding:20px;">
          <span class="font-weight-bolder mr-2 pr-2 border-right">Final Submission Time : <span class="text-danger"><?= date('h:i A', strtotime($date_sheet['End_Time'])) ?></span></span>
          <span class="font-weight-bolder mr-2 pr-2 border-right">Total Questions :<span> <?=$answers->num_rows?></span></span>
          <span class="font-weight-bolder mr-2 pr-2 border-right">Attempted Questions :<span id="atte_question"> <?=$attempted_question?></span></span>
          <span class="font-weight-bolder">Remaining Questions :<span id="reme_question"> <?=$remening_question ?></span ></span>
        </div>
        <div class="card">
          <div class="card-body" id="current-question">
            <?php
            $counter = 1;
            while ($question = $questions->fetch_assoc()) {
              $selected['Answer'] = array();
              if($assigned->num_rows > 0){
                $selected = $assigned->fetch_assoc();
              }
              $options = json_decode($question['Options'], true);
            ?>
              <div class="row m-t-20">
                <div class="col-md-12 d-flex justify-content-between">
                  <div>
                    <input type="hidden" id="counter" value="<?=$counter?>"/>
                    <p class="fs-14 font-weight-bold"><?= $counter++ . '.&nbsp;&nbsp;&nbsp;&nbsp;' . $question['Question'] ?></p>
                  </div>
                  <div>
                    <b>(Marks: <?= $question['Marks'] ?>)</b>
                  </div>
                </div>
                <div class="col-md-12">
                  <?php foreach ($options as $key => $value) { ?>
                    <div class="form-check">
                      <input type="hidden" name="question_id" id="question_id" value="<?= $question['ID'] ?>">
                      <input type="radio" onclick="updateOverview();" name="answer[<?= $question['ID'] ?>]" id="option_<?= $question['ID'] . '_' . $key ?>" value="<?= $value ?>" <?php echo $value == $selected['Answer'] ? 'checked' : '' ?>>
                      <label for="option_<?= $question['ID'] . '_' . $key ?>">
                        <?= $value ?>
                      </label>
                    </div>
                  <?php } ?>
                </div>
              </div>
            <?php
            }
            ?>
          </div>
          <div class="card-body" id="next-question">
          </div>
          <div class="card-body previous-question">
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-12 justify-content-between">
          <button type="button" id="previous" onclick="getPreviousQuestins()" class="btn btn-primary mx-md-0 mx-2" disabled>Previous</button>
          <button type="button" id="saved" onclick="getNextQuestins();" class="btn btn-primary pull-right mx-md-0 mx-2"> Next</button>
          </div>
        </div>
      </div>
    </form>
      <div class="col-md-3">
        <div class="row">
            <!-- number -->
            <div class="col-md-12">
            <div class="card">
              <div class="card-header seperator">
                <h6>Questions</h6>
              </div>
              <div class="card-body">
                <?php
                  $student_questions = $conn->query("SELECT Question_ID FROM Exam_Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id ORDER BY ID ASC");
                  $i = 1;
                  while($question = $student_questions->fetch_assoc()) {
                      $answers = $conn->query("SELECT * FROM Exam_Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id AND Question_ID = ".$question['Question_ID']." ORDER BY ID ASC");
                      $answer = $answers->fetch_assoc();
                        if(!empty($answer) && $answer['Answer']){ ?>
                          <a href="#" onclick="getSingleQuestin(<?=$question['Question_ID']?>, <?=$i?>);" class="btn btn-success mb-2" id="answered_<?=$i?>"><?=$i++?></a>
                          <?php } else {?>
                            <a href="#" onclick="getSingleQuestin(<?=$question['Question_ID']?>, <?=$i?>);" class="btn btn-secondary mb-2" id="not_answered_<?=$question['Question_ID']?>"><?=$i++?></a>
                    <?php } }?>
              </div>
            </div>
          </div>
          </div>
          </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </form>
    </div>
        </div>
      </div>
    </div>
  </form>

  <script type="text/javascript">
    $("#exam-form").on("submit", function(e) {
      if ($('#exam-form').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('student_id', <?= $student_id ?>);
        formData.append('date_sheet_id', <?= $date_sheet_id ?>);
        formData.append('syllabus_id', <?= $syllabus_id ?>);
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(results) {
            if (results.status == 200) {
              // console.log(results.data);
                var counter =  $("#counter").val();
                var reme_question = $('#reme_question').html();
                var reme_number = parseInt(reme_question) - 1;
                var attempt_qus_number = $('#atte_question').html();
                var number = parseInt(attempt_qus_number) + 1;
                if(reme_number >= 0){
                  $('#reme_question').html(reme_number);                
                  $('#atte_question').html(number);
                }

                $('#not_answered_'+results.data).removeClass('btn-secondary');
                $('#not_answered_'+results.data).addClass('btn-success');
            } else {
    
            }
          }
        });
        e.preventDefault();
      }
    });

    // function updateOverview() {
    //   $('#submit').click();
    // }
  </script>
 <script type="text/javascript">
  function updateOverview() {
      $("#exam-form").submit();
  }
 </script>
        
        
  <script type="text/javascript">
    function getNextQuestins() {
      // $("#exam-form").submit();
      var Question_id = $("#question_id").val();
      var counter =  $("#counter").val();
      var attempt_question = $("#attempt_question").val() + 1;
      $.ajax({
        url: '../../../app/exam-students/ajax/get-next-question?question_id='+Question_id+'&counter='+counter+'&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>+'&exam_from=exam-student',
        type: 'GET',
        success: function(data) {
          if(data.status == "All_question_viewed"){
            $("#next-question").show();
            $("#current-question").hide();
            $("#saved").hide();
            $("#next-question").html('<center><h3 id="no_question">Submit your final exam!</h3><button type="button" id="submit" class="btn btn-success" onclick="finalSubmit();">Submit</button></center>');
          }else{
            $("#current-question").html('');
            $("#previous").prop('disabled', false);
            $("#counter").val(counter+1);
            $("#current-question").html(data);
          }
        }
      })
    }
  </script>
  <script type="text/javascript">
    function getSingleQuestin(id, counter_id) {
      $("#current-question").show();
      $("#next-question").html('');
      $("#saved").show();
      var counter_vai = counter_id;
      $.ajax({
        url: '../../../app/exam-students/ajax/get-single-question?question_id='+id+'&counter='+counter_vai+'&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>+'&exam_from=exam-student',
        type: 'GET',
        success: function(data) {
          if(data.status == "All_question_viewed"){
            $("#next-question").show();
            $("#saved").hide();
            $("#current-question").hide();
            $("#next-question").html('<center><h3 id="no_question">Submit your final exam!</h3><button type="button" id="submit" class="btn btn-success" onclick="finalSubmit();">Submit</button></center>');
          }else{
            $("#current-question").html('');
            $("#previous").prop('disabled', false);
            $("#counter").val(counter_vai+1);
            $("#current-question").html(data);
          }
        }
      })
    }
  </script>
  <script type="text/javascript">
    function getPreviousQuestins() {
      $("#current-question").show();
      $("#next-question").html('');
      $("#saved").show();
      $("#next-question").hide();
      var Question_id = $("#question_id").val();
      console.log(Question_id);
      var counter =  $("#counter").val();
      if(counter == 2){
        $("#previous").prop('disabled', true);
      }
      $.ajax({
        url: '../../../app/exam-students/ajax/get-previous-question.php?question_id='+Question_id+'&counter='+counter+'&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>+'&exam_from=exam-student',
        type: 'GET',
        success: function(data) {
          $("#counter").val(counter -1);
          $("#current-question").html(data);
        }
      })
    }
  </script>
  <script>
    function finalSubmit(){
      $.ajax({
        url: '../../../app/exam-students/ajax/final-submit?date_sheet_id=<?= $date_sheet_id?>&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>,
        type: 'GET',
        success: function(data) {
          if (data.status == 200) {
              notification('success', data.message);
              window.location.replace('/dashboard');
            }
        }
      })
    }
  </script>

<script>
    var count = <?=$total_seconds?>;
    var counter = setInterval(timer, 1000); //1000 will  run it every 1 second
    function timer() {
        count = count - 1;
        if (count == -1) {
          clearInterval(counter);
          return;
        }

        var seconds = count % 60;
        var minutes = Math.floor(count / 60);
        var hours = Math.floor(minutes / 60);
        minutes %= 60;
        hours %= 60;
      if(hours === 0 && minutes === 0  && seconds === 0){
          $.ajax({
            url: '../../../app/exam-students/ajax/final-submit?date_sheet_id=<?= $date_sheet_id?>&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>,
            type: 'GET',
            success: function(data) {
              if (data.status == 200) {
                notification('success', "Exam time over!!");
                location.reload();
              }
            }
          })
        }else{
        document.getElementById("timer").innerHTML = hours + " Hours " + minutes + " Minutes " + seconds + " Seconds "; // watch for spelling
        }
    }
</script>
<?php }
