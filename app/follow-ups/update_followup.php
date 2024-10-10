<?php
  if(isset($_POST['value'])){
    session_start();
    $_SESSION['current_followup'] = $_POST['value'];
  }
