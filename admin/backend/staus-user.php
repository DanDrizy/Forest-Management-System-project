<?php

include_once '../../database/connection.php';

$id = $_GET['id'];
$status = $_GET['status'];

if ($status == 'active') {
    $newStatus = 'blocked';
    // Update the user status in the database
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
    // Check if the update was successful
    echo "
    <script>alert('User blocked successfully!');
    window.location.href = '../pages/control.php';</script>";
} else {
    $newStatus = 'active';
    // Update the user status in the database
    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
    // Check if the update was successful
    echo "<script>alert('User activated successfully!');
    window.location.href = '../pages/control.php';</script>";
}



?>