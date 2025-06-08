<?php
require_once '../../logs/backend/auth_check.php';
require_once '../../database/connection.php';

// Check if user is authenticated
checkUserAuth('sales');

// Set content type to JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get and validate input data
    $stock_id = isset($_POST['stock_id']) ? trim($_POST['stock_id']) : '';
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    
    // Validate required fields
    if (empty($stock_id) || empty($amount) || empty($price)) {
        throw new Exception('All fields are required');
    }
    
    // Validate numeric values
    if (!is_numeric($amount) || !is_numeric($price)) {
        throw new Exception('Amount and price must be numeric values');
    }
    
    // Validate positive values
    if ($amount <= 0 || $price < 0) {
        throw new Exception('Amount must be greater than 0 and price cannot be negative');
    }
    
    // Convert to appropriate types
    $amount = intval($amount);
    $price = floatval($price);
    
    // Check if stock item exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM stockin WHERE in_id = ?");
    $checkStmt->execute([$stock_id]);
    
    if ($checkStmt->fetchColumn() == 0) {
        throw new Exception('Stock item not found');
    }
    
    // Update the stock item
    $updateStmt = $pdo->prepare("UPDATE stockin SET s_amount = ?, price = ? WHERE in_id = ?");
    $result = $updateStmt->execute([$amount, $price, $stock_id]);


    
    
    if (!$result) {
        throw new Exception('Failed to update stock item');
    }
    
    // Check if any rows were affected
    if ($updateStmt->rowCount() == 0) {
        throw new Exception('No changes were made to the stock item');
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Stock item updated successfully',
        'data' => [
            'stock_id' => $stock_id,
            'amount' => $amount,
            'price' => $price,
            'total' => $amount * $price
        ]
    ]);
    
} catch (PDOException $e) {
    // Database error
    error_log('Database error in update_stock.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
    
} catch (Exception $e) {
    // General error
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>