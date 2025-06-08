<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

include'../../database/connection.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update') {
            // Handle Update
            $id = $_POST['id'] ?? '';
            $plant_name = $_POST['plant_name'] ?? '';
            $seeds = $_POST['seeds'] ?? '';
            $g_sdate = $_POST['g_sdate'] ?? '';
            $g_edate = $_POST['g_edate'] ?? '';
            $soil = $_POST['soil'] ?? '';
            
            // Validate required fields
            if (empty($id) || empty($plant_name) || empty($seeds) || empty($g_sdate) || empty($g_edate) || empty($soil)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }
            
            // Validate dates
            if (strtotime($g_sdate) > strtotime($g_edate)) {
                echo json_encode(['success' => false, 'message' => 'Germination start date cannot be later than end date']);
                exit;
            }
            
            // Update the record using g_id
            $stmt = $pdo->prepare("UPDATE germination SET plant_name = ?, seeds = ?, g_sdate = ?, g_edate = ?, soil = ? WHERE g_id = ?");
            $result = $stmt->execute([$plant_name, $seeds, $g_sdate, $g_edate, $soil, $id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update record']);
            }
            
        } elseif ($action === 'delete') {
            // Handle Delete
            $id = $_POST['g_id'] ?? $_POST['id'] ?? '';
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Record ID is required']);
                exit;
            }
            
            // Check if record exists
            $checkStmt = $pdo->prepare("SELECT g_id FROM germination WHERE g_id = ? AND del = 0");
            $checkStmt->execute([$id]);
            $fetch = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $fetch_id = $fetch['g_id'] ?? '';
            // If no record found, return an error
            
            if ($fetch_id == '') {
                echo json_encode(['success' => false, 'message' => 'Record not found']);
                exit;
            }
            
            // Soft delete the record (set del = 1)
            $stmt = $pdo->prepare("UPDATE germination SET del = 1 WHERE g_id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete record']);
            }
            
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>