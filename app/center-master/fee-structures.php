<?php
  if(isset($_GET['university_id']) && isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);

    // Mode
    $available_modes = array();
    $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = $university_id");
    while($mode = $modes->fetch_assoc()){
      $available_modes[] = $mode['Name'];
    }
    $mode = implode('/', $available_modes);

    // Duration
    $duration = $conn->query("SELECT MAX(Min_Duration) as Duration FROM Sub_Courses WHERE University_ID = $university_id");
    $duration = mysqli_fetch_assoc($duration);
    $durations = $duration['Duration'];
    
    if(empty($university_id) || empty($mode) || empty($durations)){
      exit();
    }

    $alloted_fee_to_center = [];
    $alloted_fee_applicability = [];
    $alloted_fees = $conn->query("SELECT Fee, Applicable_In, Fee_Structure_ID FROM Fee_Variables WHERE Code = $id AND University_ID = $university_id");
    if($alloted_fees->num_rows > 0){
      while($alloted_fee = $alloted_fees->fetch_assoc()){
        $alloted_fee_to_center[$alloted_fee['Fee_Structure_ID']] = $alloted_fee['Fee'];
        $alloted_fee_applicability[$alloted_fee['Fee_Structure_ID']] = $alloted_fee['Applicable_In'];
      }
    }

    $fee_structures = $conn->query("SELECT ID, Name, Fee_Applicable_ID, Sharing FROM Fee_Structures WHERE Status = 1 AND (Sharing = 1 OR Is_Constant = 0) AND University_ID = $university_id ORDER BY Name ASC");
    while ($fee_structure = $fee_structures->fetch_assoc()){
  ?>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label><?=$fee_structure['Name']?></label>
          <input type="tel" 
            name="fee[<?=$fee_structure['ID']?>]"
            class="autonumeric form-control"
            placeholder="ex: <?php print $fee_structure['Sharing']==1 ? 'Enter University Share' : '5000' ?>"
            onkeypress="return isNumberKey(event)" 
            <?php print $fee_structure['Sharing']==1 ? 'data-v-min="0" data-v-max="100"' : '' ?>
            value="<?php print !empty($alloted_fee_to_center) ? $alloted_fee_to_center[$fee_structure['ID']] : ''?>"
            required
          >
        </div>
      </div>

      <div class="col-md-6">
        <?php if($fee_structure['Fee_Applicable_ID']==2 || $fee_structure['Fee_Applicable_ID']==3){ 
          if($fee_structure['Fee_Applicable_ID']==2){
            $applicability = [];
            if(!empty($alloted_fee_applicability) && $alloted_fee_applicability[$fee_structure['ID']][2]==2){
              $applicability = json_decode($alloted_fee_applicability[$fee_structure['ID']], true);
              $applicability = $applicability[2];
            }
            echo '<div class="form-check complete">';
            for($i=1; $i<=$durations; $i++){ ?>  
              <input type="checkbox" id="applicable_in_<?=$fee_structure['ID'].$i?>" <?php print !empty($applicability) ? (in_array($i, $applicability) ? 'checked' : '') : '' ?> name="applicable_in[<?=$fee_structure['ID']?>][<?=$fee_structure['Fee_Applicable_ID']?>][<?=$i?>]">
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
                    echo '<option value="'.$admission_type['ID'].'">'.$admission_type['Name'].'</option>';
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
?>
