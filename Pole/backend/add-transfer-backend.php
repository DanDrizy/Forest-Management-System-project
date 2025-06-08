<?php
include '../../database/connection.php';

if (isset($_POST['ok'])) {
    
    $name = $_POST['pole_name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $amount = $_POST['amount'];
    $height = $_POST['height'];
    $record_date = $_POST['record_date'];

    // Prepare the SQL statement
    $stmt = $pdo->prepare("INSERT INTO pole (tree_name, po_indate, location, po_amount, height, record_date) VALUES (:name, :date, :location, :amount, :height, :record_date)");
    
    // Bind the parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':height', $height);
    $stmt->bindParam(':record_date', $record_date);
    // Note: Ensure that the 'pole' table has the columns: tree_name, po_date, location, po_amount, height, record_date
    // Note: Adjust the column names in the SQL statement to match your database schema
    // Note: Ensure that the PDO connection is established correctly in the included connection file
    // Execute the statement

    if ($stmt->execute()) {
        // Redirect to the add page with a success message
        header("Location: ../pages/add.php?success=Pole added successfully");
        exit();
    } else {
        // Redirect to the add page with an error message
        header("Location: ../pages/add.php?error=Failed to add pole");
        exit();
    }
}
?>