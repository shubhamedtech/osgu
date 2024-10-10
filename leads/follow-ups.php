<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php');
if ($_SESSION['crm'] == 0) {
  header('Location: /admissions/applications');
}
?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
$_SESSION['current_followup'] = 0;
?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">Follow-Ups</li>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="row pb-3">
          <div class="col-lg-12">
            <button class="btn btn-primary btn-rounded stage-button active" onclick="changeFollowUp(this.value)" value="0" id="followUpButton0">Today (<span id="followUpCount0">0</span>)</button>
            <button class="btn btn-primary btn-rounded stage-button" onclick="changeFollowUp(this.value)" value="1" id="followUpButton1">Tomorrow (<span id="followUpCount1">0</span>)</button>
            <button class="btn btn-primary btn-rounded stage-button" onclick="changeFollowUp(this.value)" value="2" id="followUpButton2">Planed (<span id="followUpCount2">0</span>)</button>
            <button class="btn btn-primary btn-rounded stage-button" onclick="changeFollowUp(this.value)" value="3" id="followUpButton3">Missed (<span id="followUpCount3">0</span>)</button>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="table-responsive">
              <table id="followups-table" class="table table-hover table-striped nowrap">
                <thead>
                  <tr>
                    <th data-orderable="false"></th>
                    <th data-orderable="false">Follow Up In</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Enquired For</th>
                    <th>Lead Status</th>
                    <th>Source</th>
                    <th>Date</th>
                    <th>Lead Owner</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      $(function() {
        var loggedInRole = '<?= $_SESSION['Role'] ?>';
        var showToRole = loggedInRole == 'Sub-Center' ? false : true;

        var table = $("#followups-table").DataTable({
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/follow-ups/server',
            complete: function(xhr, responseText) {
              // $('#allLeads').html(xhr.responseJSON.iTotalDisplayRecords);
              getLiveCount();
              $("#selectAllLeads").prop("checked", false);
              checkBox();
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = '<a class="dropdown-item" href="#" onclick="editLead(&#39;leads&#39;, &#39;' + data + '&#39, &#39;' + row.Universities + '&#39;, &#39;lg&#39;)"><i class="uil uil-edit"></i> Edit</a>';
                var refer = <?php print !in_array($_SESSION['Role'], ['Sub-Center']) ? true : false ?> ? '<a class="dropdown-item" href="#" onclick="refer(&#39;' + row.ID + '&#39;, &#39;' + row.Name + '&#39;)"><i class="uil uil-share-alt"></i> Refer</a>' : '';
                var send_email = '<a class="dropdown-item" href="#" onclick="send(&#39;email&#39;, &#39;' + row.ID + '&#39;, &#39;' + row.Name + '&#39;, &#39;' + row.Universities + '&#39;, &#39;lg&#39;)"><i class="uil uil-envelope-upload"></i> Send Email</a>';
                var send_sms = '<a class="dropdown-item" href="#" onclick="send(&#39;sms&#39;, &#39;' + row.ID + '&#39;, &#39;' + row.Name + '&#39;, &#39;' + row.Universities + '&#39;, &#39;lg&#39;)"><i class="uil uil-comment-alt-upload"></i> Send Message</a>';
                var send_whatsapp = '<i class="uil uil-whatsapp cursor-pointer" aria-expanded="false" title="Send WhatsApp to ' + row.Name + '" onclick="send(&#39;whatsapp&#39;, &#39;' + row.ID + '&#39;, &#39;' + row.Name + '&#39;, &#39;' + row.Universities + '&#39;, &#39;lg&#39;)"></i>&nbsp;&nbsp;';
                var followup = '<i class="uil uil-schedule cursor-pointer" aria-expanded="false" onclick="addFollowUp(&#39;' + row.ID + '&#39;, &#39;' + row.Name + '&#39;, &#39;' + row.Universities + '&#39;)" title="Add Follow-Up for ' + row.Name + '"></i>&nbsp;&nbsp;';
                var admission = row.Admission == 0 ? '<a href="/admissions/application-form?lead_id=' + row.ID + '" target="_blank"><i class="uil uil-file-plus-alt cursor-pointer" aria-expanded="false" title="Apply Form"></i></a>&nbsp;&nbsp;' : '';
                return '\
            ' + send_whatsapp + '\
            <a href="tel:' + row.Mobile + '"><i class="uil uil-outgoing-call cursor-pointer" aria-expanded="false" title="Make Call to ' + row.Name + '"></i></a>&nbsp;&nbsp;\
            <i class="uil uil-tag-alt cursor-pointer" aria-expanded="false" title="Tags for ' + row.Name + '"></i>&nbsp;&nbsp;\
            ' + followup + admission + '\
            <a href="lead-details?id=' + row.ID + '" target="_blank"><i class="uil uil-eye cursor-pointer" aria-expanded="false" title="View details of ' + row.Name + '"></i></a>&nbsp;&nbsp;\
            <i class="uil uil-ellipsis-v cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>\
            <div class="dropdown-menu" role="menu">\
              ' + send_email + send_sms + refer + edit + '\
            </div>';
              }
            },
            {
              data: "At",
              "render": function(data, type, row) {
                return '<dt class="fw-bold" id="timer-' + row.ID + '">' + data + '</dt>';
              }
            },
            {
              data: "Name",
              "render": function(data, type, row) {
                return '<strong class="text-uppercase">' + data + '</strong>';
              }
            },
            {
              data: "Email",
              "render": function(data, type, row) {
                var email = data.length > 0 ? '<a href="mailto:' + data + '"><dt class="fw-normal text-lowercase pb-1"><i class="uil uil-envelope-alt"></i> ' + data + '</dt></a>' : '';
                var alternate_email = row.Alternate_Email.length > 0 ? '<a href="mailto:' + row.Alternate_Email + '"><dt class="fw-normal text-lowercase"><i class="uil uil-envelope"></i> ' + row.Alternate_Email + '</dt></a>' : '';
                return email + alternate_email;
              }
            },
            {
              data: "Mobile",
              "render": function(data, type, row) {
                var email = data.length > 0 ? '<a href="tel:' + data + '"><dt class="fw-normal text-lowercase pb-1"><i class="uil uil-phone"></i> ' + data + '</dt></a>' : '';
                var alternate_email = row.Alternate_Mobile.length > 0 ? '<a href="tel:' + row.Alternate_Mobile + '"><dt class="fw-normal text-lowercase"><i class="uil uil-phone-alt"></i> ' + row.Alternate_Mobile + '</dt></a>' : '';
                return email + alternate_email;
              }
            },
            {
              data: "Universities",
              "render": function(data, type, row) {
                var university = '<dt class="fw-bold pb-1">' + data + '</dt>';
                var course = row.Courses.length > 0 ? '<dt class="fw-normal pb-1">' + row.Courses + '</dt>' : '';
                var sub_course = row.Sub_Courses.length > 0 ? '<dt class="fw-normal pb-1">' + row.Sub_Courses + '</dt>' : '';
                return university + course + sub_course;
              }
            },
            {
              data: "Stages",
              "render": function(data, type, row) {
                var stage = data.length > 0 ? '<dt class="text-center fw-bolder pb-1">' + data + '</dt>' : '';
                var reason = row.Reasons.length > 0 ? '<dt class="text-center pb-1">' + row.Reasons + '</dt>' : '';
                return stage + reason;
              }
            },
            {
              data: "Sources",
              "render": function(data, type, row) {
                var source = data.length > 0 ? '<dt class="text-center fw-bold pb-1">' + data + '</dt>' : '';
                var sub_source = row.Sub_Sources.length > 0 ? '<dt class="text-center fw-normal pb-1">' + row.Sub_Sources + '</dt>' : '';
                return source + sub_source;
              }
            },
            {
              data: "Created_At"
            },
            {
              data: "Users",
              visible: showToRole,
            },
          ],
          "sDom": "<f<t>ip>",
          "oLanguage": {
            "sEmptyTable": "No Follow-Up!"
          },
          "language": {
            "paginate": {
              "previous": "<i class='uil uil-angle-left'>",
              "next": "<i class='uil uil-angle-right'>"
            }
          },
          "createdRow": function(row, data, index) {
            countdownTimer(data.ID, data.At, data.Formated_Date);
          },
          "aaSorting": [],
          "drawCallback": function() {
            $('[data-toggle="tooltip"]').tooltip();
          }
        });
      })
    </script>

    <script>
      function getFollowUpCount(value) {
        $.ajax({
          url: '/app/follow-ups/followup_count?id=' + value,
          type: 'GET',
          success: function(data) {
            $('#followUpCount' + value).html(data);
          }
        })
      }

      $(function() {
        getFollowUpCount(0);
        getFollowUpCount(1);
        getFollowUpCount(2);
        getFollowUpCount(3);
      })
    </script>

    <script type="text/javascript">
      function changeFollowUp(value) {
        $(".stage-button").removeClass("active");
        $("#stageButton" + value).addClass("active");
        $('input[type=search]').val('');
        $.ajax({
          url: '/app/follow-ups/update_followup',
          type: 'post',
          data: {
            "value": value
          },
          success: function(data) {
            $('#followups-table').DataTable().ajax.reload(null, false);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function addFollowUp(id, name, university_id) {
        $.ajax({
          url: '/app/follow-ups/create',
          type: 'post',
          data: {
            "id": id,
            "name": name,
            "university_id": university_id
          },
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script>
      function editLead(url, id, university_id, modal) {
        $.ajax({
          url: '/app/' + url + '/edit',
          type: 'POST',
          data: {
            "id": id,
            "university_id": university_id
          },
          success: function(data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        })
      }
    </script>

    <script>
      function countdownTimer(id, value, formated_value) {
        var countDownDate = new Date(value).getTime();
        var x = setInterval(function() {
          var now = new Date().getTime();
          var distance = countDownDate - now;
          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
          document.getElementById("timer-" + id).innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("timer-" + id).innerHTML = '<span class="text-danger">Missed Follow-Up At<br>' + formated_value + '</span>';
          }
        }, 1000);
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
