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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .close-btn:hover {
            background-color: #f5f5f5;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: #00dc82;
            box-shadow: 0 0 0 3px rgba(0, 220, 130, 0.1);
        }

        .form-input:disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00dc82, #00b369);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            min-width: 100px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 220, 130, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            min-width: 100px;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #dee2e6;
            color: #495057;
        }

        /* Delete confirmation styles */
        .delete-modal .modal-content {
            max-width: 400px;
            text-align: center;
        }

        .delete-icon {
            width: 60px;
            height: 60px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .delete-icon svg {
            width: 30px;
            height: 30px;
            color: #dc2626;
        }

        .delete-message {
            font-size: 16px;
            color: #374151;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            min-width: 100px;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
        }

        /* Search highlighting */
        .highlight {
            background-color: #ffeb3b;
            padding: 2px 4px;
            border-radius: 3px;
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Success message */
        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00dc82;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 1100;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php 
    include'../header/header.php';
    include'../../database/connection.php';
    
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
        <h1>Logs Trees Table</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search stock entries...">
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
                    <th>Amount</th>
                    <th>Measures</th>
                    <th>inDate</th>
                    <th>Actions</th>
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
                    <td> <?php echo $row['amount']; ?> </td>
                    <td> <?php echo $row['v1']." cm"?> </td>
                    <td><?php echo $row['indate']; ?></td>
                    <td class="action-buttons">
                        <button class="edit-btn" onclick="openUpdateModal(<?php echo $row['l_id']; ?>, '<?php echo $row['plant_name']; ?>', <?php echo $row['amount']; ?>, <?php echo $row['v1']; ?>, '<?php echo $row['indate']; ?>')">
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
                <h2 class="modal-title">Update Log Entry</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="updateForm">
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
                    <label class="form-label">Measure (cm)</label>
                    <input type="number" id="measure" name="measure" class="form-input" step="0.1" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" id="inDate" name="inDate" class="form-input" required>
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

        // Modal functions
        function openUpdateModal(logId, plantName, amount, measure, inDate) {
            document.getElementById('logId').value = logId;
            document.getElementById('plantName').value = plantName;
            document.getElementById('amount').value = amount;
            document.getElementById('measure').value = measure;
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

        // Update form submission
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
                            cells[3].textContent = formData.get('amount');
                            cells[4].textContent = formData.get('measure') + ' cm';
                            cells[5].textContent = formData.get('inDate');
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