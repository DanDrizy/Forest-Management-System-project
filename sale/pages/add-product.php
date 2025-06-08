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
    <link rel="stylesheet" href="../css/stockin.css">
    <link rel="stylesheet" href="">
    <style>

        .menu-item.active-stockin {
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

    ?>
   
    <div class="main-content">
        <h2 class="section-title">Product Registration</h2>
        
        <!-- Product Registration Form -->
        <div class="product-form">
            <form id="productForm" action="../backend/add-product-backend.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="product">Product</label>
                        <select id="measures" name="t_id" class="form-control" required>
                            
                        <?php

                            $select = $pdo->prepare("SELECT * from timber,logs,plant,germination WHERE  timber.l_id = logs.l_id AND logs.p_id = plant.p_id AND plant.g_id = germination.g_id AND timber.status = 'send' AND timber.t_amount > 0");
                            $select->execute();
                            $fetch = $select->fetchAll();

                            $i=1;
                            foreach ($fetch as $row) { 

                            $combine = $row['t_id'] . '|' .  $row['t_amount'];
                                
                            ?>

                            <option value="<?php echo $combine; ?>"><?php echo $i; $i++; ?>.  Tree Name: <?php echo $row['plant_name']; ?> ----- Amount: <?php echo $row['t_amount']; ?>----- Width: <?php echo $row['t_width']; ?> ----- Height: <?php echo $row['t_height']; ?>----- Size: <?php echo $row['size']; ?>----- Volume: <?php echo $row['t_volume']; ?> </option>
                                
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Categories</label>
                        <select name="category" id="" class="form-control" >
                            <option value="good">Good</option>
                            <option value="mediam">Mediam</option>
                            <option value="bad">Bad</option>
                        </select>
                    </div>
                    
                    
                    
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" placeholder="Enter any Amount of Money" id="price" name="price" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="location">Stockin Amount</label>
                        <input type="number" placeholder="Enter any Amount" id="location" name="stock_amount" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="inDate">Inserted Date</label>
                        <input type="date" id="inDate" name="inDate" value="<?php echo date('Y-m-d'); ?>" class="form-control" required readonly>
                    </div>
                </div>
                
                <div class="form-actions">
                    <!-- <button type="reset" class="btn">Clear</button> -->
                    <button type="submit" name="ok" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
        
        
        
    </div>
</body>
</html>