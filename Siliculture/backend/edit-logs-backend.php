<?php

// Include necessary files
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role
require_once '../../database/connection.php'; // Include the database connection file

// Check if the form was submitted

if (isset($_POST['ok'])) {

    // Get the data from the form
    $info = $_POST['info'];
    $amount = $_POST['amount'];
    $volume = $_POST['volume'];
    $measure = $_POST['measure'];
    $date = $_POST['date'];
    $indate = $_POST['indate'];
    $log_id = $_POST['id'];
    $p_id = $_POST['p_id'];


    
    

        // Start a transaction
        $pdo->beginTransaction();
        
        // Update the logs record
        $query = "UPDATE logs SET 
                  p_id = :p_id, 
                  amount = :amount, 
                  volume = :volume, 
                  measure = :measure, 
                  indate = :indate
                  WHERE log_id = :log_id";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'p_id' => $p_id,
            'amount' => $amount,
            'volume' => $volume,
            'measure' => $measure,
            'indate' => $indate,
            'log_id' => $log_id
        ]);
        
        // Commit the transaction
        $pdo->commit();
        
        
        // Redirect with success message
        // header("Location: ../content/logs.php?success=update");
        echo "<script>alert('Tree added successfully!');</script>";
        echo "<script>window.location.href = '../pages/logs.php';</script>";
        exit();
        

} else {
    // If the form was not submitted, redirect to the form page
    // header("Location: ../content/logs.php");
    echo "<script>alert('Failed!');</script>";
    echo "<script>window.location.href = '../pages/logs.php';</script>";
    exit();
}
?>