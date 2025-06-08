<?php
// delete_pole_record.php
// This file handles deleting a single pole plant record

// Include database connection
require_once '../database/connection.php';
require_once '../logs/backend/auth_check.php';
checkUserAuth('pole plant'); // Check if user has permission

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        try {
            // Delete the record from the database
            $stmt = $pdo->prepare("DELETE FROM poleplant_records WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete record']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameter: id']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}