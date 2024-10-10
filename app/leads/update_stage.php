<?php
  if(isset($_POST['stage'])){
    session_start();
    $_SESSION['current_stage'] = $_POST['stage'];
  }
