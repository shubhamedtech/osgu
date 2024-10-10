<?php 
if(isset($_POST['id']) && isset($_POST['reason'])){
  require '../../../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $reason = mysqli_real_escape_string($conn, $_POST['reason']);

  $invoice = $conn->query("SELECT * FROM Invoices WHERE ID = $id");
  if($invoice->num_rows>0){
    $invoice = $invoice->fetch_assoc();

    $update = $conn->query("INSERT INTO Cancel_Students (`Invoice_ID`, `Student_ID`, `Reason`) VALUES ($id, ".$invoice['Student_ID'].", '$reason')");
    if($update){
      echo json_encode(['status'=>true, 'message'=>'Student marked as cancel, Please wait for approval']);
    }else{
      echo json_encode(['status'=>false, 'message'=>'Something went wrong!']);
    }
  }else{
    echo json_encode(['status'=>false, 'message'=>'Student not found!']);
  }
}
