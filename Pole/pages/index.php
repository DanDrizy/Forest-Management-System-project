<?php

// Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('pole plant'); // Check if the user is logged in and has the required role
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .menu-item.active-dashboard {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
</style>
</head>
<body>
    <?php 
    
    include'../menu/menu.php';
    include'../../database/connection.php';

    $select_poles = $pdo->prepare("SELECT COUNT(*) FROM pole");
    $select_poles->execute();
    $poles_count = $select_poles->fetchColumn(); // Fetch the count of poles directly

    $timber = " SELECT COUNT(*)
        FROM timber
        INNER JOIN logs ON timber.l_id = logs.l_id
        INNER JOIN plant ON logs.p_id = plant.p_id
        INNER JOIN germination ON plant.g_id = germination.g_id
        WHERE t_amount > 0 AND del = 0 AND timber.status = 'unsend'";

    $select_timber = $pdo->prepare($timber);
    $select_timber->execute();
    $timber_count = $select_timber->fetchColumn(); // Fetch the count of timber directly

    $request = $pdo->prepare("SELECT COUNT(*) FROM request WHERE user = 'pole'");
    $request->execute();
    $requests = $request->fetchColumn();


    $rec = $pdo->prepare("SELECT COUNT(*) FROM received ");
    $rec->execute();
    $receive = $rec->fetchColumn();
    $rec_2 = $pdo->prepare("SELECT COUNT(*) FROM request WHERE user = 'sawmill'");
    $rec_2->execute();
    $receive_2 = $rec_2->fetchColumn();

    $title = $pdo->query("SELECT * FROM announcements WHERE type = 'pole plant' OR type = 'All' ") ; // Set the title for the page
    $fetch = $title->fetchAll(); // Fetch the title from the database


    
    
    
    ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $poles_count ?? 0; ?></div>
                    <div class="card-title">Recorded Poles</div>
                    <div class="card-subtitle">Pole that the Pole Manager Recorded</div>
                </div>
                <div style="display: flex; align-items: center;">
                    <span class="arrow-up">↑</span>
                    <span class="list-icon">≡</span>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $timber_count ?? 0; ?></div>
                    <div class="card-title">Available Timber</div>
                    <div class="card-subtitle">Available TImber for Poles</div>
                </div>
                <a href="received.php">
                    <div class="box-chart" style=" display: flex; align-items: center; justify-content: center; " >
                    <i class=" fa fa-tree "></i>
                </div>
                </a>
            </div>
            
            <div class="dashboard-card">
               
                    <div class="card-info">
                    <div class="card-value"> <?php echo $requests ?? 0; ?> </div>
                    <div class="card-title">Request</div>
                    <div class="card-amount">Request Timber to Sawmill</div>
                </div>
                 <a href="send-request.php">
                <div class="box-chart" style=" display: flex; align-items: center; justify-content: center; ">
                <i class="fa fa-send"></i>
                </div>
                </a>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $receive + $receive_2 ?? 0; ?></div>
                    <div class="card-title">Respond</div>
                    <div class="card-amount">Responds from Sawmill</div>
                </div>
                <a href="respond.php">
                    <div class="box-chart" style=" display: flex; align-items: center; justify-content: center; ">
                        <i class="fa fa-mail-forward"></i>
                    </div>
                </a>
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