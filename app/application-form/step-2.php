<?php
if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $inserted_id = intval($_POST['inserted_id']);

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  $step = $conn->query("SELECT Step FROM Students WHERE ID = $inserted_id");
  $step = mysqli_fetch_array($step);
  $step = $step['Step'];

  $step_query = "";
  if ($step < 2) {
    $step_query = ", `Step` = 2";
  }


  $email = mysqli_real_escape_string($conn, $_POST['email']);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid email']);
    exit();
  }
  $email = strtolower($email);

  $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
  if (!empty($alternate_email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid alternate email']);
    exit();
  }
  $alternate_email = strtolower($alternate_email);

  $contact = mysqli_real_escape_string($conn, $_POST['contact']);
  $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);

  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $address = strtoupper(strtolower($address));
  $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $city = strtoupper(strtolower($city));
  $district = mysqli_real_escape_string($conn, $_POST['district']);
  $district = strtoupper(strtolower($district));
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $state = strtoupper(strtolower($state));

  $address = json_encode(['present_address' => $address, 'present_pincode' => $pincode, 'present_city' => $city, 'present_district' => $district, 'present_state' => $state]);

  $update = $conn->query("UPDATE Students SET Email = '$email', Alternate_Email = '$alternate_email', Contact = '$contact', Alternate_Contact = '$alternate_contact', Address = '$address' $step_query WHERE ID = $inserted_id");

  if ($update) {
    echo json_encode(['status' => 200, 'message' => 'Step 2 details saved successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
