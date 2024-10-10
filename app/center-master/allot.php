<?php
if (isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['counsellor'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $university_id = intval($_POST['university_id']);
  $counsellor = intval($_POST['counsellor']);
  $sub_counsellor = intval($_POST['sub_counsellor']);
  $fee = empty($_POST['fee']) ? [] : $_POST['fee'];
  $applicable = empty($_POST['applicable_in']) ? [] : $_POST['applicable_in'];

  // Duration
  $duration = $conn->query("SELECT MAX(Min_Duration) as Duration FROM Sub_Courses WHERE University_ID = $university_id");
  $duration = mysqli_fetch_assoc($duration);
  $durations = $duration['Duration'];

  $fee_ids = !empty($fee) ? array_keys($fee) : [];
  $fee_ids = implode(',', $fee_ids);


  if (empty($id) || empty($university_id) || empty($counsellor)) {
    echo json_encode(['status' => 403, 'message' => 'All fields are required.']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Alloted_Center_To_Counsellor WHERE Code = $id AND University_ID = $university_id");
  if ($check->num_rows > 0) {
    $conn->query("DELETE FROM Alloted_Center_To_Counsellor WHERE Code = $id AND University_ID = $university_id");
    $conn->query("DELETE FROM Alloted_Center_To_SubCounsellor WHERE Code = $id AND University_ID = $university_id");
  }
  $update = $conn->query("INSERT INTO Alloted_Center_To_Counsellor (`Counsellor_ID`, `Code`, `University_ID`) VALUES ($counsellor, $id, $university_id)");

  if (!empty($sub_counsellor)) {
    $update = $conn->query("INSERT INTO Alloted_Center_To_SubCounsellor (`Sub_Counsellor_ID`, `Code`, `University_ID`) VALUES ($sub_counsellor, $id, $university_id)");
  }

  if ($update && !empty($fee)) {
    $fee_structures = $conn->query("SELECT ID, Name, Fee_Applicable_ID FROM Fee_Structures WHERE Status = 1 AND (Is_Constant = 0 OR Sharing = 1) AND ID IN ($fee_ids) ORDER BY Name ASC");
    if ($fee_structures->num_rows > 0) {

      while ($fee_structure = $fee_structures->fetch_assoc()) {
        $fee_head_applicability[$fee_structure['ID']] = $fee_structure['Fee_Applicable_ID'];
        if ($fee[$fee_structure['ID']] < 0) {
          echo json_encode(['status' => 400, 'message' => $fee_structure['Name'] . ' cannot be empty!']);
          exit();
        }

        if (($fee_structure['Fee_Applicable_ID'] == 2 || $fee_structure['Fee_Applicable_ID'] == 3) && (empty($applicable[$fee_structure['ID']]) || empty(array_filter($applicable[$fee_structure['ID']])))) {
          echo json_encode(['status' => 400, 'message' => $fee_structure['Name'] . ' applicable cannot be empty!']);
          exit();
        } elseif ($fee_structure['Fee_Applicable_ID'] == 1) {
          $applicablity_in = [];
          for ($i = 1; $i <= $durations; $i++) {
            $applicablity_in[] = $i;
          }
          $applicable[$fee_structure['ID']][1] = $applicablity_in;
        } elseif ($fee_structure['Fee_Applicable_ID'] == 4) {
          $applicable[$fee_structure['ID']][4] = [];
        }
      }

      foreach ($fee as $key => $value) {
        $applicable_in = [];
        if ($fee_head_applicability[$key] == 2) {
          $applicable_in[2] = array_keys($applicable[$key][2]);
        } else {
          $applicable_in[$fee_head_applicability[$key]] = $applicable[$key][$fee_head_applicability[$key]];
        }
        $final_applicability = json_encode($applicable_in);
        $conn->query("DELETE FROM Fee_Variables WHERE Code = $id AND University_ID = $university_id AND Fee_Structure_ID = $key");
        $add_fee = $conn->query("INSERT INTO Fee_Variables (`Fee_Structure_ID`, `University_ID`, `Fee`, `Code`, `Applicable_In`) VALUES ($key, $university_id, '$value', $id, '$final_applicability')");
      }
      if ($add_fee) {
        echo json_encode(['status' => 200, 'message' => 'Center alloted successlly!']);
        exit();
      } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
        exit();
      }
    }
    echo json_encode(['status' => 200, 'message' => 'Center alloted successlly!']);
    exit();
  } elseif ($update) {
    echo json_encode(['status' => 200, 'message' => 'Center alloted successlly!']);
    exit();
  }
}
