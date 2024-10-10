<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Export <span class="semi-bold"></span>Documents as</h5>
  </div>
  <div class="modal-body">
    <div class="row text-center">
      <div class="col-md-6 col-sm-6 col-xs-6">
        <img src="/assets/img/icons/pdf.png" class="cursor-pointer" onclick="exportPdf('<?=$id?>')" height="78" />
      </div>
      <div class="col-sm-6 col-sm-6 col-xs-6 sm-p-t-30">
        <img src="/assets/img/icons/zip.png" class="cursor-pointer" onclick="exportZip('<?=$id?>')" height="78" />
      </div>
    </div>
  </div>
<?php } ?>
