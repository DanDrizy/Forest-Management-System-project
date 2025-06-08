<?php
include '../../database/connection.php'; // Include the database connection file

// Get parameters from the URL
$id = isset($_GET['id']) ? $_GET['id'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$amount = isset($_GET['amount']) ? $_GET['amount'] : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';
$tree_id = isset($_GET['tree_id']) ? $_GET['tree_id'] : '';

// Validate inputs
if (empty($id) || empty($action)) {
    echo "<script>alert('Invalid parameters provided!');</script>";
    echo "<script>window.location.href = '../pages/transfer.php';</script>";
    exit;
}

try {
    if ($action == 'send') {
        // Set the record to active (sent)
        $update = $pdo->prepare("UPDATE sawmill_transfer_records SET send = 'active' WHERE tranfer_id = :id");
        $update->bindParam(':id', $id, PDO::PARAM_INT);
        $update->execute();
        
        echo "<script>alert('The log has been sent successfully!');</script>";
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
        
    } elseif ($action == 'unsend') {
        // First, get the current record that we're unsending
        $current_record = $pdo->prepare("SELECT * FROM sawmill_transfer_records 
                                        WHERE tranfer_id = :id 
                                        AND send = 'active'");
        $current_record->bindParam(':id', $id, PDO::PARAM_INT);
        $current_record->execute();
        $record = $current_record->fetch(PDO::FETCH_ASSOC);
        
        if (!$record) {
            echo "<script>alert('Record not found!');</script>";
            echo "<script>window.location.href = '../pages/transfer.php';</script>";
            exit;
        }
        
        // Check if there's already an unsent record with the same ID, date, and tree_id
        $select = $pdo->prepare("SELECT * FROM sawmill_transfer_records 
                                WHERE tranfer_id != :id 
                                AND date = :date 
                                AND tree_id = :tree_id
                                AND send = 'unactive'");
        $select->bindParam(':id', $id, PDO::PARAM_INT);
        $select->bindParam(':date', $record['date'], PDO::PARAM_STR);
        $select->bindParam(':tree_id', $record['tree_id'], PDO::PARAM_INT);
        $select->execute();
        $existing_record = $select->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_record) {
            // A matching unsent record exists, add the amounts together
            $total_amount = $existing_record['amount'] + $record['amount'];
            
            // Update the existing record with the combined amount
            $update = $pdo->prepare("UPDATE sawmill_transfer_records 
                                    SET amount = :total_amount 
                                    WHERE tranfer_id = :existing_id");
            $update->bindParam(':total_amount', $total_amount, PDO::PARAM_INT);
            $update->bindParam(':existing_id', $existing_record['tranfer_id'], PDO::PARAM_INT);
            $update->execute();
            
            // Delete the current record that we're unsending since we've combined its amount
            $delete = $pdo->prepare("DELETE FROM sawmill_transfer_records 
                                    WHERE tranfer_id = :id");
            $delete->bindParam(':id', $id, PDO::PARAM_INT);
            $delete->execute();
            
            echo "<script>alert('The log has been unsent and combined with existing record!');</script>";
        } else {
            // No matching record exists, simply update the current record to unsent
            $update = $pdo->prepare("UPDATE sawmill_transfer_records 
                                    SET send = 'unactive' 
                                    WHERE tranfer_id = :id");
            $update->bindParam(':id', $id, PDO::PARAM_INT);
            $update->execute();
            
            echo "<script>alert('The log has been unsent successfully!');</script>";
        }
        
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
    } else {
        echo "<script>alert('Invalid action specified!');</script>";
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
    }
} catch (PDOException $e) {
    echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "');</script>";
    echo "<script>window.location.href = '../pages/transfer.php';</script>";
}
?>