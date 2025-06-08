<!DOCTYPE html>
<?php

// Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('pole plant'); // Check if the user is logged in and has the required role
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .menu-item.active-transfer {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
    </style>
    <style>
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
        }

        h1 {
            color: #2ea44f;
            margin-top: 0;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2ea44f;
            box-shadow: 0 0 5px rgba(46, 164, 79, 0.3);
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        button {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        button[type="submit"] {
            background-color: #2ea44f;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #2c974b;
        }

        button[type="reset"] {
            background-color: #f5f5f5;
            color: #333;
        }

        button[type="reset"]:hover {
            background-color: #e5e5e5;
        }

        .required:after {
            content: " *";
            color: #e32;
        }
    </style>
</head>

<body>
    <?php

    include '../menu/menu.php';
    include '../../database/connection.php'; // Include the database connection file

    $select_logs = $pdo->prepare("SELECT * FROM logs_records group by volume");
    $select_logs->execute();
    $logs = $select_logs->fetchAll(PDO::FETCH_ASSOC);

    // $select_measures = $pdo->prepare("SELECT * FROM logs_records group by volume");
    // $select_measures->execute();
    // $measures = $select_measures->fetchAll(PDO::FETCH_ASSOC);




    ?>

    <div class="main-content">



        <div>

            <!-- Form with properly structured select element and hidden fields -->
            <!-- Updated Form with Fixed SQL Query -->
<div class="container">
    <h1>Tree Harvest Registration</h1>
    <form id="harvestForm" method="post" action="../backend/add-transfer-backend.php">
        <div class="form-group">
            <label for="tree_selection" class="required">Tree Name -- Volume -- Location</label>
            <select name="log_id" id="tree_selection" required>
                <?php
                // Updated query to get all relevant fields and not group by volume
                $select_logs = $pdo->prepare("SELECT * FROM logs_records WHERE cutted_trees > 0");
                $select_logs->execute();
                $logs = $select_logs->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($logs as $log) { 
                ?>
                    <option value="<?php echo $log['log_id']; ?>">
                        <?php echo $log['tree_name'] . ' -- ' . 
                                  $log['volume'] . ' ' . $log['measure'] . ' -- ' . 
                                  $log['location'] . ' (Available: ' . $log['cutted_trees'] . ')'; 
                        ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="amount_input" class="required">Amount Logs</label>
            <input type="number" id="amount_input" name="amount" placeholder="Enter Here" required min="1">
        </div>

        <div class="form-group">
            <label for="harvestDate" class="required">Recorded Date</label>
            <input type="date" id="harvestDate" name="date" readonly>
        </div>

        <!-- Hidden fields to store additional data -->
        <input type="hidden" id="selected_tree_name" name="tree_name" value="">
        <input type="hidden" id="selected_tree_id" name="tree_id" value="">
        <input type="hidden" id="selected_measure" name="measure" value="">
        <input type="hidden" id="selected_volume" name="volume" value="">
        <input type="hidden" id="selected_location" name="location" value="">

        <div class="button-group">
            <button type="reset" id="clearBtn">Clear Form</button>
            <button type="submit" name="ok">Register Harvest</button>
        </div>
    </form>
</div>

<script>
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('harvestDate').value = today;

    // Function to update hidden fields when selection changes
    function updateHiddenFields() {
        const selectElement = document.getElementById('tree_selection');
        if (!selectElement || selectElement.options.length === 0) return;
        
        // Get the selected option
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        // Get the log_id (value of the option)
        const logId = selectElement.value;
        
        // Store the log_id in the tree_id hidden field (they appear to be used interchangeably)
        document.getElementById('selected_tree_id').value = logId;
        
        // Parse the text content to extract values
        const text = selectedOption.textContent.trim();
        const mainParts = text.split(' (Available:')[0]; // Remove the available count
        const parts = mainParts.split(' -- ');
        
        if (parts.length >= 3) {
            // Update hidden fields
            document.getElementById('selected_tree_name').value = parts[0].trim();
            
            // Handle the measure part (which contains volume and unit)
            const measureParts = parts[1].trim().split(' ');
            if (measureParts.length >= 2) {
                document.getElementById('selected_volume').value = measureParts[0].trim();
                document.getElementById('selected_measure').value = measureParts[1].trim();
            }
            
            // Set location
            document.getElementById('selected_location').value = parts[2].trim();
        }
    }

    // Add event listener to the select element
    document.getElementById('tree_selection').addEventListener('change', updateHiddenFields);

    // Handle the reset button to ensure hidden fields are updated after reset
    document.getElementById('clearBtn').addEventListener('click', function() {
        setTimeout(updateHiddenFields, 10); // Small delay to ensure form reset completes
    });

    // Trigger on page load to set initial values
    document.addEventListener('DOMContentLoaded', function() {
        updateHiddenFields();
    });
</script>



        </div>
</body>

</html>