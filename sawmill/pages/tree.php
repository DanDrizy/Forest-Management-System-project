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
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/search-delete.css">
    <style>
        .menu-item.active-tree {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        
    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid-saw">
        <div class="container">
        <h1>Available LOGS</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" placeholder="Search stock entries...">
                <span class="search-icon"> <i class="fa fa-search"></i> </span>
            </div>
            <!-- <div class="button-container">
                
                <button id="deleteSelected" class="delete-btn" onclick="confirmDeleteAll()">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </div> -->
        </div>
        <?php
        
        // Include the database connection file
        include_once '../../database/connection.php';
        // Fetch the tree data from the database
        $stmt = $pdo->prepare("SELECT * FROM logs,plant,germination WHERE plant.g_id = germination.g_id AND plant.p_id = logs.p_id AND l_status ='send' AND amount > 0 ");
        $stmt->execute();
        $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $i = 1; // Initialize the counter variable
        
        
        
        ?>
        <table id="stockTable">
            <thead>
                <tr>
                    <!-- <th class="checkbox-cell">
                        <input type="checkbox" id="selectAll" class="checkbox">
                    </th> -->
                    <th class="row-id">ID</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>DBH</th>
                    <th>Height</th>
                    <th>Health</th>
                    <th>Volume</th>
                    <th>Dimention</th>
                    <th>Recorded</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trees as $tree): ?>
                <tr data-id="<?php echo $tree['p_id']; ?>">
                    <!-- <td class="checkbox-cell">
                        <!-- <input type="checkbox" class="checkbox stock-checkbox"> 
                    </td> -->
                    <td class="row-id"><?php echo $i; $i++; ?></td>
                    <td><?php echo $tree['plant_name']; ?></td>
                    <td><?php echo $tree['amount']; ?></td>
                    <td><?php echo $tree['DBH']; ?></td>
                    <td><?php echo $tree['height']; ?></td>
                    <td> <?php echo $tree['health']; ?></td>
                    <td><?php echo $tree['v1']; ?> --- <?php echo $tree['v2']; ?></td>
                    <td><?php echo $tree['d1']; ?> --- <?php echo $tree['d2']; ?></td>
                    <td> <?php echo $tree['l_indate']; ?> </td>
                    <td class="action-buttons">
                        
                        
                        <div class="delete-action" onclick="confirmDeleteSingle(<?php echo $tree['p_id']; ?>)">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                    </td>
                </tr>
                
            <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Confirmation Dialog for Delete All -->
    <div id="confirmDialog" class="confirm-dialog">
        <div class="dialog-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete all selected items? This action cannot be undone.</p>
            <div class="dialog-buttons">
                <button class="btn-cancel" onclick="closeConfirmDialog()">Cancel</button>
                <button class="btn-confirm" onclick="proceedWithDelete()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Confirmation Dialog for Single Delete -->
    <div id="confirmSingleDialog" class="confirm-dialog">
        <div class="dialog-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="dialog-buttons">
                <button class="btn-cancel" onclick="closeConfirmSingleDialog()">Cancel</button>
                <button class="btn-confirm" id="confirmSingleDeleteBtn">Delete</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="../js/check.js"></script>
    <script>
        // Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-bar input');
    const table = document.getElementById('stockTable');
    const rows = table.querySelectorAll('tbody tr');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const stockCheckboxes = document.querySelectorAll('.stock-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelected');

    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        stockCheckboxes.forEach(checkbox => {
            // Only check/uncheck visible rows
            const row = checkbox.closest('tr');
            if (row.style.display !== 'none') {
                checkbox.checked = isChecked;
            }
        });
        updateDeleteButtonState();
    });

    // Individual checkbox change
    stockCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateDeleteButtonState();
        });
    });

    // Update Select All checkbox state based on individual checkboxes
    function updateSelectAllState() {
        const visibleCheckboxes = Array.from(stockCheckboxes).filter(cb => 
            cb.closest('tr').style.display !== 'none'
        );
        const checkedVisibleBoxes = visibleCheckboxes.filter(cb => cb.checked);
        
        if (checkedVisibleBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedVisibleBoxes.length === visibleCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Enable/disable delete button based on selection
    function updateDeleteButtonState() {
        const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
        deleteSelectedBtn.disabled = checkedBoxes.length === 0;
        
        // Update button text to show count
        if (checkedBoxes.length > 0) {
            deleteSelectedBtn.innerHTML = `
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete (${checkedBoxes.length})
            `;
        } else {
            deleteSelectedBtn.innerHTML = `
                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            `;
        }
    }

    // Initial state
    updateDeleteButtonState();
});

// Enhanced delete confirmation functions
function confirmDeleteAll() {
    const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }
    
    const confirmDialog = document.getElementById('confirmDialog');
    const dialogText = confirmDialog.querySelector('p');
    dialogText.textContent = `Are you sure you want to delete ${checkedBoxes.length} selected item(s)? This action cannot be undone.`;
    
    confirmDialog.style.display = 'flex';
}

function closeConfirmDialog() {
    document.getElementById('confirmDialog').style.display = 'none';
}

function proceedWithDelete() {
    const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
    const selectedIds = Array.from(checkedBoxes).map(checkbox => {
        return checkbox.closest('tr').getAttribute('data-id');
    });
    
    if (selectedIds.length > 0) {
        // Create a form to send the selected IDs
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../backend/delete_tree-all-backend.php';
        
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    
    closeConfirmDialog();
}

function confirmDeleteSingle(treeId) {
    const dialog = document.getElementById('confirmSingleDialog');
    const confirmBtn = document.getElementById('confirmSingleDeleteBtn');
    
    confirmBtn.onclick = function() {
        window.location.href = '../backend/delete-logs.php?id=' + treeId;
    };
    
    dialog.style.display = 'flex';
}

function closeConfirmSingleDialog() {
    document.getElementById('confirmSingleDialog').style.display = 'none';
}

// Enhanced search with highlighting (optional)
function highlightSearchTerm(text, term) {
    if (!term) return text;
    const regex = new RegExp(`(${term})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

// Clear search function (optional)
function clearSearch() {
    const searchInput = document.querySelector('.search-bar input');
    const rows = document.querySelectorAll('#stockTable tbody tr');
    
    searchInput.value = '';
    rows.forEach(row => {
        row.style.display = '';
        // Remove any highlighting
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            if (cell.innerHTML.includes('<mark>')) {
                cell.innerHTML = cell.textContent;
            }
        });
    });
}
    </script>
        </div>
        
    </div>
</body>
</html>