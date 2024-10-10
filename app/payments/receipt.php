<?php
if (isset($_GET['id'])) {
  session_start();
  require '../../includes/db-config.php';
  $id = intval($_GET['id']);
  $student = $conn->query("SELECT Payments.Amount,Payments.Payment_Mode,JSON_UNQUOTE(JSON_EXTRACT(Student_Ledgers.Fee,'$.Paid'))AS Invoiced_Amount,Payments.Transaction_ID,Payments.Gateway_ID,Payments.Type,Student_Ledgers.Duration,Students.First_Name,Students.Middle_Name,Students.Last_Name,(RIGHT(CONCAT('000000',Students.ID),6))AS Student_ID,Students.Unique_ID,Student_Ledgers.Created_At,Payments.Transaction_Date FROM Student_Ledgers LEFT JOIN Payments ON Student_Ledgers.Transaction_ID=Payments.Transaction_ID LEFT JOIN Students ON Student_Ledgers.Student_ID=Students.ID WHERE Student_Ledgers.ID= $id");
  $details = $student->fetch_assoc();
?>
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Receipt</h5>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-12 d-flex justify-content-end">
        <i class="uil uil-export cursor-pointer" onclick="exportAsPDF('exportToPDFPayment')"></i>
      </div>
    </div>
    <div class="row m-t-20" id="exportToPDFPayment">
      <div class="col-md-12">
        <table class="table-hover table-bordered table-sm" width="100%">
          <tbody>
            <tr>
              <td><b>Student</b></td>
              <td><?php
                  $studentName = implode(" ", array_filter(array($details['First_Name'], $details['Middle_Name'], $details['Last_Name'])));
                  $studentID = !empty($details['Unique_ID']) ? $details['Unique_ID'] : $details['Student_ID'];
                  echo $studentName . ' (' . $studentID . ')';
                  ?></td>
            </tr>
            <tr>
              <td><b>Payment Type</b></td>
              <td><?php echo $details['Type'] == 1 ? 'Offline' : 'Online' ?></td>
            </tr>
            <tr>
              <td><b>Transaction ID</b></td>
              <td><?= $details['Transaction_ID'] ?></td>
            </tr>
            <tr>
              <td><b>Gateway ID</b></td>
              <td><?= $details['Gateway_ID'] ?></td>
            </tr>
            <tr>
              <td><b>Transaction Date</b></td>
              <td><?php echo $details['Type'] == 1 ? date("d-m-Y", strtotime($details['Transaction_Date'])) : date("d-m-Y", strtotime($details['Created_At'])) ?></td>
            </tr>
            <tr>
              <td><b>Payment Mode</b></td>
              <td><?= $details['Payment_Mode'] ?></td>
            </tr>
            <tr>
              <td><b>Transaction Amount</b></td>
              <td><?= intval($details['Amount']) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer d-flex justify-content-end">
    <button aria-label="" type="button" onclick="approvePayment(<?= $id ?>)" class="btn btn-primary btn-cons btn-animated from-left">
      <span>Approved</span>
      <span class="hidden-block">
        <i class="pg-icon">tick</i>
      </span>
    </button>
  </div>

  <script type="text/javascript">
    function approvePayment(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Approve'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '/app/applications/approve-payment',
            type: 'POST',
            data: {
              id
            },
            dataType: 'json',
            success: function(data) {
              if (data.status) {
                $(".modal").modal("hide");
                notification('success', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              } else {
                notification('danger', data.message);
              }
            }
          })
        } else {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }
  </script>

  <script>
    function exportAsPDF(id) {
      window.jsPDF = window.jspdf.jsPDF
      var options = {};
      var elementHTML = document.querySelector("#" + id);
      var pdf = new jsPDF('l', 'mm', [130, 200]);
      pdf.html(elementHTML, {
        callback: function(doc) {
          doc.save('<?= $studentID ?>.pdf');
        },
        x: 5,
        y: 5,
        width: 190, //target width in the PDF document
        windowWidth: 475 //window width in CSS pixels
      });
    }
  </script>
<?php }
?>