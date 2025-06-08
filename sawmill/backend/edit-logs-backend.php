<?php

require_once '../../database/connection.php'; // Include the database connection file

if(isset($_POST['edit'])) {
    
    $tree_id = $_POST['tree_id'];
    $cutted = $_POST['cutted'];
    $measure = $_POST['measure'];
    $volume = $_POST['volume'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $tree_name = $_POST['tree_name'];

    $log_id = $_POST['log_id']; // Get the log ID from the form

    $update = $pdo->prepare("UPDATE logs_records SET tree_id = ?, cutted_trees = ?, measure = ?, volume = ?, location = ?, date = ?, tree_name = ? WHERE log_id = ?");
    $update->execute([$tree_id, $cutted, $measure, $volume, $location, $date, $tree_name , $log_id ]); // Update the logs_records table with the new data
    

    echo "<script>alert('Logs updated successfully!');</script>";
    echo "<script>window.location.href = '../pages/logs.php';</script>"; // Redirect to the logs page after successful update


}



?>