<?php
if(isset($_POST['session_id'])){
  session_start();
  $_SESSION['current_session'] = $_POST['session_id'];
  print_r($_SESSION);
}
