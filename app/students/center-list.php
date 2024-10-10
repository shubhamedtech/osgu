<?php
require '../../includes/db-config.php';
session_start();

$role_query = str_replace('{{ table }}', 'Users', $_SESSION['RoleQuery']);
$role_query = str_replace('{{ column }}', 'ID', $role_query);

$centers = $conn->query("SELECT ID, CONCAT(UPPER(Name), ' (', Code, ')') as Name FROM Users WHERE Role = 'Center' $role_query ORDER BY Code ASC");
$options = '<option value="">Select</option>';
while ($center = $centers->fetch_assoc()) {
  $options .= '<option value="' . $center['ID'] . '">' . $center['Name'] . '</option>';
}

echo $options;
