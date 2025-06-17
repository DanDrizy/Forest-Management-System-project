<?php

require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('sales'); // Check if the user is logged in and has the required role

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
    include '../../database/connection.php';

    // Get stockin count - MySQL compatible
    $select_stockin = $pdo->prepare("SELECT COUNT(*) FROM stockin WHERE s_amount > 0");
    $select_stockin->execute();
    $stockin_count = $select_stockin->fetchColumn();

    // Get stockout count - MySQL compatible
    $select_stockout = $pdo->prepare("SELECT COUNT(*) FROM stockout WHERE out_amount > 0");
    $select_stockout->execute();
    $stockout_count = $select_stockout->fetchColumn();

    // Get profit data - MySQL compatible with proper aggregation
    $select_profit = $pdo->prepare("SELECT 
        COALESCE(SUM(outs.out_price), 0) as out_price,
        COALESCE(SUM(ins.price), 0) as in_price,
        COALESCE(SUM(outs.out_price - ins.price), 0) as profit
    FROM stockout outs LEFT JOIN stockin ins ON outs.in_id = ins.in_id
    WHERE outs.out_amount > 0");
    $select_profit->execute();
    $profit_data = $select_profit->fetch(PDO::FETCH_ASSOC);
    
    // Set default values with proper null handling
    $total_profit = $profit_data['profit'] ?? 0;
    $total_sumation = $profit_data['out_price'] ?? 0;

    // Get announcements - MySQL compatible
    $title = $pdo->prepare("SELECT * FROM announcements WHERE type = 'sale' OR type = 'All' ORDER BY date DESC LIMIT 2");
    $title->execute();
    $fetch = $title->fetchAll(PDO::FETCH_ASSOC);
    
    ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $stockin_count ?? 0; ?></div>
                    <div class="card-title">STOCK-IN</div>
                    <div class="card-subtitle">The inserted trees</div>
                </div>
                <div style="display: flex; align-items: center;">
                    <span class="arrow-up">↑</span>
                    <span class="list-icon">≡</span>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo $stockout_count; ?></div>
                    <div class="card-title">STOCK-OUT</div>
                    <div class="card-subtitle">Profit according to the records</div>
                </div>
                <div class="box-chart">
                    <div class="box-segment"></div>
                    <div class="box-segment"></div>
                    <div class="box-segment"></div>
                    <div class="box-segment empty"></div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"> <?php echo number_format($total_sumation); ?> Rwf </div>
                    <div class="card-title">Wallet</div>
                    <div class="card-amount">Money that you have in your wallet</div>
                </div>
                <div class="box-chart">
                    <div class="box-segment empty"></div>
                    <div class="box-segment"></div>
                    <div class="box-segment empty"></div>
                    <div class="box-segment empty"></div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <div class="card-info">
                    <div class="card-value"><?php echo number_format($total_profit); ?> Rwf</div>
                    <div class="card-title">Profit</div>
                    <div class="card-amount">The profit that you gain in stockout</div>
                </div>
                <div class="box-chart">
                    <div class="box-segment empty"></div>
                    <div class="box-segment empty"></div>
                    <div class="box-segment empty"></div>
                    <div class="box-segment" style="border-right: 3px solid #1d3741;"></div>
                </div>
            </div>
        </div>
        
       <?php foreach( $fetch as $data ){ ?>
        <div class="sales-message">
            <h2><?php echo htmlspecialchars($data['title']); ?></h2>
            <p>"<?php echo htmlspecialchars($data['content']); ?>"</p>
        </div>
        <?php } ?>
    </div>
</body>
</html>