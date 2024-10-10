<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();

    $role_query = str_replace("{{ table }}", "Users", $_SESSION['RoleQuery']);
    $role_query = str_replace("{{ column }}", "ID", $role_query);

    $centers = $conn->query("SELECT ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users WHERE Role IN ('Center', 'Sub-Center') $role_query");
    while($center = $centers->fetch_assoc()){
      echo '<option value="'.$center['ID'].'">'.$center['Name'].'</option>';
    }
  }
