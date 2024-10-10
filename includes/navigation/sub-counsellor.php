<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI'])); ?>
    
    <!-- BEGIN SIDEBPANEL-->
    <nav class="page-sidebar" data-pages="sidebar">
      <!-- BEGIN SIDEBAR MENU HEADER-->
      <div class="sidebar-header">
        <?php if(!empty($light_logo)){ ?>
          <img src="<?=$light_logo?>" alt="logo" class="brand" data-src="<?=$light_logo?>" data-src-retina="<?=$light_logo_retina?>" width="60">
        <?php } ?>
        <div class="sidebar-header-controls">
          <button aria-label="Toggle Drawer" type="button" class="btn btn-icon-link invert sidebar-slide-toggle m-l-20 m-r-10" data-pages-toggle="#appMenu">
            <i class="pg-icon">chevron_down</i>
          </button>
          <button aria-label="Pin Menu" type="button" class="btn btn-icon-link invert d-lg-inline-block d-xlg-inline-block d-md-inline-block d-sm-none d-none" data-toggle-pin="sidebar">
            <i class="pg-icon"></i>
          </button>
        </div>
      </div>
      <!-- END SIDEBAR MENU HEADER-->
      <!-- START SIDEBAR MENU -->
      <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">

          <!-- Single Menu -->
          <li class="m-t-20 ">
            <a href="/dashboard" class="detailed">
              <span class="title">Dashboard</span>
              <span class="details">No New Updates</span>
            </a>
            <span class="icon-thumbnail-main"><i class="uil uil-home"></i></span>
          </li>

          <?php if($_SESSION['crm']!=0){ ?>
            <li class="<?php print $breadcrumbs[1]=='leads' ? 'open active' : '' ?>">
              <a href="javascript:;"><span class="title">Leads</span>
              <span class=" arrow <?php print $breadcrumbs[1]=='leads' ? 'open active' : '' ?>"></span></a>
              <span class="icon-thumbnail-main"><i class="uil uil-user"></i></span></span>
              <ul class="sub-menu">
                <li class="">
                  <a href="/leads/generate">Generate</a>
                  <span class="icon-thumbnail"><i class="pg-icon">Le</i></span>
                </li>
                <li class="">
                  <a href="/leads/lists">Leads</a>
                  <span class="icon-thumbnail"><i class="pg-icon">Le</i></span>
                </li>
                <li class="">
                  <a href="/leads/follow-ups">Follow-Ups</a>
                  <span class="icon-thumbnail"><i class="pg-icon">FU</i></span>
                </li>
              </ul>
            </li>
          <?php } ?>
          
          <li class="<?php print $breadcrumbs[1]=='admissions' ? 'open active' : '' ?>">
            <a href="javascript:;"><span class="title">Admissions</span>
            <span class=" arrow <?php print $breadcrumbs[1]=='admissions' ? 'open active' : '' ?>"></span></a>
            <span class="icon-thumbnail-main"><i class="uil uil-book-reader"></i></span></span>
            <ul class="sub-menu">
              <li class="">
                <a href="/admissions/application-form">Apply Fresh</a>
                <span class="icon-thumbnail"><i class="pg-icon">AF</i></span>
              </li>
              <li class="">
                <a href="/admissions/applications">Applications</a>
                <span class="icon-thumbnail"><i class="pg-icon">AP</i></span>
              </li>
              <li class="">
                <a href="/admissions/re-registrations">Re-Reg</a>
                <span class="icon-thumbnail"><i class="pg-icon">RR</i></span>
              </li>
              <li class="">
                <a href="/admissions/back-papers">Back-Paper</a>
                <span class="icon-thumbnail"><i class="pg-icon">BP</i></span>
              </li>
              <li class="">
                <a href="/admissions/results">Results</a>
                <span class="icon-thumbnail"><i class="pg-icon">RT</i></span>
              </li>
              <li class="">
                <a href="/admissions/exam-schedules">Exam Schedule</a>
                <span class="icon-thumbnail"><i class="pg-icon">ES</i></span>
              </li>
            </ul>
          </li>

          <li class="m-t-20 <?php print $breadcrumbs[1]=='users' ? 'open active' : '' ?>">
            <a href="javascript:;"><span class="title">Users</span>
            <span class=" arrow <?php print $breadcrumbs[1]=='users' ? 'open active' : '' ?>"></span></a>
            <span class="icon-thumbnail-main"><i class="uil uil-users-alt"></i></span></span>
            <ul class="sub-menu">
              <li class="">
                <a href="/users/centers">Centers</a>
                <span class="icon-thumbnail"><i class="pg-icon">Ce</i></span>
              </li>
            </ul>
          </li>

          <?php
            $pages = $conn->query("SELECT Pages.ID, Pages.Name, Pages.Slug FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID AND Page_Access.University_ID = ".$_SESSION['university_id']." WHERE Pages.`Type` = 'Accounts' AND Page_Access.Inhouse = 1");
            if($pages->num_rows>0){
          ?>
            <li class="<?php print $breadcrumbs[1]=='accounts' ? 'open active' : '' ?>">
              <a href="javascript:;"><span class="title">Accounts</span>
              <span class=" arrow <?php print $breadcrumbs[1]=='accounts' ? 'open active' : '' ?>"></span></a>
              <span class="icon-thumbnail-main"><i class="uil uil-bill"></i></span></span>
              <ul class="sub-menu">
                <?php while ($page = $pages->fetch_assoc()){?>
                  <li class="<?php print $breadcrumbs[2] == $page['Slug'] ? 'active' : '' ?>">
                    <a href="/accounts/<?=$page['Slug']?>"><?=$page['Name']?></a>
                    <span class="icon-thumbnail"><i class="pg-icon"><?=substr(str_replace(array('-', ' '),'',$page['Name']), 0, 2)?></i></span>
                  </li>
                <?php } ?>
              </ul>
            </li>
          <?php } ?>

          <?php
            $downloads = $conn->query("SELECT Pages.ID, Pages.Name, Pages.Slug FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID AND Page_Access.University_ID = ".$_SESSION['university_id']." WHERE Pages.`Type` = 'Download' AND Page_Access.Inhouse = 1");
            if($downloads->num_rows>0){
          ?>
            <li class="<?php print $breadcrumbs[1]=='downloads' ? 'open active' : '' ?>">
              <a href="javascript:;"><span class="title">Download Center</span>
              <span class=" arrow <?php print $breadcrumbs[1]=='downloads' ? 'open active' : '' ?>"></span></a>
              <span class="icon-thumbnail-main"><i class="uil uil-down-arrow"></i></span></span>
              <ul class="sub-menu">
                <?php while ($download = $downloads->fetch_assoc()){?>
                  <li class="">
                    <a href="/downloads/<?=$download['Slug']?>"><?=$download['Name']?></a>
                    <span class="icon-thumbnail"><i class="pg-icon"><?=substr(str_replace(array('-', ' '),'',$download['Name']), 0, 2)?></i></span>
                  </li>
                <?php } ?>
              </ul>
            </li>
          <?php } ?>

          <!--<li class="m-t-20">-->
          <!--  <a href="#" class="detailed">-->
          <!--    <span class="title">HR & Payroll</span>-->
          <!--    <span class="details">Coming Soon</span>-->
          <!--  </a>-->
          <!--  <span class="icon-thumbnail-main"><i class="uil uil-briefcase-alt"></i></span>-->
          <!--</li>-->

          <li class="m-t-0">
            <a href="#" class="detailed">
              <span class="title">Support</span>
              <span class="details">Coming Soon</span>
            </a>
            <span class="icon-thumbnail-main"><i class="uil uil-phone-alt"></i></span>
          </li>

        </ul>
        <div class="clearfix"></div>
      </div>
      <!-- END SIDEBAR MENU -->
    </nav>
    <!-- END SIDEBAR -->
    <!-- END SIDEBPANEL-->