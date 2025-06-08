

<?php

include '../../database/connection.php';
$id = $_GET['id'];

$delete = $pdo->prepare("DELETE FROM announcements WHERE an_id = :id");
$delete->bindParam(':id', $id);

if ($delete->execute()) {
    echo "<script>alert('Announcement deleted successfully!'); window.location.href='../pages/announcement.php';</script>";

} else {
    echo "<script>alert('Error deleting announcement.'); window.location.href='../pages/announcement.php';</script>";
}

?>