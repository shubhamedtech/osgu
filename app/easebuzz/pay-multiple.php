<?php
if (isset($_POST['amount']) && isset($_POST['ids'])) {
  session_start();
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';

  $ids = is_array($_POST['ids']) ? array_filter($_POST['ids']) : [];
  $amount = sprintf("%.1f", $_POST['amount']);
  $transaction_id = strtoupper(strtolower(uniqid()));

  if (empty($ids)) {
    exit(json_encode(['status' => false, 'message' => 'Please select student!']));
  }

  foreach ($ids as $id) {
    $duration = $conn->query("SELECT Duration FROM Students WHERE ID = $id");
    $duration = $duration->fetch_assoc();
    $duration = $duration['Duration'];

    $balance = balanceAmount($conn, $id, $duration);

    $add = $conn->query("INSERT INTO Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, $duration, " . $_SESSION['university_id'] . ", '$transaction_id', $balance)");
  }

  $key = $_SESSION['access_key'];
  $salt = $_SESSION['secret_key'];

  $product_info = 'Fee Payment';

  $value = $key . '|' . $transaction_id . '|' . $amount . '|' . $product_info . '|' . trim($_SESSION['Name']) . '|' . trim($_SESSION['Email']) . '|||||||||||' . $salt;
  $hash = hash('sha512', $value);

  $conn->query("INSERT INTO Payments (`Type`, `Amount`, `Transaction_ID`, `Added_By`, `University_ID`) VALUES (2, '$amount', '$transaction_id', '" . $_SESSION['ID'] . "', '" . $_SESSION['university_id'] . "')");

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://pay.easebuzz.in/payment/initiateLink',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
      'key' => $key,
      'txnid' => $transaction_id,
      'amount' => $amount,
      'productinfo' => $product_info,
      'firstname' => trim($_SESSION['Name']),
      'phone' => $_SESSION['Mobile'],
      'email' => trim($_SESSION['Email']),
      'surl' => 'http://localhost:3000/response.php',
      'furl' => 'http://localhost:3000/response.php',
      'udf1' => "",
      'udf2' => "",
      'udf3' => "",
      'udf4' => "",
      'udf5' => "",
      'udf6' => "",
      'udf7' => "",
      'hash' => $hash
    ),
    CURLOPT_HTTPHEADER => array(
      'Cookie: csrftoken=snWOPdXYVpBAqLuFIUxANDKiKw3slBhr'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;
}
