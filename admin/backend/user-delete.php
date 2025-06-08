<?php

include('../../database/connection.php');
$id = $_GET['id'];

$deleteUser = $pdo->prepare("DELETE FROM users WHERE id = :id");
$deleteUser->bindParam(':id', $id, PDO::PARAM_INT);
$deleteUser->execute();

echo "<script>alert('User deleted successfully!');</script>";
echo "<script>window.location.href = '../pages/control.php';</script>";

?>