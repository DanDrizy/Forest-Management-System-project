<?php

include'../../database/connection.php'; // Include the database connection file

$id = $_GET['id']; // Get the ID from the URL
$select_sawmill_transfer = $pdo->query("SELECT * from sawmill_transfer_records WHERE tranfer_id = '$id'");
$select_sawmill_transfer->execute();
$fetch_sawmill_transfer = $select_sawmill_transfer->fetchAll(PDO::FETCH_ASSOC);

$send = $fetch_sawmill_transfer[0]['send'];
// Check if the record exists
if (count($fetch_sawmill_transfer) > 0) {
    // If the record exists, display the data
    $tree_name = $fetch_sawmill_transfer[0]['tree_name'];
    $amount = $fetch_sawmill_transfer[0]['amount'];
    $volume = $fetch_sawmill_transfer[0]['volume'];
    $measure = $fetch_sawmill_transfer[0]['measure'];
    $location = $fetch_sawmill_transfer[0]['location'];
    $date = $fetch_sawmill_transfer[0]['date'];
    $tree_id = $fetch_sawmill_transfer[0]['tree_id'];
    $tranfer_id = $fetch_sawmill_transfer[0]['tranfer_id'];
    } else {
        // If the record does not exist, display an error message
        echo "<script>alert('No record found with the given ID.');</script>";
        echo "<script>window.location.href = '../pages/received.php';</script>";
        exit; // Stop further execution
        }


if($send == 'active')
{
    $sawmill_transfer_update = $pdo->query("UPDATE sawmill_transfer_records SET send = 'received' WHERE tranfer_id = '$id'");
    $sawmill_transfer_update->execute();

    $insert_pole = $pdo->prepare("INSERT INTO poleplant_records (tree_name, amount, volume, measure, location, date, tree_id, tranfer_id) VALUES (:tree_name, :amount, :volume, :measure, :location, :date, :tree_id, :tranfer_id)");
    $insert_pole->bindParam(':tree_name', $tree_name);
    $insert_pole->bindParam(':amount', $amount);
    $insert_pole->bindParam(':volume', $volume);
    $insert_pole->bindParam(':measure', $measure);
    $insert_pole->bindParam(':location', $location);
    $insert_pole->bindParam(':date', $date);
    $insert_pole->bindParam(':tree_id', $tree_id);
    $insert_pole->bindParam(':tranfer_id', $tranfer_id);
    $insert_pole->execute();

    echo "<script>alert('The log has been received successfully!');</script>";
    echo "<script>window.location.href = '../pages/received.php';</script>";

}
else if($send == 'received')
{
    $sawmill_transfer_update = $pdo->query("UPDATE sawmill_transfer_records SET send = 'active' WHERE tranfer_id = '$id'");
    $sawmill_transfer_update->execute();
    $delete_pole = $pdo->query("DELETE FROM poleplant_records WHERE tranfer_id = '$tranfer_id'");
    $delete_pole->execute();
    echo "<script>alert('The log has set Proccessing successfully!');</script>";
    echo "<script>window.location.href = '../pages/received.php';</script>";
}else{
    echo "<script>alert('The log has not been sent!');</script>";
    echo "<script>window.location.href = '../pages/received.php';</script>";
}


?>