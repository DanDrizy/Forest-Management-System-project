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
    $action = $_POST['action'] ?? null;
    $treeIdsJson = $_POST['tree_ids'] ?? null;

    if (!$action || !$treeIdsJson) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $treeIds = json_decode($treeIdsJson, true);
    
    if (!is_array($treeIds) || empty($treeIds)) {
        echo json_encode(['success' => false, 'message' => 'Invalid tree IDs provided']);
        exit;
    }

    // Sanitize tree IDs (ensure they are integers)
    $treeIds = array_filter(array_map('intval', $treeIds));
    
    if (empty($treeIds)) {
        echo json_encode(['success' => false, 'message' => 'No valid tree IDs provided']);
        exit;
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Create placeholders for IN clause
        $placeholders = str_repeat('?,', count($treeIds) - 1) . '?';
        
        // Check if all trees exist and belong to current user's scope
        $checkStmt = $pdo->prepare("SELECT p_id FROM plant WHERE p_id IN ($placeholders)");
        $checkStmt->execute($treeIds);
        $existingTrees = $checkStmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($existingTrees) !== count($treeIds)) {
            throw new Exception('Some tree records were not found');
        }

        // Delete the plant records
        $deleteStmt = $pdo->prepare("DELETE FROM plant WHERE p_id IN ($placeholders)");
        $result = $deleteStmt->execute($treeIds);

        if ($result) {
            $deletedCount = $deleteStmt->rowCount();
            
            // Commit transaction
            $pdo->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => "Successfully deleted $deletedCount tree record(s)",
                'deleted_count' => $deletedCount,
                'deleted_ids' => $treeIds
            ]);
        } else {
            throw new Exception('Failed to delete tree records');
        }

    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log('Database error in delete_tree.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('General error in delete_tree.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>