<?php
header('Content-Type: application/json');
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $logId = $_POST['logId'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $measure = $_POST['measure'] ?? null;
        $inDate = $_POST['inDate'] ?? null;
        
        // Validate required fields
        if (!$logId || !$amount || !$measure || !$inDate) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        // Update the log entry (only updateable fields)
        $stmt = $pdo->prepare("UPDATE logs SET amount = :amount, v1 = :measure, l_indate = :indate WHERE l_id = :logId");
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':measure', $measure, PDO::PARAM_STR);
        $stmt->bindParam(':indate', $inDate, PDO::PARAM_STR);
        $stmt->bindParam(':logId', $logId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Log updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update log']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>