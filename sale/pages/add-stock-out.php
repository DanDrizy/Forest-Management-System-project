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
        .menu-item.active-stockin
        {
            background: transparent;
            border:transparent;
        

        }
        .menu-item.active-stockout {

            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;

        }

        textarea {

            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
        }

        textarea:focus,
        input:focus,
        select:focus {
            outline: none;
            border-color: #00dc82;
        }

        input[type=date] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            background: lightgray;
            color: rgba(57, 57, 57, 0.97);
        }
    </style>


</head>

<body>
    <?php

    include '../menu/menu.php';
    include '../../database/connection.php';

    $select = $pdo->prepare("SELECT * from stockin, timber, logs, plant, germination WHERE stockin.t_id = timber.t_id AND timber.l_id = logs.l_id AND logs.p_id = plant.p_id AND plant.g_id = germination.g_id AND timber.status = 'send' AND stockin.s_amount > 0");
    $select->execute();
    $fetch = $select->fetchAll();

    $i = 1;

    ?>

    <div class="main-content">
        <h2 class="section-title">Stock out Registration</h2>

        <!-- Product Registration Form -->
        <div class="product-form">
            <form id="productForm" action="../backend/add-stockout-backend.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="product">Product Information </label>
                        <select id="measures" name="info" class="form-control" required>
                            <?php foreach ($fetch as $row) {
                                $combine = $row['s_amount'] . "|" . $row['in_id']; 
                                
                                if($row['s_amount'] <= 0) {   continue; } 
                                
                                ?>




                                <option value="<?php echo $combine; ?>">
                                    <?php echo $i; $i++; ?>. 
                                    Tree Name: <?php echo $row['plant_name']; ?> -------
                                    Available amount: <?php echo $row['s_amount']; ?> ------- 
                                    Measures: H: <?php echo $row['t_height']; ?>cm 
                                    W: <?php echo $row['t_width']; ?>cm ------- 
                                    Price: <?php echo $row['price']; ?> Rwf
                                </option>

                            <?php } ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="location">Stockout Amount</label>
                        <input type="number" placeholder="Enter any Amount" id="location" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Buy Price</label>
                        <input type="number" placeholder="Enter any Price" id="price" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Additiona Information <font style=" color: grey; " >(optional)</font></label>
                        <textarea name="note" placeholder="Enter Notes"></textarea>
                    </div>


                    <div class="form-group">
                        <label for="inDate">Inserted Date</label>
                        <input type="date" id="inDate" name="date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="ok" class="btn btn-primary">Save Stock-out</button>
                </div>
            </form>
        </div>



    </div>
</body>

</html>