<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('sales'); // Check if the user is logged in and has the required role

header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get the stock ID from POST data
$stock_id = isset($_POST['stock_id']) ? intval($_POST['stock_id']) : 0;

if ($stock_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid stock ID']);
    exit;
}

try {
    // Include database connection
    include '../../database/connection.php';
    
    // Prepare and execute delete statement
    $delete = $pdo->prepare("DELETE FROM stockin WHERE in_id = :stock_id");
    $delete->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
    
    if ($delete->execute()) {
        // Check if any row was actually deleted
        if ($delete->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Stock item deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Stock item not found or already deleted'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to delete stock item from database'
        ]);
    }
    
} catch (PDOException $e) {
    // Log the error (in production, don't expose database errors to users)
    error_log("Delete stock error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred while deleting stock item'
    ]);
} catch (Exception $e) {
    // Log the error
    error_log("General delete error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'An unexpected error occurred'
    ]);
}
?>