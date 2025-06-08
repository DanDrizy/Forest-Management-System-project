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
    <link rel="stylesheet" href="../css/table.css">
    <style>
        .menu-item.active-logs {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        
        /* Styles for confirmation dialog */
        .confirmation-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .confirmation-box {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
        }
        
        .confirmation-box h3 {
            margin-top: 0;
        }
        
        .confirmation-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .confirm-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .cancel-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Edit Dialog Styles */
        .edit-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .edit-box {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .edit-box h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #00dc82;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #00dc82;
            box-shadow: 0 0 5px rgba(0, 220, 130, 0.3);
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .edit-buttons {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .save-btn {
            background-color: #00dc82;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .save-btn:hover {
            background-color: #00b86f;
        }

        .dashboard-grid-saw
        {
            overflow-y: auto;
        }
        .dashboard-grid-saw::-webkit-scrollbar {
            width: 8px;
        }       
        .dashboard-grid-saw::-webkit-scrollbar-thumb {
            background:rgba(162, 162, 162, 0.62);
            border-radius: 10px;
        }
        .dashboard-grid-saw::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }

        /* No results message */
        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php 
    
    include'../header/header.php'; 
    include '../../database/connection.php'; // Include the database connection file
    
    $select_item = "SELECT * from timber,logs,plant,germination WHERE timber.l_id = logs.l_id AND logs.p_id = plant.p_id AND plant.g_id = germination.g_id  And timber.t_amount > 0"; // SQL query to select all items from the database
    
    
    $stmt = $pdo->prepare($select_item);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $i = 1; // Initialize the counter for the ID column


    ?>

        
        
    <div class="dashboard-grid-saw">
        <div class="container">
        <h1>Timber Records</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search stock entries...">
                <!-- <span class="search-icon"> <i class="fa fa-search"></i> </span> -->
            </div>
            <div class="button-container" >
                <a href="add-logs.php" style=" text-decoration: none; ">
                <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Logs
                </button>
                </a>
                <button id="deleteAllBtn" class="delete-btn">
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
                    <!-- <th class="checkbox-cell">
                        <input type="checkbox" id="selectAll" class="checkbox">
                    </th> -->
                    <th class="row-id">ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Height</th>
                    <th>Width</th>
                    <th>Size</th>
                    <th>Volume</th>
                    <th>Location</th>
                    <th>Recorded</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach ($items as $item): 
                
                ?>
                <tr data-id="<?php echo $item['t_id']; ?>" class="table-row">
                    <!-- <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox stock-checkbox">
                    </td> -->
                    <td class="row-id"> <?php echo $i; $i++; ?> </td>
                    <td class="searchable"> <?php echo $item['plant_name']; ?> </td>
                    <td class="searchable"> <?php echo $item['t_amount']; ?> </td>
                    <td class="searchable"> <?php echo $item['type']; ?> </td>
                    <td class="searchable"> <?php echo $item['t_height']?> </td>
                    <td class="searchable"> <?php echo $item['t_width']; ?> </td>
                    <td class="searchable"> <?php echo $item['size']; ?> </td>
                    <td class="searchable"> <?php echo $item['t_volume']; ?> </td>
                    <td class="searchable"> <?php echo $item['t_location']; ?> </td>
                    <td class="searchable"> <?php echo $item['t_indate']; ?> </td>
                    <td class="action-buttons">
                        
                        <?php if($item['status'] == 'send') { ?>
                        
                            <button class="edit-btn">
                            <i class=" fa fa-remove " ></i>
                            Not
                        </button>
                        </a>

                        <?php } else { ?>
                           <button class="edit-btn edit-action" 
                                   data-id="<?php echo $item['t_id']; ?>"
                                   data-name="<?php echo htmlspecialchars($item['plant_name']); ?>"
                                   data-amount="<?php echo $item['t_amount']; ?>"
                                   data-type="<?php echo htmlspecialchars($item['type']); ?>"
                                   data-height="<?php echo $item['t_height']; ?>"
                                   data-width="<?php echo $item['t_width']; ?>"
                                   data-size="<?php echo $item['size']; ?>"
                                   data-volume="<?php echo $item['t_volume']; ?>"
                                   data-location="<?php echo htmlspecialchars($item['t_location']); ?>">
                            <i class="fa fa-pencil-alt" ></i>
                            Edit
                        </button>
                         <?php } ?>
                        
                        <button class="delete-single-btn delete-action" data-id="<?php echo $item['t_id']; ?>" style=" border: none; " >
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                           

                    </td>
                </tr>
                <?php
             endforeach; ?>
                
            </tbody>
        </table>
        
        <!-- No results message -->
        <div id="noResults" class="no-results" style="display: none;">
            No records found matching your search criteria.
        </div>
    </div>

    <!-- Edit Dialog -->
    <div id="editDialog" class="edit-dialog">
        <div class="edit-box">
            <h3>Edit Timber Record</h3>
            <form id="editForm">
                <input type="hidden" id="editRecordId" name="record_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editName">Plant Name:</label>
                        <input type="text" id="editName" name="plant_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editType">Type:</label>
                        <input type="text" id="editType" name="type" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editAmount">Amount:</label>
                        <input type="number" id="editAmount" name="amount" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="editSize">Size:</label>
                        <input type="number" id="editSize" name="size" min="0" step="0.01" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editHeight">Height:</label>
                        <input type="number" id="editHeight" name="height" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editWidth">Width:</label>
                        <input type="number" id="editWidth" name="width" min="0" step="0.01" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editVolume">Volume:</label>
                        <input type="number" id="editVolume" name="volume" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editLocation">Location:</label>
                        <input type="text" id="editLocation" name="location" required>
                    </div>
                </div>
                
                <div class="edit-buttons">
                    <button type="button" class="cancel-btn" onclick="closeEditDialog()">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Dialog for Single Delete -->
    <div id="singleDeleteDialog" class="confirmation-dialog">
        <div class="confirmation-box">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this record?</p>
            <div class="confirmation-buttons">
                <button id="confirmSingleDelete" class="confirm-btn">Yes, Delete</button>
                <button class="cancel-btn" onclick="closeSingleDeleteDialog()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Dialog for Delete All -->
    <div id="deleteAllDialog" class="confirmation-dialog">
        <div class="confirmation-box">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete all selected records?</p>
            <div class="confirmation-buttons">
                <button id="confirmDeleteAll" class="confirm-btn">Yes, Delete All</button>
                <button class="cancel-btn" onclick="closeDeleteAllDialog()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="../js/check.js"></script>
    <script>
        // Variables to store the ID to delete
        let recordIdToDelete = null;

        // Real-time search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#tableBody .table-row');
            const noResultsDiv = document.getElementById('noResults');
            let visibleRows = 0;

            tableRows.forEach(row => {
                const searchableText = Array.from(row.querySelectorAll('.searchable'))
                    .map(cell => cell.textContent.toLowerCase())
                    .join(' ');

                if (searchableText.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            if (visibleRows === 0 && searchTerm !== '') {
                noResultsDiv.style.display = 'block';
            } else {
                noResultsDiv.style.display = 'none';
            }
        });

        // Edit functionality
        function openEditDialog(recordData) {
            document.getElementById('editRecordId').value = recordData.id;
            document.getElementById('editName').value = recordData.name;
            document.getElementById('editAmount').value = recordData.amount;
            document.getElementById('editType').value = recordData.type;
            document.getElementById('editHeight').value = recordData.height;
            document.getElementById('editWidth').value = recordData.width;
            document.getElementById('editSize').value = recordData.size;
            document.getElementById('editVolume').value = recordData.volume;
            document.getElementById('editLocation').value = recordData.location;
            
            document.getElementById('editDialog').style.display = 'flex';
        }
        
        function closeEditDialog() {
            document.getElementById('editDialog').style.display = 'none';
        }

        // Function to open the single delete confirmation dialog
        function openSingleDeleteDialog(recordId) {
            recordIdToDelete = recordId;
            document.getElementById('singleDeleteDialog').style.display = 'flex';
        }

        // Function to close the single delete confirmation dialog
        function closeSingleDeleteDialog() {
            recordIdToDelete = null;
            document.getElementById('singleDeleteDialog').style.display = 'none';
        }

        // Function to open the delete all confirmation dialog
        function openDeleteAllDialog() {
            document.getElementById('deleteAllDialog').style.display = 'flex';
        }

        // Function to close the delete all confirmation dialog
        function closeDeleteAllDialog() {
            document.getElementById('deleteAllDialog').style.display = 'none';
        }

        // Add event listener to each edit and delete button
        document.addEventListener('DOMContentLoaded', function() {
            // Edit buttons
            const editButtons = document.querySelectorAll('.edit-action');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const recordData = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        amount: this.getAttribute('data-amount'),
                        type: this.getAttribute('data-type'),
                        height: this.getAttribute('data-height'),
                        width: this.getAttribute('data-width'),
                        size: this.getAttribute('data-size'),
                        volume: this.getAttribute('data-volume'),
                        location: this.getAttribute('data-location')
                    };
                    openEditDialog(recordData);
                });
            });

            // Edit form submission
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                // Send AJAX request to update the record
                fetch('../backend/update-timber.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Record updated successfully!');
                        closeEditDialog();
                        location.reload(); // Reload the page to see changes
                    } else {
                        alert('Error updating record: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating record. Please try again.');
                });
            });

            // Single delete buttons
            const deleteButtons = document.querySelectorAll('.delete-single-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const recordId = this.getAttribute('data-id');
                    openSingleDeleteDialog(recordId);
                });
            });

            // Delete all button
            document.getElementById('deleteAllBtn').addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select at least one record to delete.');
                    return;
                }
                openDeleteAllDialog();
            });

            // Confirm single delete button
            document.getElementById('confirmSingleDelete').addEventListener('click', function() {
                if (recordIdToDelete) {
                    // Send AJAX request to delete the record
                    fetch('../backend/delete-logs.php?id=' + recordIdToDelete, {
                        method: 'GET'
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert('Record deleted successfully!');
                        closeSingleDeleteDialog();
                        location.reload(); // Reload the page to see changes
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting record. Please try again.');
                    });
                }
            });

            // Confirm delete all button
            document.getElementById('confirmDeleteAll').addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
                const selectedIds = Array.from(checkedBoxes).map(checkbox => {
                    return checkbox.closest('tr').getAttribute('data-id');
                });

                if (selectedIds.length === 0) {
                    alert('No records selected.');
                    return;
                }

                // Send AJAX request to delete selected records
                const formData = new FormData();
                formData.append('ids', JSON.stringify(selectedIds));

                fetch('../backend/delete-logs-all.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert('Selected records deleted successfully!');
                    closeDeleteAllDialog();
                    location.reload(); // Reload the page to see changes
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting records. Please try again.');
                });
            });
        });

        // Close dialogs when clicking outside
        window.addEventListener('click', function(event) {
            const editDialog = document.getElementById('editDialog');
            const deleteDialog = document.getElementById('singleDeleteDialog');
            const deleteAllDialog = document.getElementById('deleteAllDialog');
            
            if (event.target === editDialog) {
                closeEditDialog();
            }
            if (event.target === deleteDialog) {
                closeSingleDeleteDialog();
            }
            if (event.target === deleteAllDialog) {
                closeDeleteAllDialog();
            }
        });
    </script>
        </div>
        
        
    </div>
</body>
</html>