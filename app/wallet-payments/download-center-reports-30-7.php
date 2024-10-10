<?php 
include '../../includes/db-config.php';

use setasign\Fpdi\Fpdi;

require_once('../../extras/TCPDF/tcpdf.php');
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

session_start();

$searchqueryinwallet = '';
$searchqueryinwallet_invoice = '';

$start = $_REQUEST['start_date'];
$end = $_REQUEST['end_date'];

if( !empty($start) && !empty($end)){
    $start = date_format(date_create($start),'Y-m-d');
    $end = date_format(date_create($end),'Y-m-d');
    $searchqueryinwallet = "Wallets.Transaction_Date > '$start' AND Wallets.Transaction_Date < '$end'";
    $searchqueryinwallet_invoice = "Wallet_Payments.Transaction_Date > '$start' AND Wallet_Payments.Transaction_Date < '$end'";
}

$id = $_REQUEST['center'];

if (!empty($id)) {
    $id = intval($id);
    $searchqueryinwallet .= empty($searchqueryinwallet) ? " Wallets.Added_By = $id" : " And Wallets.Added_By = $id";
    $searchqueryinwallet_invoice .= empty($searchqueryinwallet_invoice) ? " Wallet_Invoices.User_ID = $id" : " And Wallet_Invoices.User_ID = $id"; 
}

$reports = $conn->query("(SELECT IF(Wallets.Type = '1','Offline','Online') as `Payment_type` , Wallets.Transaction_ID as `Transaction_ID` , Wallets.Gateway_ID as `Gateway_ID`, Wallets.Transaction_Date as `Transaction_Date`, CONCAT('+',Wallets.Amount) as `Amount`, Wallets.Payment_Mode as `Payment_Mode` , concat('-----') as 'Student', Users.Name as `Name` FROM `Wallets` LEFT JOIN Users ON Users.ID = Wallets.Added_By WHERE $searchqueryinwallet)
UNION
(SELECT IF(Wallet_Payments.Type = '3','Wallet','') as `Payment_type` , Wallet_Payments.Transaction_ID as `Transaction_ID`, Wallet_Payments.Gateway_ID as `Gateway_ID`, Wallet_Payments.Transaction_Date as `Transaction_Date` , CONCAT('- ',Wallet_Invoices.Amount) as `Amount`, Wallet_Payments.Payment_Mode as `Payment_Mode` , CONCAT(Students.First_Name,' ',Students.Middle_Name,' ',Students.Last_Name,' (',Students.Unique_ID,')')  as `Student`, Users.Name as `Name`  FROM `Wallet_Invoices` LEFT JOIN Users ON Users.ID = Wallet_Invoices.User_ID LEFT JOIN Students ON Students.id = Wallet_Invoices.Student_ID LEFT JOIN Wallet_Payments ON Wallet_Payments.Transaction_ID = Wallet_Invoices.Invoice_No WHERE $searchqueryinwallet_invoice)  
ORDER BY `Transaction_Date`");


$pdf = new Fpdi();
$pdf->AddPage();

$pdf->SetMargins(10, 10);  // Set left and top margins
$pdf->SetAutoPageBreak(true,0); 
$pdf->SetFillColor(200, 220, 255);

$pdf->SetFont("times", '', 20);

$pdf->SetXY(5,15);
$pdf->Cell(0, 0, 'Wallet Payment Reports', 0, 0, 'C', 0);

$pdf->SetFont("times", 'B', 12);

if (!empty($id)) {
    $user = $conn->query("SELECT CONCAT(Users.Name,'(',Users.Code,')') FROM Users WHERE Users.ID = $id");
    $user = $user->fetch_column();
    $pdf->SetXY(10,25);
    $pdf->Cell(130, 10, 'Center Name : '. $user , 0, 0, 'L', 0);
}

$pdf->SetFont('Arial', 'B',  10);
$cellWidth = 20;
$cellHeight = 10;
$pdf->SetXY(10, 40);
$pdf->MultiCell(12,10,'Sr.No','TLB','C');
$pdf->SetXY(22, 40);
$pdf->MultiCell(18, 5,'Payment Type', 'TLB','C');
$pdf->SetXY(40, 40);
$pdf->MultiCell(25, 5,'Transaction ID', 'TLB','C');
$pdf->SetXY(65, 40);
$pdf->MultiCell(27, 10, 'Gateway ID', 'TLB', 'C');
$pdf->SetXY(92,40);
$pdf->MultiCell(22,5,'Transaction Date','TLB','C');
$pdf->SetXY(114,40);
$pdf->MultiCell(43,10,'Student',1,'C');
$pdf->SetXY(157,40);
$pdf->MultiCell(20,5,'Payment Mode','TLB','C');
$pdf->SetXY(177,40);
$pdf->MultiCell(20,10,'Amount',1,'C');
$pdf->Ln();

$i = 1;
$Y_cordi = 50;
if ($reports->num_rows > 0) {
    while($row = $reports->fetch_assoc()) {
        if ($Y_cordi >= 280) {
            $pdf->AddPage();
            $Y_cordi = 10;
        }
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(10, $Y_cordi);
        $pdf->MultiCell(12,10,$i,'TLB','C',true);
        $pdf->SetXY(22, $Y_cordi);
        $pdf->MultiCell(18, 10, $row['Payment_type'], 'TLB','C');
        $pdf->SetXY(40, $Y_cordi);
        if(strlen(trim($row['Transaction_ID'])) > 15) {
            $pdf->MultiCell(25, 5, $row['Transaction_ID'] , 'TLB','C',true);
        } else {
            $pdf->MultiCell(25, 10,$row['Transaction_ID'], 'TLB','C',true);
        }
        $pdf->SetXY(65, $Y_cordi);
        if (strlen(trim($row['Gateway_ID'])) > 15) {
            $pdf->MultiCell(27, 5, $row['Gateway_ID'], 'TLB', 'C');
        } else {
            $pdf->MultiCell(27, 10, $row['Gateway_ID'], 'TLB', 'C');
        }
        $pdf->SetXY(92,$Y_cordi);
        $pdf->MultiCell(22,10,$row['Transaction_Date'],'TLB','C',true);
        $pdf->SetXY(114,$Y_cordi);
        if (strlen(trim($row['Student'])) > 25){
            $pdf->MultiCell(43,5,$row['Student'],'TLB','C');
        } else {
            $pdf->MultiCell(43,10,$row['Student'],'TLB','C');
        }
        $pdf->SetXY(157,$Y_cordi);
        if (strlen(trim($row['Payment_Mode'])) > 13) {
            $pdf->MultiCell(20,5,$row['Payment_Mode'],'TLB','C',true);
        } else {
            $pdf->MultiCell(20,10,$row['Payment_Mode'],'TLB','C',true);
        }
        $pdf->SetXY(177,$Y_cordi);
        if(preg_match('/^\+/',$row['Amount'])) {
            $pdf->SetTextColor(0,255,0);
        } else {
            $pdf->SetTextColor(255,0,0);
        }
        $pdf->SetFont('Arial','B',8);
        $pdf->MultiCell(20,10,$row['Amount'],1,'C');
        $pdf->SetTextColor(0,0,0);
        $pdf->Ln();
        $i++;
        $Y_cordi += 10; 
    }
} else {
    $pdf->SetXY(10, 50);
    $pdf->MultiCell(12,10,'1','TLB','C',true);
    $pdf->SetXY(22, 50);
    $pdf->MultiCell(18, 10, 'N/A', 'TLB','C');
    $pdf->SetXY(40, 50);
    $pdf->MultiCell(25, 10,'N/A', 'TLB','C',true);
    $pdf->SetXY(65, 50);
    $pdf->MultiCell(27, 10,'N/A', 'TLB', 'C');
    $pdf->SetXY(92,50);
    $pdf->MultiCell(22,10, 'N/A','TLB','C',true);
    $pdf->SetXY(114,50);
    $pdf->MultiCell(43,10,'---',1,'C');
    $pdf->SetXY(157,50);
    $pdf->MultiCell(20,10,'N/A','TLB','C',true);
    $pdf->SetXY(177,50);
    $pdf->MultiCell(20,10,'0.00',1,'C');
}
$pdf->Output('CenterReports.pdf', 'I');

?>