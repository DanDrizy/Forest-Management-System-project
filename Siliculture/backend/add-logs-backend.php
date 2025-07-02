<?php

include'../../database/connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $info = $_POST['info'];
    $p_id = $_POST['p_id'];
    $amount = 0;
    $height = 0;
    $comments = $_POST['comments'];
    $lt = $_POST['lt'];
    $compartment = $_POST['compartment']; // Added compartment field
    $d1 = 0;
    $d2 = 0;
    $v1 = 0;
    $v2 = 0; // bino mbikoreye kugirango nirinde gusiba volime and dimentions in database
    $inDate = $_POST['indate'];
    $status = "unsend"; // Assuming you want to set a default status


    $query = "INSERT INTO logs (p_id, amount, height,d1, d2, v1, v2, l_indate, l_status, coments, lt, compartment) VALUES (:p_id, :amount, :height, :d1, :d2, :v1, :v2, :indate, :status, :comment, :lt, :compartment)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':p_id', $p_id);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':height', $height);
    $stmt->bindParam(':d1', $d1);
    $stmt->bindParam(':d2', $d2);
    $stmt->bindParam(':v1', $v1);
    $stmt->bindParam(':v2', $v2);
    $stmt->bindParam(':indate', $inDate);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':comment', $comments);
    $stmt->bindParam(':lt', $lt);
    $stmt->bindParam(':compartment', $compartment); // Bind the compartment parameter


    $update_plant = "UPDATE plant SET p_status = 'send' WHERE p_id = :p_id";
    $update_stmt = $pdo->prepare($update_plant);
    $update_stmt->bindParam(':p_id', $p_id);
    $update_stmt->execute();

    if ($stmt->execute()) {
        echo "<script>alert('Logs added successfully!');</script>";
        echo "<script>window.location.href = '../pages/add-logs.php';</script>";

    } else {
        
        echo "<script>alert('Error adding logs.');</script>";
        echo "<script>window.location.href = '../pages/add-logs.php';</script>";
    }
}


?>