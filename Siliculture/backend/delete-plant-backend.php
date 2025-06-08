<?php
// Include necessary files
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role
include '../../database/connection.php';

// Function to verify and delete a record
function deleteRecord($id, $pdo) {
    try {
        // Check if ID is valid
        $id = intval($id);
        if ($id <= 0) {
            return ["status" => "error", "message" => "Invalid ID provided"];
        }
        
        // Prepare and execute delete statement
        $stmt = $pdo->prepare("DELETE FROM plant WHERE p_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Check if deletion was successful
        if ($stmt->rowCount() > 0) {
            return ["status" => "success", "message" => "Record deleted successfully "];
        } else {
            return ["status" => "error", "message" => "Record not found or already deleted "];
        }
    } catch (PDOException $e) {
        // Log the error
        error_log("Database error in delete-plant.php: " . $e->getMessage());
        return ["status" => "error", "message" => "Database error occurred"];
    }
}

// Handle the request - check if it's AJAX
header('Content-Type: application/json');

// Single delete
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $result = deleteRecord($id, $pdo);
    echo json_encode($result);
    exit();
}

// Bulk delete
if (isset($_POST['delete_ids']) && is_array($_POST['delete_ids'])) {
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($_POST['delete_ids'] as $id) {
        $result = deleteRecord($id, $pdo);
        if ($result["status"] == "success") {
            $successCount++;
        } else {
            $errorCount++;
        }
    }
    
    if ($errorCount == 0) {
        echo json_encode(["status" => "success", "message" => "$successCount records deleted successfully"]);
    } else {
        echo json_encode([
            "status" => "partial", 
            "message" => "$successCount records deleted successfully, $errorCount failed"
        ]);
    }
    exit();
}

// If we got here, no valid request was made
echo json_encode(["status" => "error", "message" => "Invalid request"]);
?>