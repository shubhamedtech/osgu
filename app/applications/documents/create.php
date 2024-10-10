<?php
$search = "";
if (isset($_GET['search'])) {
  $search = "?searchValue=" . $_GET['search'];
}
?>

<!-- Modal -->
<div class="modal-header clearfix text-left mb-4">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Export <span class="semi-bold"></span>Documents</h5>
</div>
<form role="form" action="/app/applications/documents/export<?= $search ?>" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <?php
        $documents = array('Photo', 'Student Signature', 'Parent Signature', 'Aadhar', 'Affidavit', 'Migration', 'Other Certificate', 'High School', 'Intermediate', 'UG', 'PG', 'Other');
        foreach ($documents as $document) { ?>
          <div class="row">
            <div class="col-md-12 form-check complete">
              <input type="checkbox" id="document_<?= str_replace(" ", "_", $document) ?>" name="download[]" value="<?= $document ?>">
              <label for="document_<?= str_replace(" ", "_", $document) ?>" class="font-weight-bold">
                <?= $document ?>
              </label>
            </div>
          </div>
        <?php }
        ?>
      </div>
    </div>
  </div>
  <div class="modal-footer d-flex justify-content-between">
    <div class="m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" name="pdf" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Export as PDF</span>
        <span class="hidden-block">
          <i class="uil uil-file-download"></i>
        </span>
      </button>
    </div>
    <div class="m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" name="zip" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Export as Image</span>
        <span class="hidden-block">
          <i class="uil uil-image-download"></i>
        </span>
      </button>
    </div>
  </div>
</form>