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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/register.css">
    <style>
        .menu-item.active-transfer {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }

        .text-option {
            color:rgb(220, 0, 99);
            font-weight: bold;
        }
   
</style>
<style></style>
</head>
<body>
    <?php 
    
    include'../menu/menu.php';
    
    include'../../database/connection.php';

    $id = $_GET['id'];


    $select_poleplant_record = $pdo->prepare("SELECT * FROM poleplant_transfer WHERE pole_transfer_id = :id ");

    $select_poleplant_record->bindParam(':id',$id);
    $select_poleplant_record->execute();
    $rows = $select_poleplant_record->fetchAll(PDO::FETCH_ASSOC);


    $select_poleplant_record_2 = $pdo->prepare("SELECT * FROM poleplant_transfer WHERE pole_transfer_id = :id");

    $select_poleplant_record_2->bindParam(':id',$id);
    $select_poleplant_record_2->execute();
    $poleplant_record_2 = $select_poleplant_record_2->fetchAll(PDO::FETCH_ASSOC);
    
    $i = 1;


    // $amount_format = number_format($poleplant_record_2[0]['amount'], 2, '.', '');
    $date_format = date('Y-m-d', strtotime($poleplant_record_2[0]['date']));
    
    ?>
    
     <div class="main-content">                            
        <div class="dashboard-grid-saw-a">         
            <div class="container">         
                <div class="form-header">             
                    <h2>Edit The Transfer</h2>             
                    <p>Please fill tree details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action=" ../backend/edit-transfer-backend.php" enctype="multipart/form-data">             
                    <div class="form-group">                 
                        <label>Name</label>                 
                        <select id="pole_id" name="pole_info" required>     
                            <?php foreach ($rows as $row) {
                            
                            $combine = $row['tree_name'].'|'.$row['pole_transfer_id'].'|'.$row['location'];
                                
                            ?>
                            <option value="<?php echo $combine; ?>"><?php echo $i.". "; $i++; ?> <?php echo $row['tree_name']." ------- <font class='text-option'>Available Measures:</font>". $row['volume']."". $row['measure']."------- <font class='text-option'>Available Amount:</font> : ". $row['amount']; ?> </option>
                            <?php } ?>                   
                                           
                        </select>             
                    </div>                          
                                          
                    <div class="form-group">                 
                        <label for="provider">Date</label>
                                         
                        <input type="date" id="provider" name="date" value="<?php echo $date_format; ?>" required>             
                    </div>              
                                             
                    <div class="form-group">                 
                        <label for="amount">Amount</label>                 
                        <input type="number" id="amount" name="amount" placeholder="Enter amount" min="0" step="0.01" value="<?php echo $poleplant_record_2[0]['amount']; ?>" required>             
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