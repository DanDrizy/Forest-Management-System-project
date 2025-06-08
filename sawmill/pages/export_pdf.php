<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('sawmill');

// You can use TCPDF or similar library for PDF generation
// Install via: composer require tecnickcom/tcpdf

require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php'); // Adjust path as needed

include '../../database/connection.php';

// Get date parameters
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

// Build the same query as in the main file
$query = "SELECT 
    g.*,
    p.*,
    t.*,
    l.*,
    l.height AS l_height,
    p.health AS l_health,
    l.amount AS l_amount,
    l_indate,
    l_status 
FROM germination g 
JOIN plant p ON p.g_id = g.g_id 
JOIN logs l ON l.p_id = p.p_id 
JOIN timber t ON t.l_id = l.l_id 
WHERE t.t_amount > 0 
AND (t.status = 'unsend' OR t.status = 'send')";

if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND DATE(t.t_indate) BETWEEN :start_date AND :end_date";
} elseif (!empty($start_date)) {
    $query .= " AND DATE(t.t_indate) >= :start_date";
} elseif (!empty($end_date)) {
    $query .= " AND DATE(t.t_indate) <= :end_date";
}

$query .= " ORDER BY t.t_indate DESC";

$select_report = $pdo->prepare($query);

if (!empty($start_date)) {
    $select_report->bindParam(':start_date', $start_date);
}
if (!empty($end_date)) {
    $select_report->bindParam(':end_date', $end_date);
}

$select_report->execute();
$fetch = $select_report->fetchAll();

// Create PDF


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sawmill System');
$pdf->SetTitle('Sawmill Report');
$pdf->SetSubject('Sawmill Report');

// Set default header data
$pdf->SetHeaderData('', 0, 'Sawmill Report', 'Generated on ' . date('Y-m-d H:i:s'));

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Date range info
$dateRangeText = 'All Records';
if (!empty($start_date) && !empty($end_date)) {
    $dateRangeText = 'Date Range: ' . date('M d, Y', strtotime($start_date)) . ' - ' . date('M d, Y', strtotime($end_date));
} elseif (!empty($start_date)) {
    $dateRangeText = 'From: ' . date('M d, Y', strtotime($start_date));
} elseif (!empty($end_date)) {
    $dateRangeText = 'Until: ' . date('M d, Y', strtotime($end_date));
}

$pdf->Cell(0, 10, $dateRangeText, 0, 1, 'C');
$pdf->Ln(5);

// Create table
$html = '<table border="1" cellpadding="4">
    <thead>
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <th width="5%">ID</th>
            <th width="15%">Name</th>
            <th width="10%">Type</th>
            <th width="15%">HxW Size</th>
            <th width="8%">Amount</th>
            <th width="15%">Location</th>
            <th width="17%">Inserted</th>
        </tr>
    </thead>
    <tbody>';

$i = 1;
foreach ($fetch as $item) {
    $html .= '<tr>
        <td>' . $i . '</td>
        <td>' . htmlspecialchars($item['plant_name']) . '</td>
        <td>' . htmlspecialchars($item['type']) . '</td>
        <td>' . htmlspecialchars($item['t_height']) . 'x' . htmlspecialchars($item['t_width']) . ' : ' . htmlspecialchars($item['size']) . '</td>
        <td>' . htmlspecialchars($item['t_amount']) . '</td>
        <td>' . htmlspecialchars($item['t_location']) . '</td>
        <td>' . date('M d, Y H:i', strtotime($item['t_indate'])) . '</td>
    </tr>';
    $i++;
}

$html .= '</tbody></table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$filename = 'sawmill_report_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D'); // 'D' for download

?>