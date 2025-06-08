<?php
// delete-logs-all.php
header('Content-Type: application/json');

// Include the database connection file
require_once '../../database/connection.php';
require_once '../../logs/backend/auth_check.php';

// Check if the user is logged in and has the required role
checkUserAuth('sawmill');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle AJAX request for selected records
        $ids = json_decode($_POST['ids'] ?? '[]', true);
        
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'No records selected for deletion']);
            exit;
        }

        // Sanitize the IDs
        $ids = array_filter($ids, function($id) {
            return is_numeric($id) && $id > 0;
        });

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'Invalid record IDs provided']);
            exit;
        }

        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        // Prepare the delete query
        $deleteQuery = "DELETE FROM timber WHERE t_id IN ($placeholders)";
        $stmt = $pdo->prepare($deleteQuery);
        
        // Execute the query with the IDs
        if ($stmt->execute($ids)) {
            $deletedCount = $stmt->rowCount();
            echo json_encode([
                'success' => true, 
                'message' => "$deletedCount record(s) deleted successfully"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete records']);
        }
        
    } else {
        // Handle traditional GET request (delete all records)
        $deleteQuery = "DELETE FROM timber WHERE t_amount > 0";
        $stmt = $pdo->prepare($deleteQuery);
        
        if ($stmt->execute()) {
            $deletedCount = $stmt->rowCount();
            echo json_encode([
                'success' => true, 
                'message' => "All $deletedCount record(s) deleted successfully"
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete all records']);
        }
    }

} catch (PDOException $e) {
    error_log("Database error in delete-logs-all.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error in delete-logs-all.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting records']);
}
?>