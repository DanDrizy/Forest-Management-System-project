<?php
// delete-logs.php
header('Content-Type: application/json');

// Include the database connection file
require_once '../../database/connection.php';
require_once '../../logs/backend/auth_check.php';

// Check if the user is logged in and has the required role
checkUserAuth('sawmill');

try {
    // Get the record ID from the URL parameter
    $recordId = $_GET['id'] ?? null;
    
    // Validate the ID
    if (empty($recordId) || !is_numeric($recordId) || $recordId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid record ID provided']);
        exit;
    }
    
    // Convert to integer
    $recordId = (int)$recordId;
    
    // Check if the record exists before deleting
    $checkQuery = "SELECT t_id FROM timber WHERE t_id = :record_id";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':record_id', $recordId, PDO::PARAM_INT);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit;
    }
    
    // Prepare the delete query
    $deleteQuery = "DELETE FROM timber WHERE t_id = :record_id";
    $stmt = $pdo->prepare($deleteQuery);
    $stmt->bindParam(':record_id', $recordId, PDO::PARAM_INT);
    
    // Execute the query
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No record was deleted']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete record']);
    }

} catch (PDOException $e) {
    error_log("Database error in delete-logs.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error in delete-logs.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the record']);
}
?>