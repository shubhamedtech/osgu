<?php
  session_start(); 
  session_destroy();        
  header("Cache-control: private, no-cache");
  header("Expires: Mon, 26 Jun 1997 05:00:00 GMT");
  header("Pragma: no-cache");
  header ("Last-Modified: " . gmdate ("D, d M Y H:i:s") . " GMT");
  header("Location: /");
