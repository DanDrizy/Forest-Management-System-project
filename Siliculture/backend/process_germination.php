<?php

include'../../database/connection.php';

if(isset($_POST['germination'])){
    
    $tree_name = $_POST['tree_name'];
    $germination_date = $_POST['plant_date'];
    $germination_start_date = $_POST['germination_start'];
    $germination_end_date = $_POST['germination_end'];
    $number_seed = $_POST['num_seeds'];
    $soil = $_POST['soil_type'];
    $note = $_POST['comments'];

    $sql = "INSERT INTO germination (plant_name, curdate, g_sdate, g_edate, seeds, soil,g_note) VALUES ('$tree_name', '$germination_date', '$germination_start_date', '$germination_end_date', '$number_seed', '$soil', '$note')";
    $stm = $pdo->query($sql);

    echo"<script> alert('Record inserted successfully'); window.location.href='../pages/germ.php'; </script>";

    // if($pdo->query($sql) === TRUE){

    //     echo "Record inserted successfully";
    //     //header('Location: ../germination.php');
    // }else{

    //     echo "connection failed";

    // }

      

    
}



?>