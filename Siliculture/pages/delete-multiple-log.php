<?php
header('Content-Type: application/json');
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $logIds = $_POST['logIds'] ?? null;
        
        if (!$logIds || !is_array($logIds)) {
            echo json_encode(['success' => false, 'message' => 'Log IDs are required']);
            exit;
        }
        
        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($logIds) - 1) . '?';
        
        // Delete multiple log entries
        $stmt = $pdo->prepare("DELETE FROM logs WHERE l_id IN ($placeholders)");
        
        if ($stmt->execute($logIds)) {
            $deletedCount = $stmt->rowCount();
            if ($deletedCount > 0) {
                echo json_encode([
                    'success' => true, 
                    'message' => "$deletedCount log(s) deleted successfully",
                    'deletedCount' => $deletedCount
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No logs found to delete']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete logs']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>