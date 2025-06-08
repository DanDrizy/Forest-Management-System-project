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
    <link rel="stylesheet" href="../css/register.css">
    <style>
        .menu-item.active-tree {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
</style>
<style></style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">
        <?php

        // Include the database connection file
        include_once '../../database/connection.php';
        
        $id = $_GET['id'];
        // Include the database connection file
        $select = $pdo->prepare("SELECT * FROM sawmill_trees_records WHERE tree_id = :id");
        $select->bindParam(':id', $id, PDO::PARAM_INT);
        $select->execute();
        $row = $select->fetch(PDO::FETCH_ASSOC);
        
        ?>

        
        
        <div class="dashboard-grid-saw-a">
        <div class="container">
        <div class="form-header">
            <h2>Update the Form</h2>
        </div>
        
        <form id="registrationForm" method="post" action=" ../backend/edit-tree-backend.php" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" id="id" name="id" value="<?php echo $row['tree_id']; ?>" hidden>
                <label for="name">Tree Name</label>
                <input type="text" id="name" name="name" placeholder="Enter Tree name" value="<?php echo $row['tree_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="provider">Provider</label>
                <input type="text" id="provider" name="provider" placeholder="Enter provider Tree Location" value="<?php echo $row['provider']; ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Status</label>
                <select id="location" name="status" required>
                    <option value="<?php echo $row['status']; ?>" selected hidden> <?php echo $row['status']; ?> </option>
                    <option value="Harvested">Harvested</option>
                    <option value="Inproccess">Inproccess</option>
                    <option value="Not yet">Not yet</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <select id="location" name="location" required>
                    <option value="<?php echo $row['location']; ?>" hidden selected><?php echo $row['location']; ?> </option>
                    <option value="New York">New York</option>
                    <option value="Los Angeles">Los Angeles</option>
                    <option value="Chicago">Chicago</option>
                    <option value="Houston">Houston</option>
                    <option value="Phoenix">Phoenix</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount" min="0" step="0.01" value="<?php echo $row['amount']; ?>" required>
            </div>
            
            <button type="submit" name="ok" class="btn">Update</button>
        </form>
        
        
    </div>

   


    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="../js/check.js"></script>
        </div>
        
    </div>
</body>
</html>