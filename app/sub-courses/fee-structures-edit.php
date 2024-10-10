<?php
  if(isset($_GET['university_id']) && isset($_GET['mode']) && isset($_GET['durations']) && isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);
    $mode = intval($_GET['mode']);
    $durations = intval($_GET['durations']);

    if(empty($university_id) || empty($mode) || empty($durations) || empty($id)){
      exit();
    }

    $mode = $conn->query("SELECT Name FROM Modes WHERE ID = $mode");
    $mode = mysqli_fetch_assoc($mode);
    $mode = $mode['Name'];

    $vocational_query = "";
    $check_for_vocational = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Is_Vocational	= 1");
    if($check_for_vocational->num_rows>0){
      $vocational_query = " AND Name NOT LIKE 'Course%'";
    }

    $fee_structures = $conn->query("SELECT ID, Name, Fee_Applicable_ID FROM Fee_Structures WHERE Status = 1 AND Is_Constant = 1  AND University_ID = $university_id $vocational_query ORDER BY Name ASC");
    while ($fee_structure = $fee_structures->fetch_assoc()){
      $fee = $conn->query("SELECT * FROM Fee_Constant WHERE Sub_Course_ID = $id AND University_ID = $university_id AND Fee_Structure_ID = ".$fee_structure['ID']);
      $fee = mysqli_fetch_assoc($fee);
      $applicable = !empty($fee) ? (array)json_decode($fee['Applicable_In']) : (array)json_decode(json_encode(array($fee_structure['Fee_Applicable_ID']=>[])));
  ?>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label><?=$fee_structure['Name']?></label>
          <input type="tel" name="fee[<?=$fee_structure['ID']?>]" class="form-control" placeholder="ex: 5000" onkeypress="return isNumberKey(event)" value="<?php print !empty($fee) ? $fee['Fee'] : '' ?>" required>
        </div>
      </div>

      <div class="col-md-6">
        <?php if($fee_structure['Fee_Applicable_ID']==2 || $fee_structure['Fee_Applicable_ID']==3){ 
          if($fee_structure['Fee_Applicable_ID']==2){
            echo '<div class="form-check complete">';
            for($i=1; $i<=$durations; $i++){ ?>  
              <input type="checkbox" id="applicable_in_<?=$fee_structure['ID'].$i?>" <?php print in_array($i, $applicable[2]) ? 'checked' : '' ?> name="applicable_in[<?=$fee_structure['ID']?>][<?=$fee_structure['Fee_Applicable_ID']?>][<?=$i?>]">
              <label for="applicable_in_<?=$fee_structure['ID'].$i?>">
                <?=$mode.' '.$i?>
              </label>  
        <?php }
            echo '</div>';
          }elseif($fee_structure['Fee_Applicable_ID']==3){ ?>
            <div class="form-group form-group-default required">
              <label>Applicable In</label>
              <select class="full-width" style="border: transparent;" name="applicable_in[<?=$fee_structure['ID']?>][<?=$fee_structure['Fee_Applicable_ID']?>]">
                <option value="">Choose</option>
                <?php 
                  $admission_types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = $university_id");
                  while($admission_type = $admission_types->fetch_assoc()){
                    $selected = $admission_type['ID']==$applicable[3] ? 'selected' : '';
                    echo '<option value="'.$admission_type['ID'].'" '.$selected.'>'.$admission_type['Name'].'</option>';
                  }
                ?>      
              </select>
            </div>      
          <?php }
        ?>
        <?php } ?>
      </div>
    </div>
  <?php
    }
  }
