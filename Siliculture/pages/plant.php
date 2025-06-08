<?php 
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

include '../../database/connection.php';
 
$select_data = $pdo->query("SELECT * FROM plant,germination WHERE plant.g_id = germination.g_id AND plant.p_status = 'unsend' ORDER BY p_id DESC");
$data = $select_data->fetchAll(PDO::FETCH_ASSOC);

$i = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forestry Management System | Trees</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/plant.css">
    <link rel="stylesheet" href="../css/delete-plant.css">
    <style>
        /* Success and error message styles */
        .message-container {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            
        }
        
    </style>
</head>
<body>
    <div class="message-backend">
                    <?php 
                // Display success message if there is one
                if (isset($_GET['update']) && $_GET['update'] == 'success') {
                    echo '<div class="message-container-box success-message-box">Tree information updated successfully!</div>';
                } elseif (isset($_GET['update']) && $_GET['update'] == 'no_changes') {
                    echo '<div class="message-container-box success-message-box">No changes were made to the tree information.</div>';
                } elseif (isset($_GET['error'])) {
                    echo '<div class="message-container-box error-message-box">An error occurred. Please try again.</div>';
                }
                ?>
    </div>
    <?php include '../menu/menu.php'; ?>
    
    <div class="main-content">
        
        <div class="dashboard-grid-saw">
            <div class="container">
                <h1>Plant Records</h1>
                
                
                
                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search tree entries...">
                        <!-- <span class="search-icon"> <i class="fa fa-search"></i> </span> -->
                    </div>
                    <div class="button-container">
                        <a href="add-tree.php" style="text-decoration: none;">
                            <button id="addStockBtn" class="add-btn">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Tree
                            </button>
                        </a>
                        <button id="deleteSelected" class="delete-btn" disabled>
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
                
                <table id="stockTable">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAll" class="checkbox">
                            </th>
                            <th class="row-id">ID</th>
                            <th>Name</th>
                            <th>DBH</th>
                            <th>Height</th>
                            <th>Health</th>
                            <th>Recorded</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row) { ?>
                            <tr data-id="<?php echo $row['p_id']; ?>">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="checkbox stock-checkbox">
                                </td>
                                <td class="row-id"><?php echo $i; $i++; ?></td>
                                <td><?php echo $row['plant_name']; ?></td>
                                <td><?php echo $row['DBH']; ?></td>
                                <td><?php echo $row['p_height']; ?></td>
                                <td><?php echo $row['health']; ?></td>
                                <td><?php echo $row['indate']; ?></td>
                                <td class="action-buttons">
                                    
                                    <button class="edit-btn"><i class="fa fa-pencil"></i> Edit</button>
                                    <div class="delete-action" onclick="deleteRow(this)">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal for Tree Details -->
    <div id="treeDetailsModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tree Details</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="treeDetailsContent">
                <!-- Content will be loaded here via AJAX -->
                <div class="loading-spinner">Loading...</div>
            </div>
            <div class="modal-footer">
                <button class="close-modal-btn">Close</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/view.js"></script>
    <!-- <script src="../js/delete-plant.js"></script> -->
</body>
</html>