<?php
header('Content-Type: application/json');
require_once '../../database/connection.php';

$now = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$reply_to_id = $_POST['reply_to_id'] ?? '';
$reply_message = $_POST['reply_message'] ?? '';

if (empty($reply_to_id) || empty($reply_message)) {
    echo json_encode(['success' => false, 'message' => 'Reply message is required']);
    exit;
}

try {
    // Get the original message details
    $stmt = $pdo->prepare("SELECT title, subject FROM request WHERE r_id = ?");
    $stmt->execute([$reply_to_id]);
    $original = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$original) {
        echo json_encode(['success' => false, 'message' => 'Original message not found']);
        exit;
    }
    
    // Create reply with "Re:" prefix
    $reply_subject = 'Re: ' . ($original['title'] ?: $original['subject']);
    
    $stmt = $pdo->prepare("INSERT INTO received (req_id, title, subject, content, created_at, read_status) VALUES (?, ?, ?, ?, ?, 0)");
    $result = $stmt->execute([$reply_to_id, $reply_subject, $reply_subject, $reply_message, $now]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send reply']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>