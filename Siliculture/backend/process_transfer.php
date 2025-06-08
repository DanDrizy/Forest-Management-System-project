<?php
    require_once '../../logs/backend/auth_check.php'; // Include the authentication check
    checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role


    include'../../database/connection.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected logs and notes
    $selected_logs = isset($_POST['selected_logs']) ? $_POST['selected_logs'] : [];
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    
    // Validate that logs were selected
    if (empty($selected_logs)) {
        // No logs selected, redirect back with error message
        header('Location: ../transfer.php?error=no_logs_selected');
        exit;
    }
    
    try {
        // Begin transaction for data integrity
        $pdo->beginTransaction();
        
        // Get current date and time for the transfer record
        $transfer_date = date('Y-m-d H:i:s');
        
        // Create a new transfer record in the transfers table (if you have one)
        // If you don't have a transfers table, you might want to create one to track transfers
        // $create_transfer = $pdo->prepare("
        //     INSERT INTO transfers (transfer_date, notes, status) 
        //     VALUES (:transfer_date, :notes, 'completed')
        // ");
        // $create_transfer->execute([
        //     ':transfer_date' => $transfer_date,
        //     ':notes' => $notes
        // ]);
        
        // Get the ID of the newly created transfer
        $transfer_id = $pdo->lastInsertId();
        
        // Update each selected log to mark as sent
        $update_logs = $pdo->prepare("
            UPDATE logs 
            SET l_status = 'sent'
            WHERE l_id = :log_id
        ");
        
        // Keep track of processed logs
        $processed_logs = [];
        
        // Process each selected log
        foreach ($selected_logs as $log_id) {
            $update_logs->execute([
                ':log_id' => $log_id
            ]);
            
            // Add to processed logs array
            $processed_logs[] = $log_id;
        }
        
        // Commit the transaction
        $pdo->commit();
        
        // Redirect back with success message
        header('Location: ../pages/transfer.php?success=transfer_complete&count=' . count($processed_logs));
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $pdo->rollBack();
        
        // Log the error (you might want to implement proper error logging)
        error_log("Transfer processing error: " . $e->getMessage());
        
        // Redirect back with error message
        header('Location: ../pages/transfer.php?error=transfer_failed&message=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Not a POST request, redirect back to the transfer page
    header('Location: ../pages/transfer.php');
    exit;
}
?>