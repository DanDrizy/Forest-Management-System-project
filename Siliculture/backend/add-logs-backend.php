<?php

include'../../database/connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $info = $_POST['info'];
    $p_id = $_POST['p_id'];
    $amount = $_POST['amount'];
    $height = $_POST['height'];
    $d1 = $_POST['d-1'];
    $d2 = $_POST['d-2'];
    $v1 = $_POST['start-volume'];
    $v2 = $_POST['end-volume'];
    $inDate = $_POST['indate'];
    $status = "unsend"; // Assuming you want to set a default status


    $query = "INSERT INTO logs (p_id, amount, height,d1, d2, v1, v2, l_indate,l_status) VALUES (:p_id, :amount, :height, :d1, :d2, :v1, :v2, :indate, :status)";
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