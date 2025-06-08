<?php
require_once '../../database/connection.php';
require_once '../../logs/backend/auth_check.php';
checkUserAuth('pole plant');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate all required inputs
    $requiredFields = ['id', 'tree_name', 'record_date', 'po_amount', 'height', 'location'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => "Missing field: $field"
            ]);
            exit;
        }
    }

    // Sanitize and assign variables
    $id = (int)$_POST['id'];
    $tree_name = trim($_POST['tree_name']);
    $record_date = $_POST['record_date'];
    $po_amount = (int)$_POST['po_amount'];
    $height = trim($_POST['height']);
    $location = trim($_POST['location']);

    // Validate amount
    if ($po_amount <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Amount must be greater than zero'
        ]);
        exit;
    }

    try {
        // Prepare SQL to update all fields
        $stmt = $pdo->prepare("UPDATE pole SET 
            tree_name = :tree_name,
            record_date = :record_date,
            po_amount = :po_amount,
            height = :height,
            location = :location
            WHERE po_id = :id");

        $stmt->execute([
            ':tree_name' => $tree_name,
            ':record_date' => $record_date,
            ':po_amount' => $po_amount,
            ':height' => $height,
            ':location' => $location,
            ':id' => $id
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Record updated successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
