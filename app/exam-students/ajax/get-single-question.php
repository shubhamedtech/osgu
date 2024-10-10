<?php
    if (isset($_GET['syllabus_id']) && isset($_GET['id']) && isset($_GET['id']) ) {
        require '../../../includes/db-config.php';
        session_start();
        date_default_timezone_set("Asia/Kolkata");
        $Question_id = $_GET['question_id'];
        $syllabus_id = $_GET['syllabus_id'];
        $counter = $_GET['counter'];
        $exam_from = $_GET['exam_from'];

        if($exam_from == "reguler-student"){
            $assigned_question = $conn->query("SELECT ID FROM Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id AND Question_ID = $Question_id ");
            $question_first = $assigned_question->fetch_assoc()['ID'];
            $assigned_question = $conn->query("SELECT Question_ID FROM Students_Answers WHERE ID = ".$question_first." ");
            $questions = $conn->query("SELECT * FROM MCQs WHERE ID = ".$assigned_question->fetch_assoc()['Question_ID']." ");

            if($questions->num_rows > 0){
                while ($question = $questions->fetch_assoc()) {
                    $assigned = $conn->query("SELECT Answer FROM Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id AND Question_ID = ".$question['ID']." ");
                    $selected = $assigned->fetch_assoc();
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
            }
        }else{
            $assigned_question = $conn->query("SELECT ID FROM Exam_Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id AND Question_ID = $Question_id ");
            $question_first = $assigned_question->fetch_assoc()['ID'];
            $assigned_question = $conn->query("SELECT Question_ID FROM Exam_Students_Answers WHERE ID = ".$question_first." ");
            $questions = $conn->query("SELECT * FROM MCQs WHERE ID = ".$assigned_question->fetch_assoc()['Question_ID']." ");

            if($questions->num_rows > 0){
                while ($question = $questions->fetch_assoc()) {
                    $assigned = $conn->query("SELECT Answer FROM Exam_Students_Answers WHERE Student_ID = ".$_SESSION['ID']." AND Syllabus_ID = $syllabus_id AND Question_ID = ".$question['ID']." ");
                    $selected = $assigned->fetch_assoc();
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
            }
        }
    }
?>