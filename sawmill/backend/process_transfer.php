<?php
// Include necessary files
require_once '../../logs/backend/auth_check.php';
require_once '../../database/connection.php';

// Check if user is authenticated
checkUserAuth('sawmill');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/transfer.php?error=invalid_request');
    exit();
}

// Check if logs were selected
if (!isset($_POST['selected_logs']) || empty($_POST['selected_logs'])) {
    header('Location: ../pages/transfer.php?error=no_logs_selected');
    exit();
}

$selected_logs = $_POST['selected_logs'];
$transfer_count = 0;
$errors = [];

try {
    // Start transaction
    $pdo->beginTransaction();
    
    foreach ($selected_logs as $l_id) {
        //Validate log ID
        $l_id = (int)$l_id;
        if ($l_id <= 0) {
            continue;
        }
        
        // Check if the timber record exists and is not already sent
        $check_query = $pdo->prepare("
            SELECT *
            FROM timber t 
            WHERE t.t_id = ?
        ");
        $check_query->execute([$l_id]);
        $timber_record = $check_query->fetch();
        
        if (!$timber_record) {
            $errors[] = "Timber record with log ID $l_id not found or already sent";
            continue;
        }
        
        // Update the timber status to 'sent'
        $update_query = $pdo->prepare("
            UPDATE timber 
            SET status = 'send'
            WHERE t_id = ?
        ");
        
        if ($update_query->execute([$l_id])) {

            if ($update_query->rowCount() > 0) {
                $transfer_count++;
            }
        } else {
            $errors[] = "Failed to update timber record with log ID $l_id";
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    // Prepare success message
    if ($transfer_count > 0) {
        $message = "Successfully transferred $transfer_count timber log(s) to Sales department.";
        if (!empty($errors)) {
            $message .= " Note: " . implode(", ", $errors);
        }
        header("Location: ../pages/transfer.php?success=transfer_complete&count=$transfer_count&message=" . urlencode($message));
    } else {
        $error_message = empty($errors) ? "No records were updated. weee ".$selected_logs." " : implode(", ", $errors);
        header("Location: ../pages/transfer.php?error=transfer_failed&message=" . urlencode($error_message));
    }
    
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    // Log the error (you might want to log this to a file)
    error_log("Transfer error: " . $e->getMessage());
    
    header("Location: ../pages/transfer.php?error=transfer_failed&message=" . urlencode("Database error occurred during transfer."));
}

exit();
?>