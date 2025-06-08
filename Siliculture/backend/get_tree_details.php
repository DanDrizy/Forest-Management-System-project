<?php
// File: get_tree_details.php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

include '../../database/connection.php';

// Set the content type header to JSON
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Tree ID is required']);
    exit;
}

$id = (int) $_GET['id'];

try {
    // Use prepared statement to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM plant WHERE p_id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $tree = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tree) {
        echo json_encode(['error' => 'Tree not found']);
        exit;
    }
    
    // Return tree details as JSON
    echo json_encode($tree);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>