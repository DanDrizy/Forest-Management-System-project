<?php

    include'../../database/connection.php';

    $activation = $_GET['action'];
    $id = $_GET['id'];
    $combine = $_GET['combine'];

    // $date = date('Y-m-d');

// $combine = $row['tree_name'].'|'.$row['pole_id'].'|'.$row['location'].'|'.$row['amount'].''.$row['volume'].''.$row['measure'];

    // echo $combine;
    // list($tree_name, $pole_id, $location, $amount, $volume, $measure) = explode('|', $combine);

    // echo $tree_name;
    // echo $pole_id;
    // echo $location;
    // echo $amount;
    // echo $volume;
    // echo $measure;
    

    $select_sale_record = $pdo->prepare("SELECT * FROM poleplant_transfer WHERE pole_transfer_id = :id AND status = :activate ");
    $select_sale_record->bindParam(':id', $id);
    $select_sale_record->bindParam(':activate',$activation);
    $select_sale_record->execute();
    $fetch_sale_record = $select_sale_record->fetchAll();


    if($activation == 'active'){
        $update_sale_record = $pdo->prepare("UPDATE poleplant_transfer SET status = 'received' WHERE pole_transfer_id = :id");
        $update_sale_record->bindParam(':id', $id);
        $update_sale_record->execute();

       

        echo "<script> alert(' Activate Successfully!'); </script>";
        echo "<script>window.location.href = '../pages/product.php';</script>";

        
    }else if($activation == 'received'){
        $update_sale_record = $pdo->prepare("UPDATE poleplant_transfer SET status = 'active' WHERE pole_transfer_id = :id");
        $update_sale_record->bindParam(':id', $id);
        $update_sale_record->execute();

        echo "<script> alert(' Received Successfully! '); </script>";
        echo "<script>window.location.href = '../pages/product.php';</script>";
        
    } 

    


?>