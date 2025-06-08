<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('sales'); // Check if the user is logged in and has the required role
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stockout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style></style>
</head>

<body>
    <?php include '../menu/menu.php'; ?>

    <div class="main-content">

        <?php

        include '../header/header.php';


        include '../../database/connection.php';

        $select = $pdo->query("SELECT stockout.*, stockin.*, timber.*, logs.*, plant.*, germination.* FROM stockout,stockin, timber, logs, plant, germination WHERE stockout.in_id = stockin.in_id AND stockin.t_id = timber.t_id AND timber.l_id = logs.l_id AND logs.p_id = plant.p_id AND plant.g_id = germination.g_id");
        $select->execute();
        $fetch = $select->fetchAll();

        $i = 1;


        ?>



        <div class="dashboard-grid-i">
            <div class="stock-container">
                <h1>Stock Out Inventory</h1>

                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search stock entries..." onkeyup="searchTable()">
                        <!-- <span class="search-icon"><i class="fa fa-search"></i></span> -->
                    </div>
                    <div class="button-container">
                        <a href="add-stock-out.php" style="text-decoration: none;">
                            <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Stock out
                            </button>
                        </a>
                        <button id="viewNotesBtn" class="btn btn-info" onclick="openNotesModal()">
                            <i class="fa fa-eye"></i>
                            View All Notes
                        </button>
                        <!-- <button id="deleteSelected" class="delete-btn" abled>
                            <i class="fa fa-trash"></i>
                            Delete
                        </button> -->
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
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Purchased</th>
                            <th>Saled</th>
                            <th>Date</th>
                            <th>H x W</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($fetch as $row) { ?>
                            <tr data-id="<?php echo $row['out_id']; ?>">
                                <!-- <td class="checkbox-cell">
                                    <input type="checkbox" class="checkbox stock-checkbox">
                                </td> -->
                                <td class="row-id"> <?php echo $i;
                                                    $i++; ?> </td>
                                <td class="searchable"> <?php echo $row['plant_name']; ?> </td>
                                <td class="searchable"> <?php echo  $row['type']; ?> </td>
                                <td class="searchable"> <?php echo  $row['out_amount']; ?> </td>
                                <td class="searchable"> <?php echo number_format($row['price']); ?> Rwf</td>
                                <td class="searchable sale-price" data-original-price="<?php echo $row['out_price']; ?>"> <?php echo number_format($row['out_price']); ?> Rwf</td>
                                <td class="searchable"><?php echo $row['out_date']; ?> </td>
                                <td class="searchable"> <?php echo $row['t_height']; ?>cm x <?php echo $row['t_width']; ?> cm </td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="openEditModal(this, '<?php echo $row['out_id']; ?>', '<?php echo $row['out_price']; ?>', '<?php echo $row['plant_name']; ?>')">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
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

                <!-- No results message (initially hidden) -->
                <div id="noResults" class="no-results" style="display: none;">
                    No matching records found.
                </div>
            </div>

            <!-- Edit Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Edit Stock Record</h2>
                        <span class="close" onclick="closeEditModal()">&times;</span>
                    </div>
                    <form id="editForm">
                        <input type="hidden" id="editRecordId" name="record_id">

                        <div class="form-group">
                            <label for="editItemName">Item Name:</label>
                            <input type="text" id="editItemName" name="item_name" readonly>
                        </div>

                        <div class="form-group">
                            <label for="editPrice">Sale Price (Rwf):</label>
                            <input type="number" id="editPrice" name="sale_price" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="editNotes">Additional Notes:</label>
                            <textarea id="editNotes" name="notes" placeholder="Add notes to help identify any issues with this record..."></textarea>
                        </div>

                        <div class="modal-buttons">
                            <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notes Modal -->
            <div id="notesModal" class="modal">
                <div class="modal-content large">
                    <div class="modal-header">
                        <h2>All Update Notes</h2>
                        <span class="close" onclick="closeNotesModal()">&times;</span>
                    </div>
                    
                    <div id="notesContent">
                        <!-- Notes will be loaded here -->
                    </div>
                    
                    <div class="modal-buttons">
                        <button type="button" class="btn btn-secondary" onclick="closeNotesModal()">Close</button>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                // Real-time search functionality
                function searchTable() {
                    const input = document.getElementById('searchInput');
                    const filter = input.value.toLowerCase();
                    const table = document.getElementById('stockTable');
                    const tbody = table.getElementsByTagName('tbody')[0];
                    const rows = tbody.getElementsByTagName('tr');
                    const noResults = document.getElementById('noResults');
                    let visibleRows = 0;

                    // Clear previous highlights
                    clearHighlights();

                    for (let i = 0; i < rows.length; i++) {
                        const searchableCells = rows[i].getElementsByClassName('searchable');
                        let found = false;

                        if (filter === '') {
                            rows[i].style.display = '';
                            visibleRows++;
                            continue;
                        }

                        // Search through all searchable cells
                        for (let j = 0; j < searchableCells.length; j++) {
                            const cellText = searchableCells[j].textContent.toLowerCase();

                            if (cellText.includes(filter)) {
                                found = true;
                                // Highlight matching text
                                highlightText(searchableCells[j], filter);
                            }
                        }

                        if (found) {
                            rows[i].style.display = '';
                            visibleRows++;
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }

                    // Show/hide no results message
                    if (visibleRows === 0 && filter !== '') {
                        noResults.style.display = 'block';
                        table.style.display = 'none';
                    } else {
                        noResults.style.display = 'none';
                        table.style.display = 'table';
                    }
                }

                function highlightText(element, searchTerm) {
                    const originalText = element.getAttribute('data-original') || element.innerHTML;
                    element.setAttribute('data-original', originalText);

                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
                    element.innerHTML = highlightedText;
                }

                function clearHighlights() {
                    const highlightedElements = document.querySelectorAll('[data-original]');
                    highlightedElements.forEach(element => {
                        element.innerHTML = element.getAttribute('data-original');
                        element.removeAttribute('data-original');
                    });
                }

                // Edit Modal Functions
                function openEditModal(button, recordId, currentPrice, itemName) {
                    document.getElementById('editRecordId').value = recordId;
                    document.getElementById('editItemName').value = itemName;
                    document.getElementById('editPrice').value = currentPrice;
                    document.getElementById('editNotes').value = '';
                    document.getElementById('editModal').style.display = 'block';
                }

                function closeEditModal() {
                    document.getElementById('editModal').style.display = 'none';
                    document.getElementById('editForm').reset();
                }

                // Notes Modal Functions
                function openNotesModal() {
                    document.getElementById('notesModal').style.display = 'block';
                    loadAllNotes();
                }

                function closeNotesModal() {
                    document.getElementById('notesModal').style.display = 'none';
                }

                function loadAllNotes() {
                    const notesContent = document.getElementById('notesContent');
                    notesContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fa fa-spinner fa-spin"></i> Loading notes...</div>';

                    $.ajax({
                        url: 'get_all_notes.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                displayNotes(response.notes);
                            } else {
                                notesContent.innerHTML = '<div class="no-notes">Error loading notes: ' + response.message + '</div>';
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                            notesContent.innerHTML = '<div class="no-notes">Error loading notes. Please try again.</div>';
                        }
                    });
                }

                function displayNotes(notes) {
                    const notesContent = document.getElementById('notesContent');
                    
                    if (!notes || notes.length === 0) {
                        notesContent.innerHTML = '<div class="no-notes">No update notes found.</div>';
                        return;
                    }

                    let html = `<div class="notes-summary">
                        <strong>Total Notes:</strong> ${notes.length}
                    </div>
                    <div class="notes-container">`;

                    notes.forEach(function(note) {
                        html += `
                            <div class="note-item">
                                <div class="note-header">
                                    <div class="note-info">
                                        <div class="note-title">${escapeHtml(note.plant_name)}</div>
                                        <div class="note-details">
                                            <strong>Record ID:</strong> ${note.out_id} | 
                                            <strong>Price:</strong> ${formatPrice(note.out_price)} Rwf | 
                                            <strong>Amount:</strong> ${note.out_amount}
                                        </div>
                                    </div>
                                    <span class="note-date">${formatDate(note.updated_at)}</span>
                                </div>
                                <div class="note-content">${escapeHtml(note.up_notes)}</div>
                            </div>
                        `;
                    });

                    html += '</div>';
                    notesContent.innerHTML = html;
                }

                function formatPrice(price) {
                    if (!price) return '0';
                    return new Intl.NumberFormat().format(price);
                }

                function formatDate(dateString) {
                    if (!dateString) return 'N/A';
                    const date = new Date(dateString);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                }

                function escapeHtml(text) {
                    if (!text) return 'No notes available';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Close modal when clicking outside
                window.onclick = function(event) {
                    const editModal = document.getElementById('editModal');
                    const notesModal = document.getElementById('notesModal');
                    
                    if (event.target === editModal) {
                        closeEditModal();
                    }
                    if (event.target === notesModal) {
                        closeNotesModal();
                    }
                }

                // Handle form submission
                document.getElementById('editForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const recordId = formData.get('record_id');
                    const newPrice = formData.get('sale_price');
                    const notes = formData.get('notes');

                    // Here you would typically send an AJAX request to update the database
                    // For now, we'll just update the display
                    updateTableRow(recordId, newPrice, notes);
                    closeEditModal();
                });

                function updateTableRow(recordId, newPrice, notes) {
                    // Find the row with the matching data-id
                    const row = document.querySelector(`tr[data-id="${recordId}"]`);
                    if (row) {
                        // Update the sale price cell
                        const salePriceCell = row.querySelector('.sale-price');
                        salePriceCell.textContent = new Intl.NumberFormat().format(newPrice) + ' Rwf';
                        salePriceCell.setAttribute('data-original-price', newPrice);

                        // You might want to add a visual indicator that the row was updated
                        row.style.backgroundColor = '#e8f5e8';
                        setTimeout(() => {
                            row.style.backgroundColor = '';
                        }, 2000);

                        // Here you would typically send the data to your PHP script
                        // Example AJAX call (uncomment and modify as needed):

                        $.ajax({
                            url: 'update_stock_price.php',
                            method: 'POST',
                            data: {
                                record_id: recordId,
                                sale_price: newPrice,
                                notes: notes
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    console.log('Price updated successfully');
                                    // Optional: Show success message
                                    alert('Price updated successfully!');
                                } else {
                                    alert('Error: ' + response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', error);
                                alert('Error updating price. Please try again.');
                            }
                        });

                    }
                }

                // Clear search when input is empty
                document.getElementById('searchInput').addEventListener('input', function() {
                    if (this.value === '') {
                        clearHighlights();
                    }
                });

                // Add this JavaScript function to your existing script section

function deleteRow(deleteButton) {
    // Get the row that contains the delete button
    const row = deleteButton.closest('tr');
    const recordId = row.getAttribute('data-id');
    const itemName = row.querySelector('.searchable').textContent.trim();
    
    // Show confirmation dialog
    if (confirm(`Are you sure you want to delete "${itemName}"?\n\nThis action cannot be undone.`)) {
        // Show loading state
        deleteButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        deleteButton.style.pointerEvents = 'none';
        
        // Send AJAX request to delete the record
        $.ajax({
            url: 'delete_stock_out.php',
            method: 'POST',
            data: {
                record_id: recordId,
                action: 'delete'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Remove the row with animation
                    row.style.transition = 'all 0.3s ease';
                    row.style.backgroundColor = '#ffebee';
                    row.style.transform = 'scale(0.95)';
                    row.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        // Update row numbers after deletion
                        updateRowNumbers();
                        
                        // Show success message
                        showNotification('Record deleted successfully!', 'success');
                        
                        // Check if table is empty
                        checkEmptyTable();
                    }, 300);
                } else {
                    // Reset button state on error
                    resetDeleteButton(deleteButton);
                    showNotification('Error: ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                resetDeleteButton(deleteButton);
                showNotification('Error deleting record. Please try again.', 'error');
            }
        });
    }
}

function resetDeleteButton(deleteButton) {
    deleteButton.innerHTML = `
        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
    `;
    deleteButton.style.pointerEvents = 'auto';
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('#stockTable tbody tr');
    rows.forEach((row, index) => {
        const rowIdCell = row.querySelector('.row-id');
        if (rowIdCell) {
            rowIdCell.textContent = index + 1;
        }
    });
}

function checkEmptyTable() {
    const tbody = document.querySelector('#stockTable tbody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        const noDataRow = document.createElement('tr');
        noDataRow.innerHTML = `
            <td colspan="9" style="text-align: center; padding: 40px; color: #666; font-style: italic;">
                No stock records found. <a href="add-stock-out.php" style="color: #007bff;">Add a new record</a>
            </td>
        `;
        tbody.appendChild(noDataRow);
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 10000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
    `;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Enhanced delete with batch selection (if you want to enable the commented checkboxes)
let selectedRecords = [];

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.stock-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        toggleRecordSelection(checkbox);
    });
    
    updateDeleteButton();
}

function toggleRecordSelection(checkbox) {
    const row = checkbox.closest('tr');
    const recordId = row.getAttribute('data-id');
    
    if (checkbox.checked) {
        if (!selectedRecords.includes(recordId)) {
            selectedRecords.push(recordId);
            row.style.backgroundColor = '#e3f2fd';
        }
    } else {
        selectedRecords = selectedRecords.filter(id => id !== recordId);
        row.style.backgroundColor = '';
    }
}

function updateDeleteButton() {
    const deleteButton = document.getElementById('deleteSelected');
    if (deleteButton) {
        deleteButton.disabled = selectedRecords.length === 0;
        deleteButton.innerHTML = selectedRecords.length > 0 
            ? `<i class="fa fa-trash"></i> Delete (${selectedRecords.length})` 
            : '<i class="fa fa-trash"></i> Delete';
    }
}

function deleteSelectedRecords() {
    if (selectedRecords.length === 0) {
        showNotification('No records selected', 'error');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedRecords.length} selected record(s)?\n\nThis action cannot be undone.`)) {
        // Disable the delete button
        const deleteButton = document.getElementById('deleteSelected');
        deleteButton.disabled = true;
        deleteButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
        
        $.ajax({
            url: 'delete_stock_out.php',
            method: 'POST',
            data: {
                record_ids: selectedRecords,
                action: 'delete_multiple'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Remove selected rows
                    selectedRecords.forEach(recordId => {
                        const row = document.querySelector(`tr[data-id="${recordId}"]`);
                        if (row) {
                            row.remove();
                        }
                    });
                    
                    // Reset selections
                    selectedRecords = [];
                    document.getElementById('selectAll').checked = false;
                    updateRowNumbers();
                    updateDeleteButton();
                    checkEmptyTable();
                    
                    showNotification(`${response.deleted_count} record(s) deleted successfully!`, 'success');
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
                
                // Re-enable delete button
                deleteButton.disabled = false;
                deleteButton.innerHTML = '<i class="fa fa-trash"></i> Delete';
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showNotification('Error deleting records. Please try again.', 'error');
                
                // Re-enable delete button
                deleteButton.disabled = false;
                deleteButton.innerHTML = '<i class="fa fa-trash"></i> Delete';
            }
        });
    }
}
            
            
            </script>
        </div>


    </div>
</body>

</html>