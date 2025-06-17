<?php
$host = 'localhost';      // or 127.0.0.1
$dbname = 'forestry_management'; // your MySQL database name
$username = 'root';       // your MySQL username
$password = '';           // your MySQL password (default is empty in XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
