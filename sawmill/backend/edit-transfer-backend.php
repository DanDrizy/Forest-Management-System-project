<?php
include '../../database/connection.php'; // Include the database connection file

if(isset($_POST['update'])) {
    // Get form data
    $tranfer_id = $_POST['tranfer_id']; // Transfer record ID
    $tree_id = $_POST['tree_id']; // Tree ID
    $old_amount = $_POST['old_amount']; // Original transfer amount
    $new_amount = $_POST['new_amount']; // Updated transfer amount
    $date = $_POST['date']; // Transfer date
    
    // Validate input - allow zero for deletion
    if ($new_amount < 0) {
        echo "<script>alert('Invalid amount!');</script>";
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
        exit;
    }
    
    try {
        // Get the current transfer record
        $select_transfer = $pdo->prepare("SELECT * FROM sawmill_transfer_records WHERE tranfer_id = ?");
        $select_transfer->execute([$tranfer_id]);
        $transfer = $select_transfer->fetch(PDO::FETCH_ASSOC);
        
        if (!$transfer) {
            echo "<script>alert('Transfer record not found!');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        // Calculate the difference between old and new amounts
        $difference = $new_amount - $old_amount;
        
        // Get the corresponding tree record
        $select_tree = $pdo->prepare("SELECT * FROM logs_records WHERE log_id = ?");
        $select_tree->execute([$tree_id]);
        $tree = $select_tree->fetch(PDO::FETCH_ASSOC);
        
        if (!$tree) {
            echo "<script>alert('Tree record not found!');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        // Check if there are enough logs available if increasing the amount
        if ($difference > 0 && $tree['cutted_trees'] < $difference) {
            echo "<script>alert('Not enough logs available! Only " . $tree['cutted_trees'] . " logs remaining.');</script>";
            echo "<script>window.location.href = '../pages/edit_transfer.php?id=" . $tranfer_id . "');</script>";
            exit;
        }
        
        // Calculate the new cutted_trees value
        $new_cutted_trees = $tree['cutted_trees'] - $difference;
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // If new amount is 0, delete the transfer record
        if ($new_amount == 0) {
            // Delete the transfer record
            $delete_transfer = $pdo->prepare("DELETE FROM sawmill_transfer_records WHERE tranfer_id = ?");
            $delete_result = $delete_transfer->execute([$tranfer_id]);
            
            if (!$delete_result) {
                $pdo->rollBack();
                echo "<script>alert('Failed to delete transfer record!');</script>";
                echo "<script>window.location.href = '../pages/transfer.php';</script>";
                exit;
            }
        } else {
            // Update the sawmill_transfer_records table
            $update_transfer = $pdo->prepare("
                UPDATE sawmill_transfer_records 
                SET amount = ? 
                WHERE tranfer_id = ?
            ");
            
            $update_transfer_result = $update_transfer->execute([
                $new_amount,
                $tranfer_id
            ]);
            
            if (!$update_transfer_result) {
                $pdo->rollBack();
                echo "<script>alert('Failed to update transfer record!');</script>";
                echo "<script>window.location.href = '../pages/transfer.php';</script>";
                exit;
            }
        }
        
        // Check if the new cutted_trees would be 0
        if ($new_cutted_trees == 0) {
            // Delete the logs record if cutted_trees becomes 0
            $delete_tree = $pdo->prepare("DELETE FROM logs_records WHERE log_id = ?");
            $delete_tree_result = $delete_tree->execute([$tree_id]);
            
            if (!$delete_tree_result) {
                $pdo->rollBack();
                echo "<script>alert('Failed to delete logs record!');</script>";
                echo "<script>window.location.href = '../pages/transfer.php';</script>";
                exit;
            }
        } else {
            // Update the logs_records table
            $update_tree = $pdo->prepare("
                UPDATE logs_records 
                SET cutted_trees = ? 
                WHERE log_id = ?
            ");
            
            $update_tree_result = $update_tree->execute([
                $new_cutted_trees,
                $tree_id
            ]);
            
            if (!$update_tree_result) {
                $pdo->rollBack();
                echo "<script>alert('Failed to update logs record!');</script>";
                echo "<script>window.location.href = '../pages/transfer.php';</script>";
                exit;
            }
        }
        
        // Commit transaction if all operations succeeded
        $pdo->commit();
        
        // Prepare the success message
        $message = "";
        if ($new_amount == 0) {
            $message .= "Transfer record deleted. ";
        } else {
            $message .= "Transfer updated. ";
        }
        
        if ($new_cutted_trees == 0) {
            $message .= "Logs record deleted.";
        }
        
        echo "<script>alert('" . $message . "');</script>";
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
    } 
    catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "<script>alert('Database error: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
        exit;
    }
} else {
    // Redirect if the form wasn't submitted properly
    echo "<script>window.location.href = '../pages/transfer.php';</script>";
    exit;
}
?>