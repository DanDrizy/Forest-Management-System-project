<?php
include '../../database/connection.php'; // Include the database connection file

if(isset($_POST['ok'])) {
    // Get form data
    $log_id = trim($_POST['log_id']); // Main tree identifier
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    
    // Get the hidden field values
    $tree_name = $_POST['tree_name'];
    $tree_id = $_POST['tree_id'];
    $volume = $_POST['volume'];
    $measure = $_POST['measure'];
    $location = $_POST['location'];
    
    // Default send status
    $send = "unactive";
    
    try {
        // Check if tree exists and get current data
        $select_tree = $pdo->prepare("SELECT * FROM logs_records WHERE log_id = ?");
        $select_tree->execute([$log_id]);
        $tree = $select_tree->fetch(PDO::FETCH_ASSOC);

        


        
        if (!$tree) {
            echo "<script>alert('Tree not found!');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        // Check if amount is valid (using cutted_trees field from the table)
        if ($tree['cutted_trees'] < $amount) {
            echo "<script>alert('Not enough logs! Available: " . $tree['cutted_trees'] . "');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        if ($amount <= 0) {
            echo "<script>alert('Invalid amount!');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        // Make sure measure is properly set
        if (empty($measure)) {
            // Fallback to the value from logs_records if not in the form
            $measure = $tree['measure'];
        }
        
        // Update the remaining tree amount
        $new_amount = $tree['cutted_trees'] - $amount;
        
        $pdo->beginTransaction();
        
        // Update logs_records table
        $update_tree = $pdo->prepare("UPDATE logs_records SET cutted_trees = ? WHERE log_id = ?");
        $update_result = $update_tree->execute([$new_amount, $log_id]);
        
        if (!$update_result) {
            $pdo->rollBack();
            echo "<script>alert('Failed to update tree record!');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        // Check if a record with the same tree_id already exists
        $check_existing = $pdo->prepare("
            SELECT * FROM sawmill_transfer_records 
            WHERE tree_id = ? AND measure = ? AND volume = ? AND location = ? AND send = ? AND tree_name = ?
        ");
        $check_existing->execute([$tree_id, $measure, $volume, $location, 'unactive', $tree_name]);
        $existing_record = $check_existing->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_record) {
            // Update the existing record by adding to the amount
            $new_total_amount = $existing_record['amount'] + $amount;
            
            $update_transfer = $pdo->prepare("
                UPDATE sawmill_transfer_records
                SET amount = ?, date = ?
                WHERE tree_id = ? AND send = 'unactive'
            ");
            
            $update_transfer_result = $update_transfer->execute([
                $new_total_amount,
                $date,
                $existing_record['tree_id']
            ]);
            
            if (!$update_transfer_result) {
                $pdo->rollBack();
                echo "<script>alert('Failed to update existing transfer record!');</script>";
                echo "<script>window.location.href = '../pages/transfer.php';</script>";
                exit;
            }
        } else {
            // Insert a new record into sawmill_transfer_records table
            $insert_transfer = $pdo->prepare("
                INSERT INTO sawmill_transfer_records 
                (tree_id, amount, measure, date, volume, send, tree_name, location)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $insert_result = $insert_transfer->execute([
                $tree_id,    // tree_id from form
                $amount,     // amount from form
                $measure,    // measure from form
                $date,       // date from form
                $volume,     // volume from form
                $send,       // default "unactive"
                $tree_name,  // tree_name from form
                $location    // location from form
            ]);
            
            if (!$insert_result) {
                $pdo->rollBack();
                echo "<script>alert('Failed to record transfer!');</script>";
                echo "<script>window.location.href = '../pages/transfer.php';</script>";
                exit;
            }
        }
        
        // Commit transaction if all operations succeeded
        $pdo->commit();
        
        echo "<script>alert('Transfer recorded successfully!');</script>";
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
}
?>