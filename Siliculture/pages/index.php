<?php 

require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

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
        .icons
        {
            padding: 20px 20px;
            border-radius: 50%;
            background-color: transparent;
            border: 1px solid #00dc82;
        }
        .icons i
        {
            color: #00dc82;
            font-size: 40px;
        }
</style>
</head>
<body>
    <?php 
    
    include'../../database/connection.php';
    include'../menu/menu.php';

    $select = $pdo->query("SELECT COUNT(*) FROM germination WHERE g_status = 'unsend'");
    $germinationCount = $select->fetchColumn(); // Fetch the total number of unsent germination records

    $select_plant = $pdo->query("SELECT COUNT(*) FROM plant WHERE p_status = 'unsend'");
    $plantCount = $select_plant->fetchColumn(); // Fetch the total number of unsent germination records

    $select_logs = $pdo->query("SELECT COUNT(*) FROM logs WHERE l_status = 'unsend' OR l_status = 'unsend-sawmill'");
    $logsCount = $select_logs->fetchColumn(); // Fetch the total number of unsent germination records

    $select_logs_send = $pdo->query("SELECT COUNT(*) FROM logs WHERE l_status = 'send'");
    $logsCountSend = $select_logs_send->fetchColumn(); // Fetch the total number of unsent germination records

    $title = $pdo->query("SELECT * FROM announcements WHERE type = 'siliculture' OR type = 'All' ") ; // Set the title for the page
    $fetch = $title->fetchAll(); // Fetch the title from the database
    
    ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"> <?php echo $germinationCount ?? 0; ?> </div>
                    <div class="card-title">Germination seeds</div>
                    <div class="card-subtitle">Germination seed available</div>
                </div>
                <div class="icons" style="display: flex; align-items: center;">
                    <i class=" fa fa-leaf "></i>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"> <?php echo $plantCount ?? 0; ?></div>
                    <div class="card-title">Available Plants</div>
                    <div class="card-subtitle"> Plants that are in the Table </div>
                </div>
                <!-- <div class="box-chart" style=" display: flex; align-items: center; justify-content: center; " > -->
                    <div class="icons" style="display: flex; align-items: center;">
                    
                    <i class=" fa fa-tree "></i>
                    </div>
                
                <!-- </div> -->
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"> <?php echo $logsCount; ?> </div>
                    <div class="card-title">Harvested</div>
                    <div class="card-amount">These are plants that were harvested to be used</div>
                </div>
                <div class="box-chart" style=" display: flex; align-items: center; justify-content: center; ">
                <h1 style=" color: #00dc82;" >
                    <i class=" fa fa-pencil "></i>
                </h1>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $logsCountSend; ?></div>
                    <div class="card-title">Transfered Logs</div>
                    <div class="card-amount">The Logs that are transfered to sawmill</div>
                </div>
                <div class="box-chart" style=" display: flex; align-items: center; justify-content: center; ">
                <h1 style=" color: #00dc82;" >
                    <i class=" fa fa-shopping-cart "></i>
                </h1>
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