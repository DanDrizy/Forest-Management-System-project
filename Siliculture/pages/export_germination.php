<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

include'../../database/connection.php';

try {
    // Fetch data from the germination table
    $select = $pdo->query("SELECT * FROM germination ORDER BY g_id");
    $data = $select->fetchAll(PDO::FETCH_ASSOC);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="germination_data_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write CSV headers
    $headers = ['No', 'Tree Name', 'Seeds', 'Germination Start', 'Germination End', 'Soil Type', 'Recorded Date'];
    fputcsv($output, $headers);
    
    // Write data rows
    $counter = 1;
    foreach ($data as $row) {
        $csvRow = [
            $counter++,
            $row['plant_name'],
            $row['seeds'],
            $row['g_sdate'],
            $row['g_edate'],
            $row['soil'],
            $row['curdate']
        ];
        fputcsv($output, $csvRow);
    }
    
    // Close the output stream
    fclose($output);
    
} catch (Exception $e) {
    // Handle errors
    header('Content-Type: text/html');
    echo "Error exporting data: " . $e->getMessage();
    echo '<br><a href="javascript:history.back()">Go back</a>';
}
exit;
?>