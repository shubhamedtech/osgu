<?php
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    $documents = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id");
    while($document = $documents->fetch_assoc()) {
      $files = explode('|', $document['Location']);
      foreach($files as $file) {
        unlink('../..'.$file);
      }
    }

    $delete_docs = $conn->query("DELETE FROM Student_Documents WHERE Student_ID = $id");
    $delete_academics = $conn->query("DELETE FROM Student_Academics WHERE Student_ID = $id");
    $delete_details = $conn->query("DELETE FROM Students WHERE ID = $id");
    if($delete_details){
      echo json_encode(['status'=>200, 'message'=>'Student deleted successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Server is not responding. Please try again later']);
    }
  }
