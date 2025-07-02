<?php
// Start session at the beginning
// session_start();

include '../../database/connection.php'; // Include the database connection file

if (isset($_POST['ok'])) {
    try {
        // Validate required fields
        $required_fields = ['l_id', 'amount', 'type'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required!");
            }
        }

        // Get and sanitize form data
        $l_id = filter_var($_POST['l_id'], FILTER_VALIDATE_INT);
        $location = 'None';
        $amount = filter_var($_POST['amount'], FILTER_VALIDATE_INT);
        $height = filter_var($_POST['height'], FILTER_VALIDATE_FLOAT);
        $width = filter_var($_POST['width'], FILTER_VALIDATE_FLOAT);
        $size = 0;
        $volume = 0;
        $type = trim($_POST['type']);
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        $cur_date = date('Y-m-d');
        $status = 'unsend'; // Default status for new logs

        // Set defaults for optional numeric fields
        $height = ($height === false) ? 0.0 : $height;
        $width = ($width === false) ? 0.0 : $width;

        // Validate data types
        if ($l_id === false || $amount === false) {
            throw new Exception("Invalid data format provided!");
        }

        if ($amount <= 0) {
            throw new Exception("Invalid amount! Please enter a positive number.");
        }

        // Check if PDO connection exists
        if (!isset($pdo)) {
            throw new Exception("Database connection not available!");
        }

        // Begin transaction for data consistency
        $pdo->beginTransaction();

        // Get current number of trees available
        $select_number_trees = "SELECT amount as number_trees FROM logs WHERE l_id = ?";
        $stmt = $pdo->prepare($select_number_trees);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare select statement!");
        }
        
        $stmt->execute([$l_id]);
        $tree = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if tree exists
        if (!$tree) {
            throw new Exception("Tree record not found!");
        }

        // Validate that we have enough trees to cut
        if ($tree['number_trees'] < $amount) {
            throw new Exception("Not enough trees available! Only " . $tree['number_trees'] . " trees remaining.");
        }

        // Calculate new amount
        $new_number_trees = $tree['number_trees'] - $amount;

        // Update the number of trees in logs table
        $update_number_trees = "UPDATE logs SET amount = ? WHERE l_id = ?";
        $stmt = $pdo->prepare($update_number_trees);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare update statement!");
        }
        
        $update_result = $stmt->execute([$new_number_trees, $l_id]);

        if (!$update_result) {
            throw new Exception("Failed to update tree record!");
        }

        // Insert the log record for cut trees
        $insert_logs = "INSERT INTO timber (l_id, t_amount, `type`, t_height, t_width, size, t_volume, t_indate, t_location, t_note, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($insert_logs);
        
        if (!$stmt) {
            throw new Exception("Failed to prepare insert statement!");
        }
        
        $insert_result = $stmt->execute([
            $l_id,
            $amount,
            $type,
            $height,
            $width,
            $size,
            $volume,
            $cur_date,
            $location,
            $note,
            $status
        ]);

        if (!$insert_result) {
            throw new Exception("Failed to add log record!");
        }

        // If we got here, everything worked fine, so commit the transaction
        $pdo->commit();

        // Set success message and redirect
        $_SESSION['success_message'] = 'Logs added successfully!';
        header('Location: ../pages/logs.php');
        exit;

    } catch (Exception $e) {
        // Rollback the transaction if anything went wrong
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Set error message and redirect
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../pages/logs.php');  
        exit;
        
    } catch (PDOException $e) {
        // Handle database-specific errors
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        header('Location: ../pages/logs.php');
        exit;
    }
}
?>