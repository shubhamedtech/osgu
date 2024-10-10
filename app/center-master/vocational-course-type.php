<?php
  if(isset($_GET['university_id']) && isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);

    $course_types_array = [];
    $alloted_course_types = $conn->query("SELECT Course_Type_ID FROM Center_Course_Types WHERE `User_ID` = $id AND University_ID = $university_id");
    while($alloted_course_type = $alloted_course_types->fetch_assoc()){
      $course_types_array[] = $alloted_course_type['Course_Type_ID'];
    }
?>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default form-group-default-select2 required">
          <label>Course Type</label>
          <select class="full-width" id="course_type" name="course_type[]" onchange="getVocationalCourse()" multiple>
            <?php
              $course_types = $conn->query("SELECT ID, Name FROM Course_Types WHERE University_ID = $university_id ORDER BY Name ASC");
              while ($course_type = $course_types->fetch_assoc()){ ?>
                <option value="<?=$course_type['ID']?>"><?=$course_type['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div id="vocational_course">

    </div>

    <script type="text/javascript">
      $('#course_type').val([<?=implode(",", $course_types_array)?>]).select2({
        placeholder: 'Course Type'
      }).change();
    </script>

    <script type="text/javascript">
      function getVocationalCourse(){
        var type_ids = $('#course_type').val();
        $.ajax({
          url: '/app/center-master/vocational-courses?ids='+type_ids+'&university_id=<?=$university_id?>&user_id=<?=$id?>',
          type:'GET',
          success: function(data) {
            $('#vocational_course').html(data);
          }
        })
      }
      getVocationalCourse();
    </script>


<?php } ?>
