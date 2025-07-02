<?php

// Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('sawmill'); // Check if the user is logged in and has the required role
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .menu-item.active-dashboard {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        .icon-box
        {
            padding: 20px 25px;
            border-radius: 50%;
            background-color: transparent;
            border: 1px solid #00dc82;
        }
        .icon-box i
        {
            color: #00dc82;
            font-size: 40px;
        }
</style>
</head>
<body>
    <?php 
    include'../menu/menu.php';
    include '../../database/connection.php'; // Include the database connection file
    
    $select_timber = $pdo->query("SELECT COUNT(*) as sumation FROM timber");
    $select_timber->execute(); // Execute the query to get the total number of timber records
    $select_timber_num = $select_timber->fetchColumn(); // Fetch the total number of timber records


    $select_logs = $pdo->query("SELECT COUNT(*) as sumation FROM logs WHERE l_status = 'sent'");
    $select_logs->execute(); // Execute the query to get the total number of timber records
    $select_logs_num = $select_logs->fetchColumn();

    $select_logs_del = $pdo->query("SELECT COUNT(*) as sumation FROM request WHERE read_status = '0' AND user = 'pole' ");
    $select_logs_del->execute(); // Execute the query to get the total number of timber records
    $select_logs_num_del = $select_logs_del->fetchColumn();


    $select_logs_tran = $pdo->query("SELECT COUNT(*) as sumation FROM timber WHERE status = 'send' AND t_amount > 0");
    $select_logs_tran->execute(); // Execute the query to get the total number of timber records
    $select_logs_num_tran = $select_logs_tran->fetchColumn();


    $title = $pdo->query("SELECT * FROM announcements WHERE type = 'sawmill' OR type = 'All' ") ; // Set the title for the page
    $fetch = $title->fetchAll(); // Fetch the title from the database

    
    
    ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $select_timber_num; ?></div>
                    <div class="card-title">Available Timber</div>
                    <div class="card-subtitle">The inserted trees</div>
                </div>
                <div class="icon-box" style="display: flex; align-items: center;">
                    <i class="fas fa-tree"></i>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $select_logs_num; ?></div>
                    <div class="card-title">Available Sent Harvest</div>
                    <div class="card-subtitle">Profit according to the records</div>
                </div>
                <div class="icon-box" style="display: flex; align-items: center;">
                    <i class="fas fa-cut"></i>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $select_logs_num_del; ?></div>
                    <div class="card-title">Un Read Pole Request</div>
                    <div class="card-amount">The Logs that sawmill Deleted</div>
                </div>
                <div class="icon-box" style="display: flex; align-items: center;">
                    <i class="fas fa-message"></i>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $select_logs_num_tran; ?></div>
                    <div class="card-title">Transfered Timber</div>
                    <div class="card-amount">The amount of the timber transfered to the Saler</div>
                </div>
                <div class="icon-box" style="display: flex; align-items: center;">
                    <i class="fas fa-tree"></i>
                </div>
            </div>
        </div>
        
        <?php foreach( $fetch as $data ){ ?>
        <div class="sales-message">
            <h2><?php echo $data['title']; ?></h2>
            <p>"<?php echo $data['content']; ?>"</p>
        </div>
        <?php } ?>
    </div>
</body>
</html>