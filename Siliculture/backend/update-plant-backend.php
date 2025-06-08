<?php
// Include necessary files
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role
include '../../database/connection.php';

// Check if the form was submitted
if (isset($_POST['submit'])) {
    // Get form data and sanitize inputs
    // $id = $_GET['id'] ? $_GET['id'] : 0;
    $id = htmlspecialchars(trim($_POST['id']));
    $p_name = htmlspecialchars(trim($_POST['p_name']));
    $country = htmlspecialchars(trim($_POST['country']));
    $provance = htmlspecialchars(trim($_POST['provance']));
    $location = htmlspecialchars(trim($_POST['location']));
    $tree_type = htmlspecialchars(trim($_POST['tree-type']));
    $info = htmlspecialchars(trim($_POST['additionalInfo']));
    $comment = htmlspecialchars(trim($_POST['comment']));
    $date = date('Y-m-d');
    
    
    
    try {
        // Prepare update statement
        $stmt = $pdo->prepare("UPDATE plant SET 
            p_name = :p_name,
            country = :country,
            provance = :provance,
            location = :location,
            tree_type = :tree_type,
            info = :info,
            comment = :comment,
            indate = :date
            WHERE p_id = :id");
        
        // Bind parameters
        $stmt->bindParam(':p_name', $p_name);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':provance', $provance);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':tree_type', $tree_type);
        $stmt->bindParam(':info', $info);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $id);
        

        // Execute the query
        $stmt->execute();
        
        // Check if update was successful
        if ($stmt->rowCount() > 0) {

            echo "<script>alert('Plant updated successfully!');</script>";
            echo "<script>window.location.href = '../pages/plant.php?update=success';</script>";
            exit();


        } else {
            
            echo "<script>alert('No changes made or record not found!');</script>";
            echo "<script>window.location.href = '../pages/plant.php?update=no_changes';</script>";
            exit();
        }
    } catch (PDOException $e) {

        // Log the database error
        error_log("Database error: " . $e->getMessage());
        
        

        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        echo "<script>window.location.href = '../pages/plant.php?error=database_error';</script>";
        exit();
    }
} else {
    

    echo "<script>window.location.href = '../pages/plant.php';</script>";
    echo "<script>window.location.href = '../pages/plant.php?error=database_error';</script>";


    exit();
}
?>