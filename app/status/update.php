<?php

if (isset($_POST['table']) && isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $table = mysqli_real_escape_string($conn, $_POST['table']);
  $table = str_replace('-', '_', $table);

  $column = "Status";
  if (isset($_POST['column']) && !empty($_POST['column'])) {
    $column = $_POST['column'];
  }
  $inputStatus=null;
  if (isset($_POST['status']) && !empty($_POST['status'])) {
    $inputStatus = $_POST['status'];
  }

  if ($table == 'Students') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  } else {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
  }

  if (empty($table) || empty($id)) {
    echo json_encode(['status' => 403, 'message' => 'Forbidden']);
    exit();
  }

  $get_status = $conn->query("SELECT $column FROM $table WHERE ID = $id OR id = $id");
  if ($get_status->num_rows > 0) {
    $status = mysqli_fetch_assoc($get_status);

    
    //soft delete
    if($inputStatus!=null){
      $update = $conn->query("UPDATE $table SET $column = 2 WHERE ID = $id OR id = $id");
      if ($update) {
        echo json_encode(['status' => 200, 'message' => 'Record deleted successfully!']);
        die;
      } else {
        echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
        die;
      }
    }
    //status update
    if ($status[$column] == 1) {
      $update = $conn->query("UPDATE $table SET $column = 0 WHERE ID = $id OR id = $id");
    } else {
      $update = $conn->query("UPDATE $table SET $column = 1 WHERE ID = $id OR id = $id");
    }
   
    if ($update) {
      echo json_encode(['status' => 200, 'message' => $column . ' changed successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 404, 'message' => 'No record found!']);
  }
} else {
  echo json_encode(['status' => 403, 'message' => 'Forbidden']);
  exit();
}


// if (isset($_POST['table']) && isset($_POST['id'])) {
//   require '../../includes/db-config.php';
//   session_start();

//   $table = mysqli_real_escape_string($conn, $_POST['table']);
//   $table = str_replace('-', '_', $table);

//   $column = "Status";
//   if (isset($_POST['column']) && !empty($_POST['column'])) {
//     $column = $_POST['column'];
//   }

//   if ($table == 'Students') {
//     $id = mysqli_real_escape_string($conn, $_POST['id']);
//     $id = base64_decode($id);
//     $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
//   } else {
//     $id = mysqli_real_escape_string($conn, $_POST['id']);
//   }

//   if (empty($table) || empty($id)) {
//     echo json_encode(['status' => 403, 'message' => 'Forbidden']);
//     exit();
//   }

//   $get_status = $conn->query("SELECT $column FROM $table WHERE ID = $id");
//   if ($get_status->num_rows > 0) {
//     $status = mysqli_fetch_assoc($get_status);

//     if ($status[$column] == 1) {
//       $update = $conn->query("UPDATE $table SET $column = 0 WHERE ID = $id");
//     } else {
//       $update = $conn->query("UPDATE $table SET $column = 1 WHERE ID = $id");
//     }
//     if ($update) {
//       echo json_encode(['status' => 200, 'message' => $column . ' changed successfully!']);
//     } else {
//       echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
//     }
//   } else {
//     echo json_encode(['status' => 404, 'message' => 'No record found!']);
//   }
// } else {
//   echo json_encode(['status' => 403, 'message' => 'Forbidden']);
//   exit();
// }
