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
    <link rel="stylesheet" href=" https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/register.css">
    <style>
        .menu-item.active-logs {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }

        .text-option {
            color:rgb(220, 0, 99);
            font-weight: bold;
        }
        .pole-column
        {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }
   
</style>
<style></style>
</head>
<body>
    <?php  include'../menu/menu.php'; ?>
    
     <div class="main-content">                            
        <div class="dashboard-grid-saw-a">         
            <div class="container">         
                <div class="form-header">             
                    <h2>Add The Pole Plant</h2>             
                    <p>Please fill tree details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action=" ../backend/add-transfer-backend.php" enctype="multipart/form-data">             
                    <div class="form-group">    
                        <label>Pole Name</label>                 
                        <input type="text" id="amount" name="pole_name" placeholder="Enter Pole Name" min="0" step="0.01" required>             
                                   
                    </div>                          
                                          
                    <div class="form-group">                 
                        <label for="provider">Date & Location</label>                 
                        <div class="pole-column">
                            <input type="date" id="amount" name="date" placeholder="Enter Amount"  min="0" step="0.01" required> 
                            <input type="text" id="amount" name="location" placeholder="Enter Location" min="0" step="0.01" required>
                        </div>            
                    </div>              
                                             
                    <div class="form-group">                 
                        <label for="amount">Amount</label>                 
                        <div class="pole-column">
                            <input type="number" id="amount" name="amount" placeholder="Enter Amount" min="0" step="0.01" required> 
                            <input type="number" id="amount" name="height" placeholder="Enter Heigh" min="0" step="0.01" required> 
                        </div>            
                    </div>     
                    
                    
                    <div class="form-group">                 
                        <label for="amount" style=" color: grey; " >records in Date</label>                  
                        <input type="date" id="amount" name="record_date" value="<?php echo date('Y-m-d'); ?>"  placeholder="Enter amount" min="0" step="0.01" readonly style=" color: grey; " >             
                    </div> 
                    <button type="submit" name="ok" class="btn">Register</button>         
                </form>                       
            </div>            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.js"></script>
           <script src="../js/check.js"></script>
        </div>              
    </div> 
</body>
</html>