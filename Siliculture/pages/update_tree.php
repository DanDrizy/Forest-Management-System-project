<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('Siliculture');

include '../../database/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $treeId = $_POST['p_id'] ?? null;
    $plantName = $_POST['plant_name'] ?? null;
    $dbh = $_POST['dbh'] ?? null;
    $height = $_POST['height'] ?? null;
    $health = $_POST['health'] ?? null;
    $recordDate = $_POST['record_date'] ?? null;

    // Validate required fields
    if (!$treeId || !$plantName || !$dbh || !$height || !$health || !$recordDate) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Validate numeric fields
    if (!is_numeric($dbh) || !is_numeric($height)) {
        echo json_encode(['success' => false, 'message' => 'DBH and Height must be numeric values']);
        exit;
    }

    // Validate date format
    $dateTime = DateTime::createFromFormat('Y-m-d', $recordDate);
    if (!$dateTime || $dateTime->format('Y-m-d') !== $recordDate) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit;
    }

    // Check if tree exists
    $checkStmt = $pdo->prepare("SELECT p_id FROM plant WHERE p_id = ?");
    $checkStmt->execute([$treeId]);
    
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Tree record not found']);
        exit;
    }

    // Update the plant record
    $updateStmt = $pdo->prepare("
        UPDATE plant SET
        DBH = ?, p_height = ?, health = ?, indate = ? 
        WHERE p_id = ?
    ");
    
    $result = $updateStmt->execute([
        
        floatval($dbh),
        floatval($height),
        $health,
        $recordDate,
        $treeId
    ]);

    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Tree updated successfully',
            'data' => [
                'plant_name' => $plantName,
                'dbh' => $dbh,
                'height' => $height,
                'health' => $health,
                'record_date' => $recordDate
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update tree record']);
    }

} catch (PDOException $e) {
    error_log('Database error in update_tree.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('General error in update_tree.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the tree']);
}
?>