<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('sales');

header('Content-Type: application/json');

try {
    include '../../database/connection.php';
    
    // Query to get all notes from stockout table where up_notes is not null or empty
    $query = "SELECT
plant_name,
 out_id,
 up_notes,
 updated_at,
 out_price,
 out_amount
              FROM stockout 
              JOIN stockin ON stockout.in_id = stockin.in_id
              JOIN timber ON stockin.t_id = timber.t_id
              JOIN logs ON timber.l_id = logs.l_id
              JOIN plant ON logs.p_id = plant.p_id
              JOIN germination ON plant.g_id = germination.g_id
			  WHERE up_notes IS NOT NULL 
              AND up_notes != '' 
              ORDER BY updated_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'notes' => $notes,
        'count' => count($notes)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>