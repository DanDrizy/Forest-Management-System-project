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
    </style>
</head> 
<body>     
    <?php 
    include'../menu/menu.php';
    include'../../database/connection.php'; // Include the database connection file

    $id = $_GET['id']; // Get the ID from the URL
    $select_item = "SELECT * FROM logs_records WHERE log_id = :id"; // SQL query to select the item with the given ID
    $stmt = $pdo->prepare($select_item);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Bind the ID parameter
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
     // SQL query to select all trees
    $query = "SELECT * FROM sawmill_trees_records Where status = 'Harvested'"; // Adjust the query as needed
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ?>          
    <div class="main-content">                            
        <div class="dashboard-grid-saw-a">         
            <div class="container">         
                <div class="form-header">             
                    <h2>Logs Update Form</h2>             
                    <p>Please fill  details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action="../backend/edit-logs-backend.php" enctype="multipart/form-data">  
                    
                    <input type="number" name="log_id" value="<?php echo $item['log_id']; ?>" hidden > <!-- Hidden input to store the log ID -->
                
                    <div class="form-group">                 
                        <div class="form-group">                 
                            <label for="tree_id">Tree Names</label>  
                            <select name="tree_id" id="">
                                <?php foreach ($trees as $tree): 
                                
                                $query = "SELECT * FROM sawmill_trees_records WHERE  tree_id = '" . $item['tree_id'] . "' "; // Adjust the query as needed
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                $sawmill_trees = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                    
                                    
                                ?>
                                    
                                    <option value="<?php echo $item['tree_id']; ?>"><?php echo $sawmill_trees[0]['tree_name']; ?> <input type="text" name="tree_name" id="tree_name" value="<?php echo $sawmill_trees[0]['tree_name']; ?>" hidden > </option>


                                    
                                <?php endforeach; ?>
                            </select>
                        </div>                          
                        <label for="name">Number of Cutted Tree & date</label>                 
                        <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; ">                 
                        <input type="number" id="name" name="cutted" placeholder="Enter Here" value="<?php echo $item['cutted_trees']; ?>" required>             
                        <input type="date" id="name" name="date" placeholder="Enter Here" value="<?php echo $item['date']; ?>"  required>      
                        </div>          
                    </div>                          
                    <label for="provider">Volume & The Daimations</label>                 
                    <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; " >                 
                        <input type="number" id="provider" name="volume" placeholder="Enter Volume of The tree" value="<?php echo $item['volume']; ?>" required>             
                        <select name="measure" id="">
                            <option value="<?php echo $item['measure']; ?>" hidden selected> <?php echo $item['measure']; ?> </option>
                            <option value="cm">cm</option>   
                            <option value="mm">unity</option>
                            <option value="ft">ft</option>
                        </select>          
                    </div>              
                    <div class="form-group">                 
                        <label for="status">Location</label>                 
                        <input type="text" name="location" id="" placeholder="Enter location" value="<?php echo $item['location']; ?>" required>            
                    </div>                          
                                            
                    <button type="submit" name="edit" class="btn">Update</button>         
                </form>                       
            </div>            
            
        </div>              
    </div> 
</body> 
</html>