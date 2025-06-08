<?php

// Include the database connection file
include_once '../../database/connection.php';

// Check if the form is submitted
if (isset($_POST['ok'])) {
    // Get the form data
    $name = $_POST['name'];
    $provider = $_POST['provider'];
    $status = $_POST['status'];
    $location = $_POST['location'];
    $amount = $_POST['amount'];
    $id = $_POST['id']; // Get the ID of the record to update

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("UPDATE sawmill_trees_records SET tree_name = :name, provider = :provider, status = :status, location = :location, amount = :amount WHERE tree_id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':id', $id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Tree updated successfully!');</script>";
        echo "<script>window.location.href = '../pages/tree.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating tree.');</script>";
        echo "<script>window.location.href = '../pages/tree.php?error=1';</script>";
        exit();
    }
}
else {
    echo "<script>alert('Invalid request.');</script>";
    echo "<script>window.location.href = '../pages/tree.php';</script>";
    exit();
}


?>