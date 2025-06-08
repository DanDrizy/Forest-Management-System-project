<?php
include '../../database/connection.php';

if (isset($_POST['ok'])) {
    $info = $_POST['info'];
    $amount = $_POST['amount'];
    $price = $_POST['price'];
    $date = date('Y-m-d');
    $note = $_POST['note'];

    list($in_amount, $in_id) = explode('|', $info);

    if ($amount > $in_amount) {
        echo "<script>alert('Not enough stock!');</script>";
        exit;
    }

    // Check if stockout entry already exists for the same in_id and date
    $select_exist = $pdo->prepare("SELECT * FROM stockout WHERE in_id = :in_id AND out_date = :out_date");
    $select_exist->bindParam(':in_id', $in_id);
    $select_exist->bindParam(':out_date', $date);
    $select_exist->execute();
    $fetch_exist = $select_exist->fetch(PDO::FETCH_ASSOC);

    if ($fetch_exist) {
        // Update existing stockout record
        $fetch_exist = $select_exist->fetch(PDO::FETCH_ASSOC);
        $old_amount = $fetch_exist['out_amount'];
        $old_note = $fetch_exist['out_note'];
        $out_id = $fetch_exist['out_id'];

        // Combine notes
        $combined_note = !empty($old_note) ? $old_note . " | " . $note : $note;
        
        // Add new amount to existing amount
        $new_out_amount = $amount + $old_amount;

        // Check if total amount doesn't exceed available stock
        // We need to get current stock and add back the old amount to check availability
        $current_stock = $in_amount + $old_amount; // Current stock + previously taken amount
        if ($new_out_amount > $current_stock) {
            echo "<script>alert('Not enough stock for total amount!');</script>";
            exit;
        }

        // Update stockout record
        $update = $pdo->prepare("UPDATE stockout SET out_price = :out_price, out_amount = :out_amount, out_note = :note WHERE out_id = :out_id");
        $update->bindParam(':out_price', $price);
        $update->bindParam(':out_amount', $new_out_amount);
        $update->bindParam(':out_id', $out_id);
        $update->bindParam(':note', $combined_note);
        $update->execute();

        // Update stockin - reduce by the additional amount only
        $new_stock_amount = $in_amount - $amount; // Only reduce by the new amount being added
        $update_stockin = $pdo->prepare("UPDATE stockin SET s_amount = :new_amount WHERE in_id = :in_id");
        $update_stockin->bindParam(':new_amount', $new_stock_amount);
        $update_stockin->bindParam(':in_id', $in_id);
        $update_stockin->execute();

        echo "<script>alert('Stockout updated successfully!');</script>";
        exit;
    }

    // Insert new stockout record
    $insert = $pdo->prepare("INSERT INTO stockout(in_id, out_price, out_date, out_note, out_amount) VALUES (:in_id, :out_price, :out_date, :out_note, :out_amount)");
    $insert->bindParam(':in_id', $in_id);
    $insert->bindParam(':out_price', $price);
    $insert->bindParam(':out_date', $date);
    $insert->bindParam(':out_note', $note);
    $insert->bindParam(':out_amount', $amount);
    $insert->execute();

    // Update stockin - reduce available amount
    $new_amount = $in_amount - $amount;
    $update_stockin = $pdo->prepare("UPDATE stockin SET s_amount = :new_amount WHERE in_id = :in_id");
    $update_stockin->bindParam(':new_amount', $new_amount);
    $update_stockin->bindParam(':in_id', $in_id);
    $update_stockin->execute();

    echo "<script>alert('Stockout added successfully!');</script>";
}
?>