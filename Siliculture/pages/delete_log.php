<?php
header('Content-Type: application/json');
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $logId = $_POST['logId'] ?? null;
        
        if (!$logId) {
            echo json_encode(['success' => false, 'message' => 'Log ID is required']);
            exit;
        }
        
        // Delete the log entry
        $stmt = $pdo->prepare("DELETE FROM logs WHERE l_id = :logId");
        $stmt->bindParam(':logId', $logId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Log deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Log not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete log']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>