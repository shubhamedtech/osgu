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
    $start = date_format(date_create($start),'Y-m-d 00:00:00');
    $end = date_format(date_create($end),'Y-m-d 23:59:59');
    $searchqueryinwallet = "Wallets.Updated_At BETWEEN '$start' AND '$end'";
    $searchqueryinwallet_invoice = "Wallet_Invoices.Created_At BETWEEN '$start' AND '$end'";
}

$id = $_REQUEST['center'];
$center_ids = explode(',',$id);

if (!empty($id)) {
    $searchqueryinwallet .= empty($searchqueryinwallet) ? " Wallets.Added_By IN ($id)" : " And Wallets.Added_By IN ($id)";
    $searchqueryinwallet_invoice .= empty($searchqueryinwallet_invoice) ? " Wallet_Invoices.User_ID IN ($id)" : " And Wallet_Invoices.User_ID IN ($id)"; 
}

$reports = $conn->query("(SELECT IF(Wallets.Type = '1','Offline','Online') as `Payment_type`, Wallets.Transaction_ID as `Transaction_ID` , Wallets.Gateway_ID as `Gateway_ID`, Wallets.Updated_At as `Transaction_Date`, CONCAT('+',Wallets.Amount) as `Amount`, Wallets.Payment_Mode as `Payment_Mode` , concat('-----') as 'Student', Wallets.Added_By as `user_id` FROM `Wallets` LEFT JOIN Users ON Users.ID = Wallets.Added_By WHERE $searchqueryinwallet AND Wallets.Status= 1) 
UNION 
(SELECT IF(Wallet_Payments.Type = '3','Wallet','') as `Payment_type` , Wallet_Payments.Transaction_ID as `Transaction_ID`, Wallet_Payments.Gateway_ID as `Gateway_ID`, Wallet_Invoices.Created_At as `Transaction_Date`, CONCAT('- ',Wallet_Invoices.Amount) as `Amount`, Wallet_Payments.Payment_Mode as `Payment_Mode` , CONCAT(TRIM(Students.First_Name),' ',TRIM(Students.Middle_Name),' ',TRIM(Students.Last_Name),' (',Students.Unique_ID,')') as `Student`, Wallet_Invoices.User_ID as `user_id` FROM `Wallet_Invoices` LEFT JOIN Students ON Students.id = Wallet_Invoices.Student_ID LEFT JOIN Wallet_Payments ON Wallet_Payments.Transaction_ID = Wallet_Invoices.Invoice_No WHERE $searchqueryinwallet_invoice and Wallet_Payments.Type = 3) ORDER by `Transaction_Date`");


$centers_data = [];
foreach ($center_ids as $value) {
    $centers_data[$value] = [];
}


while($row = $reports->fetch_assoc()) {
    if (array_key_exists($row['user_id'],$centers_data)) {
        $centers_data[$row['user_id']][] = $row ;
    }
}

$pdf = new Fpdi();
foreach($centers_data as $key=>$value) {
    createWalletReports($key,$value);
}

function createWalletReports($id,$wallet_records) {
    global $start , $end , $conn , $pdf;
    $pdf->AddPage('h');

    $pdf->SetMargins(10, 10);  // Set left and top margins
    $pdf->SetAutoPageBreak(true,0); 
    $pdf->SetFillColor(200, 220, 255);

    $pdf->SetFont("times", '', 20);

    $pdf->SetXY(5,13);
    $pdf->Cell(0, 0, 'Wallet Payment Reports', 0, 0, 'C', 0);

    $pdf->SetFont("times", 'B', 12);

    if (!empty($id)) {
        $user = $conn->query("SELECT CONCAT(Users.Name,'(',Users.Code,')') FROM Users WHERE Users.ID = $id");
        $user = $user->fetch_column();
        $pdf->SetXY(10,22);
        $pdf->Cell(130, 10, 'Center Name : '. $user , 0, 0, 'L', 0);
    }

    if (!empty($start) && !empty($end)) {
        $pdf->SetXY(10,28);
        $pdf->Cell(130, 10, 'Report Duration : '. DATE_FORMAT(date_create($start), 'd-M-Y') . ' to ' . DATE_FORMAT(date_create($end), 'd-M-Y') , 0, 0, 'L', 0);
    }

    $cell_height = 12;

    $pdf->SetFont('Arial', 'B',  10);
    $pdf->SetXY(10, 40);
    $pdf->MultiCell(12,$cell_height,'Sr.No','TLB','C');
    $pdf->SetXY(22, 40);
    $pdf->MultiCell(23, $cell_height-6,'Payment Type', 'TLB','C');
    $pdf->SetXY(45, 40);
    $pdf->MultiCell(28, $cell_height,'Transaction ID', 'TLB','C');
    $pdf->SetXY(73, 40);
    $pdf->MultiCell(40, $cell_height, 'Gateway ID', 'TLB', 'C');
    $pdf->SetXY(113,40);
    $pdf->MultiCell(22,6,'Transaction Date','TLB','C');
    $pdf->SetXY(135,40);
    $pdf->MultiCell(75,$cell_height,'Student',1,'C');
    $pdf->SetXY(210,40);
    $pdf->MultiCell(25,$cell_height-6,'Payment Mode','TLB','C');
    $pdf->SetXY(235,40);
    $pdf->MultiCell(25,$cell_height-6,'Transection Amount',1,'C');
    $pdf->SetXY(260,40);
    $pdf->MultiCell(25,$cell_height-6,'Available Amount',1,'C');
    $pdf->Ln();

    $wallet_whr = ''; $walletpaymet_whr = '';
    if (!empty($start)) {
        $wallet_whr = 'AND Updated_At < "$start"';
        $walletpaymet_whr = 'AND Created_At < "$start"';
    }

    //Total Amount
    $credit_amts = 0;
    $credit_amts = $conn->query('SELECT sum(Amount) as `total_amt` FROM Wallets WHERE Added_By = "$id" '.$wallet_whr.' AND Status = 1');
    if( $credit_amts->num_rows > 0) {
        $credit_amts = $credit_amts->fetch_assoc();    
    }

    //Debit Amount
    $debit_amts = 0;
    $debit_amts = $conn->query('SELECT sum(Amount) as `debit_amt` FROM Wallet_Payments WHERE Added_By = "$id" '.$walletpaymet_whr.' AND Type = 3');
    if( $debit_amts->num_rows > 0) {
        $debit_amts = $debit_amts->fetch_assoc();
    }

    $balance_till_date =  floatval($credit_amts) - floatval($debit_amts);

    $i = 1; 
    $Y_cordi = 52;
    if (!empty($wallet_records)) {
        foreach ($wallet_records as $row){
            if ($Y_cordi >= 196) {
                $pdf->AddPage('h');
                $Y_cordi = 10;
            }
            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(10, $Y_cordi);
            $pdf->MultiCell(12,$cell_height,$i,'TLB','C',true);
            $pdf->SetXY(22, $Y_cordi);
            $pdf->MultiCell(23, $cell_height, $row['Payment_type'], 'TLB','C');
            $pdf->SetXY(45, $Y_cordi);
            $pdf->MultiCell(28, $cell_height,$row['Transaction_ID'], 'TLB','C',true);
            $pdf->SetXY(73, $Y_cordi);
            $pdf->MultiCell(40, $cell_height, $row['Gateway_ID'], 'TLB', 'C');
            $pdf->SetXY(113,$Y_cordi);
            $pdf->MultiCell(22,$cell_height-6,$row['Transaction_Date'],'TLB','C',true);
            $pdf->SetXY(135,$Y_cordi);
            if(preg_match('/\(.*?\)/',$row['Student'])) {
                list($student_name,$unique_id) = explode("(",trim($row['Student']));
                $pdf->MultiCell(75,$cell_height-6,$student_name,'TL','C');
                $pdf->SetXY(135,$Y_cordi+6);
                $pdf->MultiCell(75,$cell_height-6,"(". $unique_id,'LB','C');
            } else {
                $pdf->MultiCell(75,$cell_height,trim($row['Student']),'TLB','C');
            }
            $pdf->SetXY(210,$Y_cordi);
            if (strlen($row['Payment_Mode']) > 18 ) {
                $pdf->MultiCell(25,$cell_height-6,$row['Payment_Mode'],'TLB','C',true);
            } else {
                $pdf->MultiCell(25,$cell_height,$row['Payment_Mode'],'TLB','C',true);
            }
            $pdf->SetXY(235,$Y_cordi);
            if(preg_match('/^\+/',$row['Amount'])) {
                $balance_till_date += floatval(substr($row['Amount'],1));
                $pdf->SetTextColor(0,128,0);
            } else {
                $balance_till_date -= floatval(substr($row['Amount'],2));
                $pdf->SetTextColor(255,0,0);
            }
            $pdf->SetFont('Arial','B',8);
            $pdf->MultiCell(25,$cell_height,$row['Amount'],1,'C');
            $pdf->SetTextColor(0,0,0);
            $pdf->SetXY(260,$Y_cordi);
            $pdf->MultiCell(25,$cell_height,$balance_till_date,1,'C');

            $pdf->Ln();
            $i++;
            $Y_cordi += $cell_height; 
        }
        $pdf->SetFont('Arial','B',10);
        $pdf->SetTextColor(0,0,0);
    } else {
        $pdf->SetXY(10, $Y_cordi);
        $pdf->MultiCell(12,$cell_height,'1','TLB','C',true);
        $pdf->SetXY(22, $Y_cordi);
        $pdf->MultiCell(23, $cell_height, 'N/A', 'TLB','C');
        $pdf->SetXY(45, $Y_cordi);
        $pdf->MultiCell(28, $cell_height,'N/A', 'TLB','C',true);
        $pdf->SetXY(73, $Y_cordi);
        $pdf->MultiCell(40, $cell_height,'N/A', 'TLB', 'C');
        $pdf->SetXY(113,$Y_cordi);
        $pdf->MultiCell(22,$cell_height, 'N/A','TLB','C',true);
        $pdf->SetXY(135,$Y_cordi);
        $pdf->MultiCell(75,$cell_height,'---',1,'C');
        $pdf->SetXY(210,$Y_cordi);
        $pdf->MultiCell(25,$cell_height,'N/A','TLB','C',true);
        $pdf->SetXY(235,$Y_cordi);
        $pdf->MultiCell(25,$cell_height,'0.00','TLB','C');
        $pdf->SetXY(260,$Y_cordi);
        $pdf->MultiCell(25,$cell_height,$balance_till_date,1,'C');
    }
}

$pdf->Output('CenterReports.pdf', 'I');

?>