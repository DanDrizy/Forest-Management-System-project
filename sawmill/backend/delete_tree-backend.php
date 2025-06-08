<?php
// Include necessary files
require_once '../../logs/backend/auth_check.php';
checkUserAuth('sawmill');

include_once '../../database/connection.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_ids'])) {
        // Handle multiple selected items deletion
        $selectedIds = $_POST['selected_ids'];
        
        if (!empty($selectedIds)) {
            // Sanitize the IDs
            $sanitizedIds = array_map('intval', $selectedIds);
            $placeholders = str_repeat('?,', count($sanitizedIds) - 1) . '?';
            
            // Prepare and execute the delete statement
            $stmt = $pdo->prepare("UPDATE logs SET amount = 0 WHERE p_id IN ($placeholders)");
            $stmt->execute($sanitizedIds);
            
            $deletedCount = $stmt->rowCount();
            
            // Redirect with success message
            header("Location: ../index.php?message=Successfully deleted $deletedCount items&type=success");
            exit();
        } else {
            header("Location: ../index.php?message=No items selected for deletion&type=error");
            exit();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_all'])) {
        // Handle delete all visible items (if you want this functionality)
        $stmt = $pdo->prepare("UPDATE logs SET amount = 0 WHERE l_status = 'send' AND amount > 0");
        $stmt->execute();
        
        $deletedCount = $stmt->rowCount();
        
        header("Location: ../index.php?message=Successfully deleted all $deletedCount items&type=success");
        exit();
    } else {
        header("Location: ../index.php?message=Invalid request&type=error");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: ../index.php?message=Error occurred while deleting items&type=error");
    exit();
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    header("Location: ../index.php?message=An unexpected error occurred&type=error");
    exit();
}
?>