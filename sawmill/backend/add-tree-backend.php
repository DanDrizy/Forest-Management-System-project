<?php

// Include the database connection file
include_once '../../database/connection.php';


if(isset($_POST['ok'])) {
    // Get the form data
    $name = $_POST['name'];
    $provider = $_POST['provider'];
    $status = $_POST['status'];
    $location = $_POST['location'];
    $amount = $_POST['amount'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("INSERT INTO sawmill_trees_records (tree_name, provider, status, location, amount) VALUES (:name, :provider, :status, :location, :amount)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':amount', $amount);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "<script>alert('Tree added successfully!');</script>";
        echo "<script>window.location.href = '../pages/add_tree.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error adding tree.');</script>";
        echo "<script>window.location.href = '../pages/add_tree.php?error=1';</script>";
        exit();
    }

   
} else {
    echo "<script>alert('Invalid request.');</script>";
    echo "<script>window.location.href = '../pages/add_tree.php';</script>";
    exit();
}


?>