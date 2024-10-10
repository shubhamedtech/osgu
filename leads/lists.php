<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php');
$_SESSION['current_stage'] = 0;
if ($_SESSION['crm'] == 0) {
  header('Location: /admissions/applications');
}
?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
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
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <li class="breadcrumb-item active">Leads</li>
              <div>
                <a href="/leads/generate" class="cursor-pointer" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Lead"> <i class="uil uil-plus-circle"></i></a>
              </div>
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
            <button class="btn btn-primary btn-rounded stage-button active" onclick="changeStage(this.value)" value="0" id="stageButton0">All Leads (<span id="allLeads">0</span>)</button>
            <?php if ($_SESSION['Role'] != 'Counsellor') { ?>
              <button class="btn btn-primary btn-rounded stage-button" onclick="changeStage(this.value)" value="-1" id="stageButton-1">Own Leads (<span id="ownLeads">0</span>)</button>
            <?php } ?>
            <?php $get_all_stages = $conn->query("SELECT ID, Name FROM Stages ORDER BY ID ASC");
            while ($stage = $get_all_stages->fetch_assoc()) { ?>
              <button class="btn btn-primary btn-rounded stage-button" onclick="changeStage(this.value)" value="<?= $stage['ID'] ?>" id="stageButton<?= $stage['ID'] ?>"><?= $stage['Name'] ?> (<span id="LeadsCount<?= $stage['ID'] ?>">0</span>)</button>
            <?php } ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="table-responsive">
              <table id="leads-table" class="table table-hover table-striped nowrap">
                <thead>
                  <tr>
                    <th data-orderable="false">
                      <div class="form-check primary m-0 p-0">
                        <input type="checkbox" class="form-check-input" id="selectAllLeads">
                        <label for="selectAllLeads"></label>
                      </div>
                    </th>
                    <th data-orderable="false"></th>
                    <th>Student ID</th>
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

    <script>
      function getLiveCount() {
        var search_value = $('#leads-table_filter input').val();
        <?php $get_all_stages = $conn->query("SELECT ID, Name FROM Stages ORDER BY ID ASC");
        while ($stage = $get_all_stages->fetch_assoc()) { ?>
          getStageCount('<?= $stage["ID"] ?>', '<?= $stage["Name"] ?>', search_value);
        <?php } ?>
        getAllCount();
        getOwnCount();
      }

      function getStageCount(value, name, search) {
        $.ajax({
          url: '/app/leads/stage_count?id=' + value + '&search=' + search,
          type: 'GET',
          success: function(data) {
            $('#LeadsCount' + value).html(data);
          }
        })
      }

      function getAllCount() {
        $.ajax({
          url: '/app/leads/stage_count',
          type: 'GET',
          success: function(data) {
            $('#allLeads').html(data);
          }
        })
      }

      function getOwnCount() {
        $.ajax({
          url: '/app/leads/stage_count?user=' + <?= $_SESSION['ID'] ?>,
          type: 'GET',
          success: function(data) {
            $('#ownLeads').html(data);
          }
        })
      }
    </script>

    <script type="text/javascript">
      $(function() {
        var loggedInRole = '<?= $_SESSION['Role'] ?>';
        var showToRole = loggedInRole == 'Sub-Center' ? false : true;
        var showDelete = '<?php print in_array($_SESSION['Role'], ['Administrator', 'University Head']) ? 1 : 0 ?>';
        var table = $("#leads-table").DataTable({
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/leads/server',
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
                return '<div class="form-check primary m-0 p-0"><input type="checkbox" name="lead_id" value="' + row.Universities + '|' + data + '" class="form-check-input table-checkbox" id="lead_id_' + data + '" onclick="checkBox()"><label for="lead_id_' + data + '"></label></div>';
              }
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                var destroy = showDelete == 1 ? '<a class="dropdown-item text-danger" href="#" onclick="destroy(&#39;leads&#39;, &#39;' + data + '&#39)"><i class="uil uil-trash"></i> Delete</a>' : '';
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
            ' + followup + '\
            ' + admission + '\
            <a href="lead-details?id=' + row.ID + '" target="_blank"><i class="uil uil-eye cursor-pointer" aria-expanded="false" title="View details of ' + row.Name + '"></i></a>&nbsp;&nbsp;\
            <i class="uil uil-ellipsis-v cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>\
            <div class="dropdown-menu" role="menu">\
              ' + send_email + send_sms + refer + edit + destroy + '\
            </div>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                if (data.length > 0) {
                  var edit = loggedInRole === 'Administrator' ? '<i class="uil uil-edit icon-xs cursor-pointer ml-2" onclick="updateStudentID(&#39;' + row.ID + '&#39)" title="Update Student ID" data-toggle="tooltip" data-placement="top"></i>' : '';
                  return '<strong class="text-uppercase">' + data + '</strong>';
                } else {
                  return '<i class="uil uil-edit icon-xs cursor-pointer" onclick="updateStudentID(&#39;' + row.ID + '&#39)" title="Update Student ID" data-toggle="tooltip" data-placement="top"></i>'
                }
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
            "sEmptyTable": "Please add Lead!"
          },
          "language": {
            "paginate": {
              "previous": "<i class='uil uil-angle-left'>",
              "next": "<i class='uil uil-angle-right'>"
            }
          },
          "aaSorting": [],
          "drawCallback": function() {
            $('[data-toggle="tooltip"]').tooltip();
          }
        });
      })
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

    <script type="text/javascript">
      function changeStage(stage) {
        $(".stage-button").removeClass("active");
        $("#stageButton" + stage).addClass("active");
        $('input[type=search]').val('');
        $.ajax({
          url: '/app/leads/update_stage',
          type: 'post',
          data: {
            "stage": stage
          },
          success: function(data) {
            $('#leads-table').DataTable().ajax.reload(null, false);
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

    <script type="text/javascript">
      function refer(id, name) {
        $.ajax({
          url: '/app/refer/create',
          type: 'post',
          data: {
            "id": id,
            "name": name
          },
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function multipleRefer() {
        var ids = [];
        $.each($("input[name='lead_id']:checked"), function() {
          ids.push($(this).val());
        });
        if (ids.length > 1) {
          $.ajax({
            url: '/app/refer/create_multiple',
            type: 'post',
            data: {
              "ids": ids
            },
            success: function(data) {
              $('#md-modal-content').html(data);
              $('#mdmodal').modal('show');
            }
          })
        } else {
          toastr.error("Please select lead!");
        }
      }
    </script>

    <!-- CheckBox -->
    <script type="text/javascript">
      $("#selectAllLeads").click(function() {
        $("input[name='lead_id']").not(this).prop('checked', this.checked);
        checkBox();
      });

      function checkBox() {
        var ids = [];
        var data_count = $('.form-check-input:checked').length;
        if (data_count > 1) {
          $("#referSelectedButton").css({
            display: "inline"
          });
          $("#deleteSelectedButton").css({
            display: "inline"
          });
        } else {
          $("#referSelectedButton").css({
            display: "none"
          });
          $("#deleteSelectedButton").css({
            display: "none"
          });
        }
        if ($(".table-checkbox").length == $(".table-checkbox:checked").length) {
          $("#selectAllLeads").prop("checked", true);
        } else {
          $("#selectAllLeads").prop("checked", false);
        }
      }
    </script>

    <!-- Filter -->
    <script type="text/javascript">
      function openFilter() {
        $.ajax({
          url: '/app/leads/filter',
          type: 'GET',
          success: function(data) {
            $('#xl-modal-content').html(data);
            $('#xlmodal').modal('show');
          }
        })
      }

      document.onkeyup = function(e) {
        var e = e || window.event; // for IE to cover IEs window object
        if (e.altKey && (e.which == 102 || e.which == 70)) {
          openFilter();
        }
      }
    </script>

    <script type="text/javascript">
      if (localStorage.getItem("lead_filter") != null) {
        $('.filter-button').removeClass('btn-primary');
        $('.filter-button').addClass('btn-success');
      }
    </script>

    <script type="text/javascript">
      function download() {
        var search_value = $('#leads-table_filter input').val();
        if (search_value) {
          window.open("//app/leads/export?search_value=" + search_value);
        } else {
          window.open("//app/leads/export");
        }
      }
    </script>

    <script type="text/javascript">
      function updateStudentID(id) {
        $.ajax({
          url: '/app/leads/student-id/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
