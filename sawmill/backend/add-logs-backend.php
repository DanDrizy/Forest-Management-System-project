<?php
include '../../database/connection.php'; // Include the database connection file

if (isset($_POST['ok'])) {
    try {
        // Validate required fields
        $required_fields = ['l_id', 'location', 'amount', 'height', 'width', 'size', 'volume', 'type'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required!");
            }
        }

        // Get and sanitize form data
        $l_id = filter_var($_POST['l_id'], FILTER_VALIDATE_INT);
        $location = trim($_POST['location']);
        $amount = filter_var($_POST['amount'], FILTER_VALIDATE_INT);
        $height = filter_var($_POST['height'], FILTER_VALIDATE_FLOAT);
        $width = filter_var($_POST['width'], FILTER_VALIDATE_FLOAT);
        $size = trim($_POST['size']);
        $volume = filter_var($_POST['volume'], FILTER_VALIDATE_FLOAT);
        $type = trim($_POST['type']);
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        $cur_date = date('Y-m-d');
        $status = 'unsend'; // Default status for new logs

        // Validate data types
        if ($l_id === false || $amount === false || $height === false || $width === false || $volume === false) {
            throw new Exception("Invalid data format provided!");
        }

        if ($amount <= 0) {
            throw new Exception("Invalid amount! Please enter a positive number.");
        }

        // Begin transaction for data consistency
        $pdo->beginTransaction();

        // Get current number of trees available
        $select_number_trees = "SELECT amount as number_trees FROM logs WHERE l_id = ?";
        $stmt = $pdo->prepare($select_number_trees);
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
        $update_result = $stmt->execute([$new_number_trees, $l_id]);

        if (!$update_result) {
            throw new Exception("Failed to update tree record!");
        }

        // Insert the log record for cut trees (Fixed SQL syntax)
        $insert_logs = "INSERT INTO timber (l_id, t_amount, `type`, t_height, t_width, size, t_volume, t_indate, t_location, t_note, status) 
                       VALUES (:l_id, :amount, :type, :height, :width, :size, :volume, :date, :location, :tree_note, :status)";
        
        $stmt = $pdo->prepare($insert_logs);
        $insert_result = $stmt->execute([
            ':l_id' => $l_id,
            ':amount' => $amount,
            ':type' => $type,
            ':height' => $height,
            ':width' => $width,
            ':size' => $size,
            ':volume' => $volume,
            ':date' => $cur_date,
            ':location' => $location,
            ':tree_note' => $note,
            ':status' => $status
        ]);

        if (!$insert_result) {
            throw new Exception("Failed to add log record!");
        }

        // If we got here, everything worked fine, so commit the transaction
        $pdo->commit();

        // Better way to handle success - use session or redirect with success parameter
        session_start();
        $_SESSION['success_message'] = 'Logs added successfully!';
        header('Location: ../pages/logs.php');
        exit;

    } catch (Exception $e) {
        // Rollback the transaction if anything went wrong
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // Better error handling - use session or redirect with error parameter
        session_start();
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../pages/logs.php');
        exit;
    }
}
?>