<?php
// update-timber.php
header('Content-Type: application/json');

// Include the database connection file
require_once '../../database/connection.php';
require_once '../../logs/backend/auth_check.php';

// Check if the user is logged in and has the required role
checkUserAuth('sawmill');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get the form data
    $recordId = $_POST['record_id'] ?? null;
    $type = trim($_POST['type'] ?? '');
    $amount = $_POST['amount'] ?? 0;
    $height = $_POST['height'] ?? 0;
    $width = $_POST['width'] ?? 0;
    $size = $_POST['size'] ?? 0;
    $volume = $_POST['volume'] ?? 0;
    $location = trim($_POST['location'] ?? '');

    // Validate required fields
    if (empty($recordId) || empty($type) || empty($location)) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
        exit;
    }

    // Validate numeric fields
    if (!is_numeric($amount) || !is_numeric($height) || !is_numeric($width) || 
        !is_numeric($size) || !is_numeric($volume)) {
        echo json_encode(['success' => false, 'message' => 'Numeric fields must contain valid numbers']);
        exit;
    }

    // Convert to appropriate types
    $amount = (int)$amount;
    $height = (float)$height;
    $width = (float)$width;
    $size = (float)$size;
    $volume = (float)$volume;

    // Validate that values are not negative
    if ($amount < 0 || $height < 0 || $width < 0 || $size < 0 || $volume < 0) {
        echo json_encode(['success' => false, 'message' => 'Values cannot be negative']);
        exit;
    }

    // Prepare the update query
    $updateQuery = "UPDATE timber SET 
                    type = :type,
                    t_amount = :amount,
                    t_height = :height,
                    t_width = :width,
                    size = :size,
                    t_volume = :volume,
                    t_location = :location
                    WHERE t_id = :record_id";

    $stmt = $pdo->prepare($updateQuery);
    
    // Bind parameters
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
    $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
    $stmt->bindParam(':height', $height, PDO::PARAM_STR);
    $stmt->bindParam(':width', $width, PDO::PARAM_STR);
    $stmt->bindParam(':size', $size, PDO::PARAM_STR);
    $stmt->bindParam(':volume', $volume, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':record_id', $recordId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes were made or record not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update record']);
    }

} catch (PDOException $e) {
    error_log("Database error in update-timber.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error in update-timber.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the record']);
}
?>