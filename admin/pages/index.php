
<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('admin'); // Check if the user is logged in and has the required role
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Forestry Management System</title>
    <style></style>
</head>
<body>
    <style>
    .menu-item.active-dash svg
    {
        color: #00ff9d;

    }

    .menu-item.active-dash 
    {
    border-left: 3px solid #00ff9d;
    color: white;
    background-color: rgba(0, 255, 157, 0.1);
    }
    .icons
    {
        padding: 20px 25px;
        border-radius: 50%;
        background-color: #00ff9d;
    }
    .icons i
    {
        color: #000000;
        font-size: 40px;
    }


    </style>
<?php

include '../../database/connection.php'; // Include the database connection
include'../menu/menu.php';
$user = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $user->fetchColumn(); // Fetch the total number of users

$user_1 = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' ");
$userCount_1 = $user_1->fetchColumn(); 

$user_2 = $pdo->query("SELECT COUNT(*) FROM timber");
$userCount_2 = $user_2->fetchColumn(); 

$user_3 = $pdo->query("SELECT COUNT(*) FROM stockout ");
$userCount_3 = $user_3->fetchColumn(); 

?>
    
    <div class="main-content">
        <?php  include '../header/header.php';   ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number">
                        <?php echo $userCount ?? 0; ?>
                    </div>
                    <div class="stat-label">Users</div>
                    <div class="stat-sublabel">The inserted Users</div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;" class="icons" >
                    <i class="fa fa-user" ></i>
                    </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $userCount_1 ?? 0; ?></div>
                    <div class="stat-label">Activated User</div>
                    <div class="stat-sublabel">The inserted trees</div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 10px; border: 1px solid #00ff9d; background: transparent;" class="icons" >
                    <i class="fa fa-user" style=" color: #00ff9d; " ></i>
                </div>

            </div>
            
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number"> <?php echo $userCount_2 ?? 0; ?> </div>
                    <div class="stat-label">Available Timber</div>
                    <div class="stat-sublabel">The inserted trees</div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; border: 1px solid #00ff9d; background: transparent;" class="icons" >
                    <i class="fa fa-tree" style=" color: #00ff9d; " ></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number"><?php echo $userCount_3 ?? 0; ?></div>
                    <div class="stat-label">Saled Product</div>
                    <div class="stat-sublabel">The inserted trees</div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; border: 1px solid #00ff9d; background: transparent;" class="icons" >
                    <i class="fa fa-tags" style=" color: #00ff9d; " ></i>
                </div>
            </div>
            
        </div>
        <?php 
        include '../../database/connection.php';
        
        
        $result = $pdo->query("SELECT * FROM announcements Where type = 'All'");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        
        ?>
        <div class="message-board">
            <div class="message-title"><?php echo $row['title']; ?></div>
            <div class="message-content">
                "<?php echo $row['content']; ?>"
            </div>
            <div class="message-signature">"<?php echo $row['type']; ?>"</div>
        </div>
        
        <?php 
        include '../../database/connection.php';
        
        
        $result = $pdo->query("SELECT * FROM announcements Where type = 'admin'");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $check = $result->rowCount();
        
        if($row){ ?>
            


            <div class="message-board"> 

            <div class="message-title"><?php echo $row['title']; ?></div>
            <div class="message-content">
                "<?php echo $row['content']; ?>"
            </div>
            <div class="message-signature">"<?php echo $row['type']; ?>"</div>
        </div>
        <?php }else{ ?>

            <?php } ?>
    </div>
</body>
</html>