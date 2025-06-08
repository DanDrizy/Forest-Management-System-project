<?php
try{
    $pdo = new PDO('sqlite:' . __DIR__ . '/fms_database.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo $e->getMessage();
}
?>