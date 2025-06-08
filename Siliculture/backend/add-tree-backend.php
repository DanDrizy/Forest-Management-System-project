<?php

include'../../database/connection.php'; // Include the database connection file

if (isset($_POST['submit'])) {
    $g_id = $_POST['g_id'];
    $dbh = $_POST['dbh'];
    $health = $_POST['health'];
    $p_height = $_POST['height'];
    $info = $_POST['info'];
    $p_status = 'unsend';
    $date = date('Y-m-d'); // Get the current date

    $sql = "INSERT INTO plant (g_id, DBH, health, p_height, info, p_status, indate) VALUES (:g_id, :dbh, :health, :p_height, :info, :p_status, :indate)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['g_id' => $g_id, 'dbh' => $dbh, 'health' => $health, 'p_height' => $p_height, 'info' => $info, 'p_status' => $p_status, 'indate' => $date]);
    

    echo "<script>alert('Tree added successfully!');</script>";
   
    
    echo "<script>window.location.href = '../pages/plant.php';</script>";
    
    exit;
}

?>