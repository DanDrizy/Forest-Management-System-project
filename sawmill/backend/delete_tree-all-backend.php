<?php

// Include the database connection file
include_once '../../database/connection.php';
$delete = $pdo->prepare("UPDATE logs l_status = 'send-back-sawmill WHERE  ' ");
$delete->execute();
if ($delete) {
    echo "<script>alert('All trees deleted successfully!');</script>";
    echo "<script>window.location.href = '../pages/tree.php';</script>";
    exit();
} else {
    echo "<script>alert('Error deleting trees.');</script>";
    echo "<script>window.location.href = '../pages/tree.php?error=1';</script>";
    exit();
}

?>