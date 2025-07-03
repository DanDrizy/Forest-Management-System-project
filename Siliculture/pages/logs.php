
<?php 

require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

include '../../database/connection.php';

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
    <link rel="stylesheet" href="../css/logs.css">
    <style></style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php 
    include'../header/header.php';
    // include'../../database/connection.php';
    
    $select = $pdo->query("SELECT 
    *
FROM logs l
JOIN plant p ON l.p_id = p.p_id
JOIN germination g ON p.g_id = g.g_id
WHERE (l.l_status = 'unsend' OR l.l_status = 'unsend-sawmill');
 ");
    $select->execute();
    $fetch = $select->fetchAll();

    $i = 1;
    ?>

    <div class="dashboard-grid-saw">
        <div class="container">
        <h1>Harvest</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search entries...">
                <!-- <span class="search-icon"> <i class="fa fa-search"></i> </span> -->
            </div>
            <div class="button-container">
                <a href="add-logs.php">
                    <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Logs
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
                    <!-- <th>Amount</th> -->
                    <!-- <th>Height</th> -->
                    <th>Health</th>
                    <th>Compartment</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($fetch as $row): ?>
                    <tr data-id="<?php echo $row['l_id']; ?>" data-pid="<?php echo $row['p_id']; ?>">
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox stock-checkbox">
                    </td>
                    <td class="row-id"> <?php echo $i; $i++; ?> </td>
                    <td> <?php echo $row['plant_name'] ; ?></td>
                    <td> <?php echo $row['health']; ?> </td>
                    <td> <?php echo $row['Compartment']; ?> </td>
                    <td><?php echo $row['indate']; ?></td>
                    <td class="action-buttons">
                        <button class="edit-btn" onclick="openUpdateModal(<?php echo $row['l_id']; ?>, '<?php echo $row['plant_name']; ?>', <?php echo $row['amount']; ?>, '<?php echo $row['coments']; ?>', '<?php echo $row['indate']; ?>')">
                            <i class=" fa fa-pencil"></i>
                            Edit
                        </button>
                        <div class="delete-action" onclick="confirmDelete(<?php echo $row['l_id']; ?>)">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Update Entry</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="updateForm" >
                <input type="hidden" id="logId" name="logId">
                
                <div class="form-group">
                    <label class="form-label">Plant Name</label>
                    <input type="text" id="plantName" class="form-input" disabled>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Amount</label>
                    <input type="number" id="amount" name="amount" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" id="inDate" name="inDate" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Comments</label>
                    <textarea id="comments" name="comments" class="form-input" rows="3" placeholder="Enter your comments here..."></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal delete-modal">
        <div class="modal-content">
            <div class="delete-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h2 class="modal-title">Confirm Delete</h2>
            <p class="delete-message">Are you sure you want to delete this log entry? This action cannot be undone.</p>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn-danger" onclick="deleteLog()">Delete</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="../js/check.js"></script>
    
    <script>
        let currentDeleteId = null;

        // Real-time search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#stockTable tbody tr');
            
            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let rowText = '';
                
                // Get text from relevant columns (skip checkbox and actions)
                for (let i = 1; i < cells.length - 1; i++) {
                    rowText += cells[i].textContent.toLowerCase() + ' ';
                }
                
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                    // Highlight search terms
                    highlightSearchTerm(row, searchTerm);
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function highlightSearchTerm(row, searchTerm) {
            if (!searchTerm) return;
            
            const cells = row.querySelectorAll('td');
            for (let i = 1; i < cells.length - 1; i++) {
                const cell = cells[i];
                const originalText = cell.textContent;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
                
                if (highlightedText !== originalText) {
                    cell.innerHTML = highlightedText;
                }
            }
        }

        // Modal functions - FIXED: Now properly handles comments parameter
        function openUpdateModal(logId, plantName, amount, comments, inDate) {
            document.getElementById('logId').value = logId;
            document.getElementById('plantName').value = plantName;
            document.getElementById('amount').value = amount;
            document.getElementById('comments').value = comments; // Handle null/undefined comments
            document.getElementById('inDate').value = inDate;
            document.getElementById('updateModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        function confirmDelete(logId) {
            currentDeleteId = logId;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            currentDeleteId = null;
        }

        function deleteLog() {
            if (!currentDeleteId) return;
            
            // Show loading state
            document.body.classList.add('loading');
            
            // AJAX request to delete log
            $.ajax({
                url: 'delete-log.php',
                method: 'POST',
                data: { logId: currentDeleteId },
                success: function(response) {
                    if (response.success) {
                        // Remove row from table
                        document.querySelector(`tr[data-id="${currentDeleteId}"]`).remove();
                        showSuccessMessage('Log entry deleted successfully!');
                        closeDeleteModal();
                    } else {
                        alert('Error deleting log entry: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error deleting log entry. Please try again.');
                },
                complete: function() {
                    document.body.classList.remove('loading');
                }
            });
        }

        // Update form submission - FIXED: Now properly handles comments field
        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Show loading state
            document.body.classList.add('loading');
            
            $.ajax({
                url: 'update-log.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Update the row in the table
                        const row = document.querySelector(`tr[data-id="${formData.get('logId')}"]`);
                        if (row) {
                            const cells = row.querySelectorAll('td');
                            cells[3].textContent = formData.get('amount'); // Amount column
                            cells[4].textContent = formData.get('inDate'); // Date column
                        }
                        
                        showSuccessMessage('Log entry updated successfully!');
                        closeModal();
                    } else {
                        alert('Error updating log entry: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating log entry. Please try again.');
                },
                complete: function() {
                    document.body.classList.remove('loading');
                }
            });
        });

        // Delete selected rows
        document.getElementById('deleteSelected').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.stock-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one item to delete.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${selectedCheckboxes.length} selected items?`)) {
                const ids = [];
                selectedCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    ids.push(row.dataset.id);
                });
                
                // Show loading state
                document.body.classList.add('loading');
                
                $.ajax({
                    url: 'delete-multiple-logs.php',
                    method: 'POST',
                    data: { logIds: ids },
                    success: function(response) {
                        if (response.success) {
                            // Remove rows from table
                            selectedCheckboxes.forEach(checkbox => {
                                checkbox.closest('tr').remove();
                            });
                            showSuccessMessage(`${ids.length} log entries deleted successfully!`);
                            
                            // Reset select all checkbox
                            document.getElementById('selectAll').checked = false;
                            document.getElementById('deleteSelected').disabled = true;
                        } else {
                            alert('Error deleting log entries: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting log entries. Please try again.');
                    },
                    complete: function() {
                        document.body.classList.remove('loading');
                    }
                });
            }
        });

        function showSuccessMessage(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'success-message';
            successDiv.textContent = message;
            document.body.appendChild(successDiv);
            
            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const updateModal = document.getElementById('updateModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === updateModal) {
                closeModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            const deleteBtn = document.getElementById('deleteSelected');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            deleteBtn.disabled = !this.checked;
        });

        // Individual checkbox functionality
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('stock-checkbox')) {
                const selectedCheckboxes = document.querySelectorAll('.stock-checkbox:checked');
                const deleteBtn = document.getElementById('deleteSelected');
                const selectAllCheckbox = document.getElementById('selectAll');
                
                deleteBtn.disabled = selectedCheckboxes.length === 0;
                
                const totalCheckboxes = document.querySelectorAll('.stock-checkbox').length;
                selectAllCheckbox.checked = selectedCheckboxes.length === totalCheckboxes;
            }
        });
    </script>

        </div>
    </div>
</body>
</html>