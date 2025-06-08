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

    $select_stockin = $pdo->query("SELECT COUNT(*) FROM stockin WHERE s_amount > 0");
    $select_stockin->execute();
    $stockin_count = $select_stockin->fetchColumn();


    $select_stockout = $pdo->query("SELECT COUNT(*) FROM stockout WHERE out_amount > 0");
    $select_stockout->execute();
    $stockout_count = $select_stockout->fetchColumn();

    $select_profit = $pdo->query("SELECT 
    out.*,
    ins.*,
    SUM(out.out_price) as out_price,
    SUM(ins.price) as in_price,
	SUM(out.out_price - ins.price) as profit
    FROM stockout out
    LEFT JOIN stockin ins ON out.in_id = ins.in_id
    WHERE out.out_amount > 0");
    $select_profit->execute();
    $profit_data = $select_profit->fetchAll(PDO::FETCH_ASSOC);
    $total_profit = $profit_data[0]['profit'] ?? 0; // Ensure we have a default value if no data is returned
    $total_sumation = $profit_data[0]['out_price'] ?? 0; // Ensure we have a default value if no data is returned


    $title = $pdo->query("SELECT * FROM announcements WHERE type = 'sale' OR type = 'All' ") ; // Set the title for the page
    $fetch = $title->fetchAll(); // Fetch the title from the database

    
    
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
                    <div class="card-amount">Amount: $12,399,223,000</div>
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
                    <div class="card-amount">Amount: $203,112,554,810</div>
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
            <h2><?php echo $data['title']; ?></h2>
            <p>"<?php echo $data['content']; ?>"</p>
        </div>
        <?php } ?>
    </div>
</body>
</html>