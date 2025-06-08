<?php
// Start session at the beginning of each page
session_start();

// Set session timeout (30 minutes = 1800 seconds)
$session_timeout = 1800; 

// Check if user is inactive for too long
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // User has been inactive for too long, destroy the session
    session_unset();
    session_destroy();
    
    // Redirect to login page
    header("Location: ../../logs/index.php");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// login.php
// error_reporting(0); 
include '../../database/connection.php';

if (isset($_POST['ok'])) {
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    
    // Prepare and execute a secure query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row && $pass == $row['password']) {
        // Store user data in session (not cookies)
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $row['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
        
        $user_role = $row['role'];        
        // Redirect based on role
        if ($user_role == 'admin') {
            header("Location: ../../admin/pages/index.php");
        } else if ($user_role == 'sawmill') {
            header("Location: ../../sawmill/pages/index.php");
        } else if ($user_role == 'sales') {
            header("Location: ../../sale/pages/index.php");
        } else if ($user_role == 'pole plant') {
            header("Location: ../../pole/pages/index.php");
        } else if ($user_role == 'Siliculture') {
            header("Location: ../../siliculture/pages/index.php");
        } else {
            $_SESSION['error'] = "Role not recognized";
            echo "<script>alert('Role not recognized');</script>";
            header("Location: ../../logs/index.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid credentials";
        echo "<script>alert('Invalid credentials');</script>";
        header("Location: ../../logs/index.php");
        exit();
    }
}
?>