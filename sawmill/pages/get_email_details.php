<?php
header('Content-Type: application/json');
require_once '../../database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Invalid email ID']);
    exit;
}

$emailId = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT r_id, title, subject, content, created_at, read_status FROM request WHERE r_id = ?");
    $stmt->execute([$emailId]);
    $email = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$email) {
        echo json_encode(['error' => 'Email not found']);
        exit;
    }
    
    // Format the date
    $date = new DateTime($email['created_at']);
    $email['formatted_date'] = $date->format('M j, Y g:i A');
    
    // Add sender info
    $email['sender'] = 'PolePlant';
    
    // Use content as message if available
    $email['message'] = $email['content'] ?: $email['subject'] ?: 'No content available';
    
    echo json_encode($email);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>