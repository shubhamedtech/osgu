<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $alloted_check = $conn->query("SELECT ID FROM Alloted_Center_To_SubCounsellor WHERE Sub_Counsellor_ID = $id");
  if ($alloted_check->num_rows > 0) {
    echo json_encode(['status' => 200, 'message' => $alloted_check->num_rows . ' Centers is alloted! Please remove it first!']);
    exit();
  }

  $check = $conn->query("SELECT ID FROM Users WHERE ID = $id");
  if ($check->num_rows > 0) {
    $check = $conn->query("SELECT University_ID FROM University_User WHERE `User_ID` = $id");
    if ($check->num_rows == 0) {
      if ($_SESSION['Role'] == 'Administrator') {
        $delete = $conn->query("DELETE FROM University_User WHERE `User_ID` = $id");
      } else {
        $delete = $conn->query("DELETE FROM University_User WHERE `User_ID` = $id AND University_ID = " . $_SESSION['university_id']);
      }
    }
    $delete = $conn->query("DELETE FROM Users WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'User deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'User not exists!']);
  }
}
