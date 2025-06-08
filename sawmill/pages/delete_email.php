<?php
header('Content-Type: application/json');
require_once '../../database/connection.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !is_numeric($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid email ID']);
    exit;
}

$emailId = (int)$input['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM request WHERE r_id = ?");
    $result = $stmt->execute([$emailId]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Email deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete email']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>