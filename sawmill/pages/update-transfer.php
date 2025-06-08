<!DOCTYPE html>
<?php

// Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('sawmill'); // Check if the user is logged in and has the required role
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/update_transfer.css">
    <style>
        .menu-item.active-transfer {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
    </style>
    <style></style>
</head>

<body>
    <?php

    include '../menu/menu.php';
    include '../../database/connection.php'; // Include the database connection file

    $select_logs = $pdo->prepare("SELECT * FROM logs_records group by volume");
    $select_logs->execute();
    $logs = $select_logs->fetchAll(PDO::FETCH_ASSOC);

    

    $id = $_GET['id']; 
    
                // Get the Available Amount 
                $select_logs_update = $pdo->prepare("SELECT * FROM logs_records WHERE log_id = :id");
                $select_logs_update->bindParam(':id', $id, PDO::PARAM_INT);
                $select_logs_update->execute();
                $log_update = $select_logs_update->fetch(PDO::FETCH_ASSOC);



                $select_transfer = $pdo->prepare("SELECT * FROM sawmill_transfer_records WHERE tranfer_id = :id");
                $select_transfer->bindParam(':id', $id, PDO::PARAM_INT);
                $select_transfer->execute();
                $transfer = $select_transfer->fetch(PDO::FETCH_ASSOC);

               




    ?>

    <div class="main-content">



        <div>

            
<div class="container">
    <h1>Tranfer Logs Registration</h1>
    <form method="POST" action="../backend/edit-transfer-backend.php">
    <div class="form-group">
        <label for="tree_selection" class="required">Tree Name -- Volume -- Location</label>
        <select name="tranfer_id" id="tree_selection" required>
            <option value="<?php echo $transfer['tranfer_id']; ?>" selected hidden>
                <?php echo $transfer['tree_name'] . ' -- ' . 
                          $transfer['volume'] . ' ' . $transfer['measure']; 
                ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="amount_input" class="required">Amount Logs</label>
        <input type="number" name="tranfer_id" value="<?php echo $transfer['tranfer_id']; ?>" hidden>
        <input type="number" name="tree_id" value="<?php echo $transfer['tree_id']; ?>" hidden>
        <input type="number" name="old_amount" value="<?php echo $transfer['amount']; ?>" hidden>
        <input type="number" name="log_amount" value="<?php echo $log_update[0]['log_amount']; ?>" hidden>
        <input type="number" id="amount_input" name="new_amount" placeholder="Enter Here" required min="1" value="<?php echo $transfer['amount']; ?>">
    </div>

    <div class="form-group">
        <label for="harvestDate" class="required">Recorded Date</label>
        <input type="date" id="harvestDate" name="date" value="<?php echo $transfer['date']; ?>" readonly>
    </div>

    <div class="button-group">
        <a href="transfer.php"><button type="button" id="clearBtn"> <i class="fa fa-arrow-circle-left"> </i> Back</button></a>
        <button type="submit" name="update">Register Transfer</button>
    </div>
</form>
</div>




        </div>
</body>

</html>