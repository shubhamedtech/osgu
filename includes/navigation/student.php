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
          <?php if(!empty($_SESSION['Enrollment_No'])){ ?>
            <li class="m-t-20 ">
              <a href="/dashboard">
                <span class="title">Dashboard</span>
              </a>
              <span class="icon-thumbnail-main"><i class="uil uil-home"></i></span>
            </li>

            <?php if(in_array('Profile', $_SESSION['LMS_Permissions'])){ ?>
              <li class="m-t-20 ">
                <a href="/student/profile">
                  <span class="title">My Profile</span>
                </a>
                <span class="icon-thumbnail-main"><i class="uil uil-user-circle"></i></span>
              </li>
            <?php } ?>
            
            <?php if(in_array('Notifications', $_SESSION['LMS_Permissions'])){ ?>
              <li class="">
                <a href="/student/notifications" class="detailed">
                  <span class="title">Notifications</span>
                  <span class="details">1 New Notification</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-megaphone"></i>
                </span>
              </li>
            <?php } ?>

            <?php if(in_array('Syllabus', $_SESSION['LMS_Permissions'])){ ?>
              <li class="">
                <a href="/student/syllabus">
                  <span class="title">My Syllabus</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-book-reader"></i>
                </span>
              </li>
            <?php } ?>

            <?php // if(in_array('ID Card', $_SESSION['LMS_Permissions'])){ ?>
              <li class="">
                <a href="/student/id-card">
                  <span class="title">ID Card</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-postcard"></i>
                </span>
              </li>
            <?php // } ?>


            <?php 
              if(
                in_array('E-Books', $_SESSION['LMS_Permissions']) ||
                in_array('Assignments', $_SESSION['LMS_Permissions']) ||
                in_array('Practicals', $_SESSION['LMS_Permissions']) ||
                in_array('Projects', $_SESSION['LMS_Permissions']) ||
                in_array('Work Books', $_SESSION['LMS_Permissions']) ||
                in_array('Videos', $_SESSION['LMS_Permissions'])
              ){
            ?>
              <li class="<?php print array_key_exists(2, $breadcrumbs) && $breadcrumbs[2]=='lms' ? 'open active' : '' ?>">
                <a href="javascript:;"><span class="title">LMS</span>
                <span class=" arrow <?php print $breadcrumbs[2]=='lms' ? 'open active' : '' ?>"></span></a>
                <span class="icon-thumbnail-main"><i class="uil uil-meeting-board"></i></span></span>
                <ul class="sub-menu">
                  <?php if(in_array('E-Books', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/lms/e-books">E-Books</a>
                      <span class="icon-thumbnail"><i class="pg-icon">EB</i></span>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Assignments', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/lms/assignments">Assignments</a>
                      <span class="icon-thumbnail"><i class="pg-icon">As</i></span>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Practicals', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/lms/practicals">Practicals</a>
                      <span class="icon-thumbnail"><i class="pg-icon">Pr</i></span>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Projects', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/lms/projects">Projects</a>
                      <span class="icon-thumbnail"><i class="pg-icon">Pj</i></span>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Work Books', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/lms/work-books">Work-Books</a>
                      <span class="icon-thumbnail"><i class="pg-icon">WB</i></span>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Videos', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/lms/videos">Videos</a>
                      <span class="icon-thumbnail"><i class="pg-icon">Vi</i></span>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>

            <?php 
              if(
                in_array('Date Sheets', $_SESSION['LMS_Permissions']) ||
                in_array('Admit Card', $_SESSION['LMS_Permissions']) ||
                in_array('Mock Tests', $_SESSION['LMS_Permissions']) ||
                in_array('Exams', $_SESSION['LMS_Permissions']) ||
                in_array('Results', $_SESSION['LMS_Permissions'])
              ){
            ?>
              <li class="<?php print array_key_exists(2, $breadcrumbs) && $breadcrumbs[2]=='examination' ? 'open active' : '' ?>">
                <a href="javascript:;"><span class="title">Examination</span>
                <span class=" arrow <?php print $breadcrumbs[2]=='examination' ? 'open active' : '' ?>"></span></a>
                <span class="icon-thumbnail-main"><i class="uil uil-file-edit-alt"></i></span></span>
                <ul class="sub-menu">
                  <?php if(in_array('Date Sheets', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/examination/date-sheets">Date Sheets</a>
                      <span class="icon-thumbnail"><i class="pg-icon">Ds</i></span>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Admit Card', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/examination/admit-card">Admit Card</a>
                      <span class="icon-thumbnail"><i class="pg-icon">AC</i></span>
                    </li>
                  <?php } ?>
                  <?php if(
                      in_array('Mock Tests', $_SESSION['LMS_Permissions']) ||
                      in_array('Exams', $_SESSION['LMS_Permissions'])
                    ){ 
                  ?>
                    <li class="<?php print array_key_exists(3, $breadcrumbs) && $breadcrumbs[3]=='online-exam' ? 'open active' : '' ?>">
                      <a href="javascript:;"><span class="title">Online Exam</span>
                      <span class="arrow <?php print array_key_exists(3, $breadcrumbs) && $breadcrumbs[3]=='online-exam' ? 'open active' : '' ?>"></span></a>
                      <span class="icon-thumbnail"><i class="pg-icon">OE</i></span>
                      <ul class="sub-menu">
                        <?php if(in_array('Mock Tests', $_SESSION['LMS_Permissions'])){ ?>
                          <li>
                            <a href="/student/examination/online-exam/mock-tests">Mock Test</a>
                            <span class="icon-thumbnail"><i class="pg-icon">Mt</i></span>
                          </li>
                        <?php } ?>
                        <?php if(in_array('Exams', $_SESSION['LMS_Permissions'])){ ?>
                          <li>
                            <a href="/student/examination/online-exam/exams">Exam</a>
                            <span class="icon-thumbnail"><i class="pg-icon">Ex</i></span>
                          </li>
                        <?php } ?>
                      </ul>
                    </li>
                  <?php } ?>
                  <?php if(in_array('Results', $_SESSION['LMS_Permissions'])){ ?>
                    <li class="">
                      <a href="/student/examination/results">Results</a>
                      <span class="icon-thumbnail"><i class="pg-icon">Re</i></span>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>

            <?php if(in_array('Queries & Feedback', $_SESSION['LMS_Permissions'])){ ?>
              <li class="">
                <a href="/student/queries-&-feedback">
                  <span class="title">Queries & Feedback</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-feedback"></i>
                </span>
              </li>
            <?php } ?>

            <?php if(in_array('Dispatch', $_SESSION['LMS_Permissions'])){ ?>
              <li class="">
                <a href="/student/dispatch">
                  <span class="title">Dispatch</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-truck-loading"></i>
                </span>
              </li>
            <?php } ?>
          <?php }else{ ?> 
            <li class="m-t-20 ">
              <a href="/dashboard">
                <span class="title">Home</span>
              </a>
              <span class="icon-thumbnail-main"><i class="uil uil-home"></i></span>
            </li>

            <?php if(in_array('Notifications', $_SESSION['LMS_Permissions'])){ ?>
              <li class="m-t-20">
                <a href="/student/notifications" class="detailed">
                  <span class="title">Notifications</span>
                  <span class="details">1 New Notification</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-megaphone"></i>
                </span>
              </li>
            <?php } ?>

            <?php if(in_array('Documents', $_SESSION['LMS_Permissions'])){ ?>
              <li class="m-t-20">
                <a href="/student/documents">
                  <span class="title">My Documents</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-file-lock-alt"></i>
                </span>
              </li>
            <?php } ?>

            <?php if(in_array('Application Form', $_SESSION['LMS_Permissions'])){ ?>
              <li class="">
                <a href="/student/admission-form">
                  <span class="title">Form</span>
                </a>
                <span class="icon-thumbnail-main">
                  <i class="uil uil-file-check-alt"></i>
                </span>
              </li>
            <?php } ?>

          <?php } ?>

          <?php if(in_array('Contact Us', $_SESSION['LMS_Permissions'])){ ?>
            <li class="">
              <a href="/student/contact-us">
                <span class="title">Contact Us</span>
              </a>
              <span class="icon-thumbnail-main">
                <i class="uil uil-phone"></i>
              </span>
            </li>
          <?php } ?>

        </ul>
        <div class="clearfix"></div>
      </div>
      <!-- END SIDEBAR MENU -->
    </nav>
    <!-- END SIDEBAR -->
    <!-- END SIDEBPANEL-->
