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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.css" />
    <link rel="stylesheet" href="../css/register.css">   
    <link rel="stylesheet" href="../../sawmill/css/timber.css">  
    
</head> 
<body>     
    <?php 
    include'../menu/menu.php';
    include'../../database/connection.php'; // Include the database connection file

     // SQL query to select all trees
    $query = "SELECT * FROM logs,plant,germination WHERE logs.p_id = plant.p_id AND plant.g_id = germination.g_id AND l_status = 'sent' AND amount > 0  "; // Adjust the query as needed
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ?>          
    <div class="main-content">                            
        <div class="dashboard-grid-saw-a">         
            <div class="container">         
                <div class="form-header">             
                    <h2>Timber Registeration Form</h2>             
                    <p>Please fill  details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action=" ../backend/add-logs-backend.php" enctype="multipart/form-data">             
                    <div class="form-group">                 
                        <label class="names-info" for="tree_id"> <p>Harvest Names</p></label>  
                        <div class="form-group"  style=" display: flex; justify-content: space-between; gap: 1rem; " >                 


                            <select name="l_id" id="">
                                <option value="" hidden> Select Harvest</option>
                                
                                <?php foreach ($trees as $tree): 
                                    
                                    
                                    ?>

                                    <option value="<?php echo $tree['l_id']; ?>">Harvest Name: <?php echo $tree['plant_name']; ?> --- Harvested Date: <?php echo $tree['l_indate']; ?> </option>

                                <?php endforeach; ?>
                            </select>          

                        </div>                          
                        <label class="names-info" for="name"> <p>Amount Needed</p> <p>Type of Timber</p> </label>                 
                        <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; ">                 
                        <input type="number" min="0" id="name" name="amount" placeholder="Amount" required>             
                        <input type="text" id="name" name="type" placeholder="Type" required>      
                        </div>          
                    </div>                          
                    <label class="names-info" for="provider"> <p>Height</p> <p>Width</p> </label>              
                    <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; " >                 
                        <input type="number" id="provider" name="height" placeholder="Height" required>             
                        <input type="number" id="provider" name="width" placeholder="Width" required>             
                                 
                    </div>              
                    <!-- <label class="names-info" for="status"> <p>Size(optional)</p> <p>Volume</p> </label>                 
                    <div class="form-group"  style=" display: flex; justify-content: space-between; gap: 1rem; ">                 
                        <input type="text" name="size" id="" placeholder="Enter Size">            
                        <input type="text" name="volume" id="" placeholder="Volume">            
                    </div>      -->
                    <label class="names-info" for="status"> <p>Note(Optional)</p></label>                 
                    <div class="form-group"  style=" display: flex; justify-content: space-between; gap: 1rem; ">                 
                        <textarea name="note" id="" rows="4" placeholder="Enter note"></textarea>        
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