<!-- START HEADER -->
<div class="header ">
  <!-- START MOBILE SIDEBAR TOGGLE -->
  <a href="#" class="btn-link toggle-sidebar d-lg-none pg-icon btn-icon-link" data-toggle="sidebar">
    menu</a>
  <!-- END MOBILE SIDEBAR TOGGLE -->
  <div class="">
    <div class="brand inline">
      <?php if ($_SESSION['Role'] != 'Sub-Center') {
        if (!empty($dark_logo)) { ?>
          <img src="<?= $dark_logo ?>" alt="logo" data-src="<?= $dark_logo ?>" data-src-retina="<?= $dark_logo_retina ?>" height="35" style="max-width:120px">
        <?php }
      } elseif ($_SESSION['Role'] == 'Sub-Center') {
        $logo = $conn->query("SELECT Users.Photo FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Center = Users.ID WHERE Sub_Center = " . $_SESSION['ID'] . " AND Users.Photo != '/assets/img/default-user.png'");
        if ($logo->num_rows > 0) {
          $logo = $logo->fetch_assoc();
        ?>
          <img src="<?= $logo['Photo'] ?>" alt="center_logo" data-src="<?= $logo['Photo'] ?>" data-src-retina="<?= $logo['Photo'] ?>" height="35">
      <?php }
      } ?>
    </div>

    <?php
    $page = array_filter(explode("/", $_SERVER['REQUEST_URI']));
    $page = $page[1];
    if (isset($_SESSION['university_id']) && (!in_array($_SESSION['Role'], ['Administrator', 'Student']) || ($_SESSION['Role'] == 'Administrator' && $page == 'admissions') || ($_SESSION['Role'] == 'Administrator' && in_array($_SERVER['REQUEST_URI'], ['/users/centers', '/leads/generate', '/leads/lead-details', '/academics/programs', '/academics/specializations', '/academics/departments']))) || ($_SESSION['Role'] == 'Administrator' && in_array($_SERVER['REQUEST_URI'], ['/users/centers', '/leads/lists', '/leads/follow-ups', '/leads/lead-details']))) { ?>
      <!-- START NOTIFICATION LIST -->
      <ul class="d-lg-inline-block notification-list no-margin d-lg-inline-block b-grey b-l no-style p-l-20 p-r-20">
        <li class="p-r-5 inline">
          <a href="javascript:;" id="notification-center" class="header-icon" <?php if ($_SESSION['Role'] == 'Administrator' || (isset($_SESSION['Alloted_Universities']) && count($_SESSION['Alloted_Universities']) > 1)) : echo 'onclick="changeUniversity()';
                                                                              endif; ?>">
            <img src="<?= $_SESSION['university_logo'] ?>" alt="logo" data-src="<?= $_SESSION['university_logo'] ?>" data-src-retina="<?= $_SESSION['university_logo'] ?>" height="42px">
          </a>
        </li>
      </ul>
      <!-- END NOTIFICATIONS LIST -->
    <?php } ?>
  </div>
  <div class="d-flex align-items-center">
    <?php if (($_SESSION['Role'] == 'Administrator' && $page == 'admissions') || ($_SESSION['Role'] == 'Administrator' && $_SERVER['REQUEST_URI'] == '/users/centers') || (isset($_SESSION['Alloted_Universities']) && count($_SESSION['Alloted_Universities']) > 1)) { ?>
      <button class="btn btn-outline-primary btn-lg d-none d-sm-none d-md-block mr-4" onclick="changeUniversity()">Change University</button>
    <?php } ?>
    <div class="m-2">
        <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { 
  			//Total Amount
        	$amounts = $conn->query("SELECT sum(Amount) as total_amt FROM Wallets WHERE Added_By = " . $_SESSION['ID'] . " AND Status = 1");
          	$amounts = $amounts->fetch_assoc();
  
  			//Debit Amount
  			$debited_amount = 0;
  			$debit_amts = $conn->query("SELECT sum(Amount) as debit_amt FROM Wallet_Payments WHERE Added_By = " . $_SESSION['ID'] . " AND Type = 3");
  			if($debit_amts->num_rows > 0){
              $debit_amt = $debit_amts->fetch_assoc();
              $debited_amount = $debit_amt['debit_amt'];
            }
          	
  			$amount = $amounts['total_amt'] - $debited_amount;
      	?>
          <a href="#" class="btn btn-primary" aria-label="" title="" data-toggle="tooltip" data-original-title="Available Balance"><?=$amount?> <i class="uil uil-wallet"></i></a>
           <a href="/accounts/wallet-payments" class="btn btn-success" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Amount"> <i class="uil uil-plus"></i></a>
         <?php } ?>
    </div>
    <!-- START User Info-->
    <div class="dropdown pull-right">
      <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="profile dropdown">
        <span class="thumbnail-wrapper d32 circular inline">
          <img src="<?= $_SESSION['Photo'] ?>" alt="" data-src="<?= $_SESSION['Photo'] ?>" data-src-retina="<?= $_SESSION['Photo'] ?>" width="32" height="32">
        </span>
      </button>
      
      <div class="dropdown-menu dropdown-menu-right profile-dropdown" role="menu">
        <a href="#" class="dropdown-item"><span>Signed in as <br /><b><?= ucwords(strtolower($_SESSION['Name'])) ?></b></span></a>
        <?php if ($_SESSION['Role'] != 'Student') { ?>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">Your Profile</a>
          <a href="#" class="dropdown-item">Your Activity</a>
          <div class="dropdown-divider"></div>
          <a href="#" onclick="changePassword(<?= $_SESSION['ID'] ?>)" class="dropdown-item">Change Password</a>
          <a href="#" class="dropdown-item">Help</a>
        <?php } else { ?>
          <div class="dropdown-divider"></div>
        <?php } ?>
        <a href="/logout" class="dropdown-item">Logout</a>
      </div>
    </div>
    <!-- END User Info-->
  </div>
</div>
<!-- END HEADER -->