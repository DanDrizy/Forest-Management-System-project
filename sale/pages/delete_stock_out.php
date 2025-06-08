<?php
// delete_stock_out.php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('sales'); // Check if the user is logged in and has the required role

include '../../database/connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete' && isset($_POST['record_id'])) {
        // Delete single record
        $record_id = intval($_POST['record_id']);
        
        if ($record_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid record ID']);
            exit;
        }
        
        // First, check if the record exists
        $checkStmt = $pdo->prepare("SELECT out_id FROM stockout WHERE out_id = ?");
        $checkStmt->execute([$record_id]);
        
        if (!$checkStmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Record not found']);
            exit;
        }
        
        // Delete the record
        $deleteStmt = $pdo->prepare("DELETE FROM stockout WHERE out_id = ?");
        $result = $deleteStmt->execute([$record_id]);
        
        if ($result) {
            // Log the deletion (optional - create audit trail)
            $logStmt = $pdo->prepare("INSERT INTO audit_log (action, table_name, record_id, user_id, timestamp) VALUES (?, ?, ?, ?, datetime('now'))");
            $logStmt->execute(['DELETE', 'stockout', $record_id, $_SESSION['user_id'] ?? 'unknown']);
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'Record deleted successfully',
                'deleted_id' => $record_id
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
        }
        
    } elseif ($action === 'delete_multiple' && isset($_POST['record_ids'])) {
        // Delete multiple records
        $record_ids = $_POST['record_ids'];
        
        if (!is_array($record_ids) || empty($record_ids)) {
            echo json_encode(['status' => 'error', 'message' => 'No records selected']);
            exit;
        }
        
        // Sanitize record IDs
        $record_ids = array_map('intval', $record_ids);
        $record_ids = array_filter($record_ids, function($id) { return $id > 0; });
        
        if (empty($record_ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid record IDs']);
            exit;
        }
        
        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($record_ids) - 1) . '?';
        
        // Check how many records exist
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM stockout WHERE out_id IN ($placeholders)");
        $checkStmt->execute($record_ids);
        $existingCount = $checkStmt->fetchColumn();
        
        if ($existingCount == 0) {
            echo json_encode(['status' => 'error', 'message' => 'No records found to delete']);
            exit;
        }
        
        // Delete the records
        $deleteStmt = $pdo->prepare("DELETE FROM stockout WHERE out_id IN ($placeholders)");
        $result = $deleteStmt->execute($record_ids);
        
        if ($result) {
            $deletedCount = $deleteStmt->rowCount();
            
            // Log the deletion for each record (optional)
            foreach ($record_ids as $record_id) {
                $logStmt = $pdo->prepare("INSERT INTO audit_log (action, table_name, record_id, user_id, timestamp) VALUES (?, ?, ?, ?, datetime('now'))");
                $logStmt->execute(['DELETE', 'stockout', $record_id, $_SESSION['user_id'] ?? 'unknown']);
            }
            
            echo json_encode([
                'status' => 'success', 
                'message' => "$deletedCount record(s) deleted successfully",
                'deleted_count' => $deletedCount,
                'deleted_ids' => $record_ids
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete records']);
        }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action or missing parameters']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in delete_stock_out.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred']);
    
} catch (Exception $e) {
    error_log("General error in delete_stock_out.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing the request']);
}
?>