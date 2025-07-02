<?php
header('Content-Type: application/json');
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $logId = $_POST['logId'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $comments = $_POST['comments'] ?? null;
        $inDate = $_POST['inDate'] ?? null;
        
        // Validate required fields
        if (!$logId || !$amount || !$inDate) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        // Update the log entry (only updateable fields)
        $stmt = $pdo->prepare("UPDATE logs SET amount = :amount, coments = :comments, l_indate = :indate WHERE l_id = :logId");
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
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