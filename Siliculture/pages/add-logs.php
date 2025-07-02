<?php

// Include the database connection file
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.css" />
    <link rel="stylesheet" href="../../sawmill/css/register.css">     
    <style>  
    .main-content
    {
        overflow: auto
    }
    .main-content::-webkit-scrollbar {
        width: 8px;
    }       
    .main-content::-webkit-scrollbar-thumb {
        background: #00dc82;
        border-radius: 10px;
    }
    .main-content::-webkit-scrollbar-track {
        background: #000;
        border-radius: 10px;
    }
        .menu-item.active-logs {             
            background-color: rgba(255, 255, 255, 0.1);             
            color: white;             
            border-left: 3px solid #00dc82;         
        }
        
        /* Custom styles for the location autocomplete */
        .awesomplete {
            width: 100%;
            position: relative;
        }
        
        .awesomplete > ul {
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        
        .awesomplete > ul > li {
            padding: 10px 15px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
        }
        
        .awesomplete > ul > li:hover {
            background-color:rgb(228, 228, 228);
            color: #333;
        }
        
        .awesomplete mark {
            background: #00dc8233;
            color: #333;
            font-weight: bold;
            padding: 0;
        }
        
        .awesomplete li:hover mark {
            background: #00dc8233;
        }
        
        .awesomplete li[aria-selected="true"] {
            background-color:rgb(213, 212, 212);
            color: #333;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #00dc82;
            border-radius: 4px;
            resize: none;
        }
        textarea:focus {
            outline: none;
            border-color: #00dc82;
        }
        
    </style>
</head> 
<body>     
    <?php 
    include'../menu/menu.php';
    include'../../database/connection.php'; // Include the database connection file

     // SQL query to select all trees
    $query = "SELECT * FROM plant,germination WHERE plant.g_id=germination.g_id AND p_status = 'unsend' "; // Adjust the query as needed
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ?>          
    <div class="main-content">      
                             
        <div class="dashboard-grid-saw-a">         
            <div class="container">  
                       
                <div class="form-header">             
                    <h2>Logs Registeration Form</h2>             
                    <p>Please fill  details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action=" ../backend/add-logs-backend.php" enctype="multipart/form-data">             
                    <div class="form-group">                 
                        <div class="form-group">                 
                            <label for="tree_id">Tree Names</label>  


                            


                            <select name="p_id" id="">
                                
                                <?php foreach ($trees as $tree): 
                                    
                                    ?>

                                    <option value="<?php echo $tree['p_id']; ?>">
                                        <div class="div">
                                            <label for=""><?php echo $tree['plant_name']; ?></label>----------
                                            <span class="span">DBH: <?php echo $tree['DBH']; ?> cm</span>----------
                                            <span class="span">Health: <?php echo $tree['health']; ?></span>----------
                                            <span class="span">Height: <?php echo $tree['p_height']; ?> m</span>
                                        </div>
                                    </option>

                                <?php endforeach; ?>
                            </select>
                        </div>                          
                        <!-- <label for="name">Number of Logs & Height</label>                 
                        <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; ">                 
                        <input type="number" step="0.1" min="0" id="name" name="amount" placeholder="Logs Amount" required>             
                        <input type="number" step="0.1" min="0" id="name" name="height" placeholder="Height" required> 
                        </div>           -->
                    </div>                          
                    <label for="provider">LTs & Compartment</label>                 
                    <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; " >                 
                        <select name="lt">
                            <option hidden>---select----</option>
                            <option value="LT70 (1)">LT70 (1)</option>
                            <option value="LT70 (2)">LT70 (2)</option>
                            <option value="LT70 (3)">LT70 (3)</option>
                            <option value="LT40 (1)">LT40 (1)</option>
                            <option value="LT40 (2)">LT40 (2)</option>
                            <option value="LT40 (3)">LT40 (3)</option>
                            <option value="HR120 (1)">HR120 (1)</option>
                            <option value="HR120 (2)">HR120 (2)</option>
                            <option value="HR120 (3)">HR120 (3)</option>
                            <option value="EG200 (2)">EG200 (2)</option>
                        </select> 
                        <input type="text" name="compartment" id="" placeholder="Compartment" required>       
                    </div>  
                    <label for="provider">Comments <font style="color: gray; font-size: 12px;" >(optional)</font></label>                 

                    <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; " >                 
                        <textarea rows="6" name="comments" placeholder="Enter any Comment" ></textarea>     
                    </div>             
                    <div class="form-group">                 
                        <label for="status">inserted Date</label>                 
                        <input type="date" name="indate" id="" value="<?php echo date('Y-m-d'); ?>" readonly style=" background-color:rgb(228, 228, 228); color: #333; " >            
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