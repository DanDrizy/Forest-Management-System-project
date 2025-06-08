<?php include '../../database/connection.php'; 
if (isset($_POST['ok'])) {
    $id = $_POST['t_id'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $inDate = $_POST['inDate'];
    $stock_amount = $_POST['stock_amount'];
    
    list($t_id, $amount) = explode('|', $id);
    // Check if inserted amount is bigger than available amount
    if ($amount < $stock_amount) {

        echo "<script>alert('Inserted Amount is Bigger than Available Amount'); window.location.href='../pages/add-product.php'; </script>";
        exit();
    }
    
    $new_amount_timber = $amount - $stock_amount; // Calculate the new amount after deducting

    // Check if a record with the same product, date, and price already exists 
    $check_existing = $pdo->prepare("SELECT * FROM stockin WHERE
        t_id = :id AND
        price = :price AND
        s_indate = :s_indate");
    $check_existing->bindParam(':id', $t_id);
    $check_existing->bindParam(':price', $price);
    $check_existing->bindParam(':s_indate', $inDate);
    $check_existing->execute();
    
    $existing_record = $check_existing->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_record) {
        // Update existing record with increased amount
        $new_amount = $existing_record['s_amount'] + $stock_amount;
        
        $update_record = $pdo->prepare("UPDATE stockin SET
            s_amount = :new_amount
            WHERE in_id = :record_id");
        
        $update_record->bindParam(':new_amount', $new_amount);
        $update_record->bindParam(':record_id', $existing_record['in_id']);
        $update_record->execute();
        
        $message = "Record updated successfully";
    } else {
        // Insert new record
        $insert = $pdo->prepare("INSERT INTO stockin
            (t_id, s_amount, price, s_indate)
            VALUES
            (:t_id, :s_amount, :price, :s_indate)");
        
        $insert->bindParam(':t_id', $t_id);
        $insert->bindParam(':s_amount', $stock_amount);
        $insert->bindParam(':price', $price);
        $insert->bindParam(':s_indate', $inDate);
        $insert->execute();
        
        $message = "New record added successfully";
    }


    $update_timber_amount = $pdo->prepare("UPDATE timber SET t_amount = :new_amount WHERE t_id = :t_id");
    $update_timber_amount->bindParam(':new_amount', $new_amount_timber);
    $update_timber_amount->bindParam(':t_id', $t_id);
    $update_timber_amount->execute();

    
    
    echo "<script>alert('$message'); window.location.href='../pages/add-product.php'; </script>";
}
?>