<?php if(isset($_GET['id'])){
  require '../../includes/db-config.php';
  $university_id = base64_decode($_GET['id']);
  $check = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id");
  if($check->num_rows>0){
?>
<link href="../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
<div class="card-group horizontal" id="accordion" role="tablist" aria-multiselectable="true">
  <?php include('admission-types.php'); ?>
  <?php include('modes.php'); ?>
  <?php include('schemes.php'); ?>
  <?php include('admission-sessions.php'); ?>
  <?php include('exam-sessions.php'); ?>
  <?php include('course-types.php'); ?>
  <?php include('fee-structures.php'); ?>
  <?php include('page-access.php'); ?>
</div>
<?php } }
