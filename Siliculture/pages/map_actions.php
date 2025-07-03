<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('Siliculture');

include '../../database/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'save_pin':
                $tree_name = $_POST['tree_name'] ?? '';
                $latitude = $_POST['latitude'] ?? '';
                $longitude = $_POST['longitude'] ?? '';
                
                if (empty($tree_name) || empty($latitude) || empty($longitude)) {
                    throw new Exception('All fields are required');
                }
                
                // Check if the germination_pins table exists, if not create it
                $createTableQuery = "CREATE TABLE IF NOT EXISTS germination_pins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    tree_name VARCHAR(255) NOT NULL,
                    latitude DECIMAL(10, 7) NOT NULL,
                    longitude DECIMAL(10, 7) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                
                $pdo->exec($createTableQuery);
                
                // Insert the new pin
                $stmt = $pdo->prepare("INSERT INTO germination_pins (tree_name, latitude, longitude) VALUES (?, ?, ?)");
                $result = $stmt->execute([$tree_name, $latitude, $longitude]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Pin saved successfully']);
                } else {
                    throw new Exception('Failed to save pin');
                }
                break;
                
            case 'delete_pin':
                $pin_id = $_POST['pin_id'] ?? '';
                
                if (empty($pin_id)) {
                    throw new Exception('Pin ID is required');
                }
                
                $stmt = $pdo->prepare("DELETE FROM germination_pins WHERE id = ?");
                $result = $stmt->execute([$pin_id]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Pin deleted successfully']);
                } else {
                    throw new Exception('Failed to delete pin');
                }
                break;
                
            case 'get_pins':
                $stmt = $pdo->prepare("SELECT * FROM germination_pins ORDER BY created_at DESC");
                $stmt->execute();
                $pins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode(['success' => true, 'pins' => $pins]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}