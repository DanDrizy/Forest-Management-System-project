<?php
include '../../database/connection.php'; // Include the database connection file

$id = $_GET['id'];
$action = $_GET['action'];

// Start a transaction for data consistency
$pdo->beginTransaction();

try {
    // Get the current record details before updating
    $get_current = $pdo->prepare("SELECT pole_id, tree_name, date, measure, volume, location FROM poleplant_transfer WHERE pole_transfer_id = :id");
    $get_current->bindParam(':id', $id);
    $get_current->execute();
    $current_record = $get_current->fetch(PDO::FETCH_ASSOC);

    if (!$current_record) {
        throw new Exception("Record not found");
    }

    // Determine the new status based on the action
    $new_status = ($action == 'unactive') ? 'active' : 'unactive';

    // Update the status of the current record
    $update = $pdo->prepare("UPDATE poleplant_transfer SET status = :new_status WHERE pole_transfer_id = :id");
    $update->bindParam(':new_status', $new_status);
    $update->bindParam(':id', $id);
    $update->execute();
    
    // Find other records with the same date, measure, volume, location, and the new status (excluding the current record)
    $find_similar = $pdo->prepare("SELECT pole_transfer_id, amount FROM poleplant_transfer 
                                  WHERE pole_id = :pole_id
                                  AND date = :date
                                  AND measure = :measure
                                  AND volume = :volume
                                  AND location = :location
                                  AND status = :status
                                  AND pole_transfer_id != :id
                                  AND status != 'instock'
                                  AND status != 'received'");
    
    $find_similar->bindParam(':pole_id', $current_record['pole_id']);
    $find_similar->bindParam(':date', $current_record['date']);
    $find_similar->bindParam(':measure', $current_record['measure']);
    $find_similar->bindParam(':volume', $current_record['volume']);
    $find_similar->bindParam(':location', $current_record['location']);
    $find_similar->bindParam(':status', $new_status);
    $find_similar->bindParam(':id', $id);
    $find_similar->execute();
    
    $similar_records = $find_similar->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($similar_records) > 0) {
        // Get the current record amount
        $get_amount = $pdo->prepare("SELECT amount FROM poleplant_transfer WHERE pole_transfer_id = :id");
        $get_amount->bindParam(':id', $id);
        $get_amount->execute();
        $current_amount = $get_amount->fetchColumn();
        
        // Calculate total amount including all similar records
        $total_amount = $current_amount;
        $ids_to_delete = [];
        
        foreach ($similar_records as $record) {
            $total_amount += $record['amount'];
            $ids_to_delete[] = $record['pole_transfer_id'];
        }
        
        // Update the current record with the combined amount
        $update_amount = $pdo->prepare("UPDATE poleplant_transfer SET amount = :total_amount WHERE pole_transfer_id = :id");
        $update_amount->bindParam(':total_amount', $total_amount);
        $update_amount->bindParam(':id', $id);
        $update_amount->execute();
        
        // Delete the other similar records that were merged
        if (!empty($ids_to_delete)) {
            $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
            $delete_similar = $pdo->prepare("DELETE FROM poleplant_transfer WHERE pole_transfer_id IN ($placeholders)");
            
            foreach ($ids_to_delete as $key => $value) {
                $delete_similar->bindValue($key + 1, $value);
            }
            
            $delete_similar->execute();
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    // Redirect with success message
    if ($new_status == 'active') {
        echo "<script>alert('Pole is now Activated" . (count($similar_records) > 0 ? " and similar records were merged" : "") . "');</script>";
    } else {
        echo "<script>alert('Pole is now Deactivated" . (count($similar_records) > 0 ? " and similar records were merged" : "") . "');</script>";
    }
    echo "<script>window.location.href = '../pages/transfer.php';</script>";
    
} catch (Exception $e) {
    // Rollback on any error
    $pdo->rollBack();
    echo "<script>alert('Error: " . str_replace("'", "\\'", $e->getMessage()) . "');</script>";
    echo "<script>window.location.href = '../pages/transfer.php';</script>";
}
?>