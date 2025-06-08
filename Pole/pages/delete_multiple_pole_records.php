<?php
// delete_multiple_pole_records.php
// This file handles deleting multiple pole plant records

// Include database connection
require_once '../database/connection.php';
require_once '../logs/backend/auth_check.php';
checkUserAuth('pole plant'); // Check if user has permission

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    if (isset($_POST['ids']) && is_array($_POST['ids'])) {
        $ids = array_map('intval', $_POST['ids']);
        
        // Check if there are any IDs to delete
        if (count($ids) > 0) {
            try {
                // Prepare placeholders for SQL IN clause
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                
                // Delete the records from the database
                $stmt = $pdo->prepare("DELETE FROM poleplant_records WHERE id IN ($placeholders)");
                
                // Execute the statement with the IDs array
                if ($stmt->execute($ids)) {
                    $affectedRows = $stmt->rowCount();
                    echo json_encode([
                        'status' => 'success', 
                        'message' => "$affectedRows record(s) deleted successfully"
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete records']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No valid IDs provided']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameter: ids']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}