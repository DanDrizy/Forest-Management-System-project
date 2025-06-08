<?php
// auth_check.php - Include this at the top of every protected page
session_start();

// Set session timeout (30 minutes = 1800 seconds)
$session_timeout = 1800; 

// Function to check if user is logged in and validate role
function checkUserAuth($required_role = null) {
    global $session_timeout;
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        // User not logged in, redirect to login page
        header("Location: ../../logs/index.php");
        exit();
    }
    
    // Check if user has been inactive for too long
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
        // User has been inactive for too long, destroy the session
        session_unset();
        session_destroy();
        
        // Redirect to login page with timeout message
        header("Location: ../../logs/index.php?msg=timeout");
        exit();
    }
    
    // Check if user has the required role (if specified)
    if ($required_role !== null && $_SESSION['role'] !== $required_role) {
        // User doesn't have the required role
        header("Location: ../../logs/access_denied.php");
        exit();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

// Usage example:
// checkUserAuth(); // Just check if logged in
// checkUserAuth('admin'); // Check if logged in and has admin role
?>