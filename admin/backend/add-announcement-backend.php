<?php

include '../../database/connection.php';

if(isset($_POST['ok'])){
    $title = $_POST['announcement-title'];
    $type = $_POST['announcement-type'];
    $date = $_POST['announcement-date'];
    $content = $_POST['announcement-content'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $pdo->prepare("INSERT INTO announcements (title, type, date, content) VALUES (:title, :type, :date, :content)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':content', $content);

    if ($stmt->execute()) {
        echo "<script>alert('Announcement added successfully!');</script>";
        echo "<script>window.location.href = '../pages/announcement.php';</script>";
    } else {
        echo "Error adding announcement.";
    }
} else {
    echo "Invalid request.";
}
?>