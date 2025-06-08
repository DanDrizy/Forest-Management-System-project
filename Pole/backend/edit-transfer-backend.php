<?php
include '../../database/connection.php';

if (isset($_POST['ok'])) {
    try {
        // Start transaction for data consistency
        $pdo->beginTransaction();
        
        $combine = $_POST['pole_info'];
        $date = $_POST['date'];
        $new_amount = $_POST['amount'];
        $record_date = $_POST['record_date'];
        
        list($tree_name, $pole_transfer_id, $location) = explode('|', $combine);
        
        // First, retrieve the existing transfer record
        $select_existing_transfer = $pdo->prepare("SELECT * FROM poleplant_transfer WHERE pole_transfer_id = :pole_transfer_id");
        $select_existing_transfer->bindParam(':pole_transfer_id', $pole_transfer_id);
        $select_existing_transfer->execute();
        $existing_transfer = $select_existing_transfer->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing_transfer) {
            throw new Exception("Transfer record not found.");
        }
        
        // Retrieve the original pole plant record to get current amount
        $select_poleplant_record = $pdo->prepare("SELECT * FROM poleplant_records WHERE pole_id = :pole_id");
        $select_poleplant_record->bindParam(':pole_id', $existing_transfer['pole_id']);
        $select_poleplant_record->execute();
        $poleplant_record = $select_poleplant_record->fetch(PDO::FETCH_ASSOC);
        
        if (!$poleplant_record) {
            throw new Exception("Pole plant record not found.");
        }
        
        // Calculate the difference in amounts
        $original_amount = $existing_transfer['amount'];
        $amount_difference = $new_amount - $original_amount;
        
        // Check if there's enough amount available in the pole plant record
        $current_pole_amount = $poleplant_record['amount'];
        
        if ($amount_difference > 0 && $amount_difference > $current_pole_amount) {
            // If trying to increase amount and not enough available
            throw new Exception("Not enough amount available in pole plant record.");
        }
        
        // Update pole plant record amount
        $new_pole_amount = $current_pole_amount - $amount_difference;
        $update_poleplant_record = $pdo->prepare("UPDATE poleplant_records SET amount = :new_pole_amount WHERE pole_id = :pole_id");
        $update_poleplant_record->bindParam(':new_pole_amount', $new_pole_amount);
        $update_poleplant_record->bindParam(':pole_id', $existing_transfer['pole_id']);
        $update_poleplant_record->execute();
        
        // Update the transfer record
        $update_transfer = $pdo->prepare("UPDATE poleplant_transfer 
            SET amount = :new_amount, date = :date, indate = :record_date 
            WHERE pole_transfer_id = :pole_transfer_id");
        $update_transfer->bindParam(':new_amount', $new_amount);
        $update_transfer->bindParam(':date', $date);
        $update_transfer->bindParam(':record_date', $record_date);
        $update_transfer->bindParam(':pole_transfer_id', $pole_transfer_id);
        $update_transfer->execute();


        // //update the checking in sawmill record to avoid double entry or miss proccessing the data

        // $tree_id = $poleplant_record['tree_id'];
        // $update_transfer = $pdo->prepare("UPDATE sawmill_transfer_records SET checking = '+' WHERE tree_id = :pole_id");
        // $update_transfer->bindParam(':pole_transfer_id', $tree_id);
        // $update_transfer->execute();
        
        // Commit the transaction
        $pdo->commit();
        
        echo "<script>alert('Transfer updated successfully');</script>";
        echo "<script>window.location.href = '../pages/transfer.php';</script>";
        
    } catch (Exception $e) {
        // Rollback on any error
        $pdo->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        echo "<script>window.location.href = '../pages/add_tree.php';</script>";
    }
}
?>