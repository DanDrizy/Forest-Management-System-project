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
    </style>
</head> 
<body>     
    <?php 
    include'../menu/menu.php';
    include'../../database/connection.php'; // Include the database connection file

    $id = $_GET['id']; // Get the id from the URL
    $p_id = $_GET['p_id']; // Get the p_id from the URL

     // SQL query to select all trees
    $query = "SELECT * FROM logs,plant,germination WHERE logs.p_id = plant.p_id AND plant.g_id = germination.g_id AND plant.p_id = $p_id "; // Adjust the query as needed
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $plants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // SQL query to select all trees
    $query = "SELECT * FROM logs,plant WHERE plant.p_id = logs.p_id AND logs.l_id = :id"; // Adjust the query as needed
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    ?>          
    <div class="main-content">      
    <?php include'../header/header.php'; ?>
                             
        <div class="dashboard-grid-saw-a">         
            <div class="container">  
                       
                <div class="form-header">             
                    <h2>Logs Update Form</h2>             
                    <p>Please fill  details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action=" ../backend/edit-logs-backend.php" enctype="multipart/form-data">             
                    <div class="form-group">                 
                        <div class="form-group">                 
                            <label for="tree_id">Tree Names</label>  


                            


                            <select name="p_id" id="">


                                <?php foreach ($plants as $tree): 
                                    
                                    // $combine = $tree['p_id']."|".$tree['p_name'];
                                    
                                    ?>

                                    <option value="<?php echo $tree['p_id']; ?>"><?php echo $tree['plant_name']; ?> </option>

                                <?php endforeach; ?>
                            </select>
                        </div>                          
                        <label for="name">Number of Cutted Tree & date</label>                 
                        <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; ">                 
                        <input type="number" id="name" value="<?php echo $tree['amount']; ?>" name="amount" placeholder="Enter Here" required>             
                        <input type="hidden" id="name" value="<?php echo $tree['l_id']; ?>" name="id" >             
                        <input type="date" id="name" name="date" value="<?php echo $tree['indate']; ?>" placeholder="Enter Here" required> 
                        </div>          
                    </div>                          
                    <label for="provider">Volume & The Daimations</label>                 
                    <div class="form-group" style=" display: flex; justify-content: space-between; gap: 1rem; " >                 
                        <input type="number" id="provider" value="<?php echo $tree['v2']; ?>" name="volume" placeholder="Enter Volume of The tree" required>             
                        <select name="measure" id="">
                            <option value="<?php echo $tree['v1']; ?>" hidden>old:  <?php echo $tree['v1']; ?> </option>
                            <option value="cm">new: cm</option>   
                            <option value="mm">new: unity</option>
                            <option value="ft">new: ft</option>
                        </select>          
                    </div>              
                    <div class="form-group">                 
                        <label for="status">inserted Date</label>                 
                        <input type="text" name="indate" id="" value="<?php echo date('Y-m-d'); ?>" readonly style=" background-color:rgb(228, 228, 228); color: #333; " >            
                    </div>                          
                                            
                    <button type="submit" name="ok" class="btn" >UPDATE</button>         
                </form>                       
            </div>            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.js"></script>
            
            <script src="../js/check.js"></script>
        </div>              
    </div> 
</body> 
</html>