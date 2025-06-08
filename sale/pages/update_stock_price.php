<?php
// Include authentication and database connection
require_once '../../logs/backend/auth_check.php';
require_once '../../database/connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// TEMPORARY: Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is authenticated and has proper role
try {
    checkUserAuth('sales');
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Authentication failed: ' . $e->getMessage()
    ]);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST requests are allowed'
    ]);
    exit;
}

// Validate required fields
if (!isset($_POST['record_id']) || !isset($_POST['sale_price'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: record_id and sale_price'
    ]);
    exit;
}

// Sanitize and validate input data
$record_id = filter_var($_POST['record_id'], FILTER_VALIDATE_INT);
$sale_price = filter_var($_POST['sale_price'], FILTER_VALIDATE_FLOAT);
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

// Validate record ID
if ($record_id === false || $record_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid record ID'
    ]);
    exit;
}

// Validate sale price
if ($sale_price === false || $sale_price < 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid sale price. Price must be a positive number.'
    ]);
    exit;
}

// Validate notes length
if (strlen($notes) > 1000) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Notes are too long. Maximum 1000 characters allowed.'
    ]);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Check if record exists
    $checkStmt = $pdo->prepare("SELECT out_id, out_price FROM stockout WHERE out_id = ?");
    $checkStmt->execute([$record_id]);
    $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$existingRecord) {
        $pdo->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Record not found'
        ]);
        exit;
    }
    
    $old_price = $existingRecord['out_price'];
    
    // Check what columns exist in stockout table (SQLite syntax)
    $columnsStmt = $pdo->prepare("PRAGMA table_info(stockout)");
    $columnsStmt->execute();
    $columnInfo = $columnsStmt->fetchAll(PDO::FETCH_ASSOC);
    $columns = array_column($columnInfo, 'name');
    
    // Build update query based on available columns
    if (in_array('up_notes', $columns) && in_array('updated_at', $columns)) {
        $updateStmt = $pdo->prepare("UPDATE stockout SET out_price = ?, up_notes = ?, updated_at = datetime('now') WHERE out_id = ?");
        $updateResult = $updateStmt->execute([$sale_price, $notes, $record_id]);
    } elseif (in_array('up_notes', $columns)) {
        $updateStmt = $pdo->prepare("UPDATE stockout SET out_price = ?, up_notes = ? WHERE out_id = ?");
        $updateResult = $updateStmt->execute([$sale_price, $notes, $record_id]);
    } else {
        // Only update price if notes column doesn't exist
        $updateStmt = $pdo->prepare("UPDATE stockout SET out_price = ? WHERE out_id = ?");
        $updateResult = $updateStmt->execute([$sale_price, $record_id]);
    }
    
    if ($updateResult && $updateStmt->rowCount() > 0) {
        // Skip audit log if it causes issues
        try {
            // Check if audit log table exists (SQLite syntax)
            $auditCheck = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='stock_audit_log'");
            $auditCheck->execute();
            
            if ($auditCheck->rowCount() > 0) {
                $user_id = $_SESSION['user_id'] ?? 1;
                $logStmt = $pdo->prepare("
                    INSERT INTO stock_audit_log (table_name, record_id, action, old_value, new_value, notes, user_id, created_at) 
                    VALUES ('stockout', ?, 'price_update', ?, ?, ?, ?, datetime('now'))
                ");
                $logStmt->execute([$record_id, $old_price, $sale_price, $notes, $user_id]);
            }
        } catch (PDOException $e) {
            // Log audit error but continue
            error_log("Audit log failed: " . $e->getMessage());
        }
        
        $pdo->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Stock price updated successfully',
            'data' => [
                'record_id' => $record_id,
                'old_price' => $old_price,
                'new_price' => $sale_price,
                'formatted_price' => number_format($sale_price) . ' Rwf',
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
        
    } else {
        $pdo->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'No changes were made. The record might not exist or the price is the same.'
        ]);
    }
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // TEMPORARY: Show actual error for debugging
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'debug_info' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode()
        ]
    ]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => 'General error: ' . $e->getMessage()
    ]);
}
?>