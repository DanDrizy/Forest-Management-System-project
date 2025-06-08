
        document.addEventListener('DOMContentLoaded', function() {
            // Get modal elements
            const modal = document.getElementById('treeDetailsModal');
            const modalContent = document.getElementById('treeDetailsContent');
            const closeButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
            
            // View buttons
            const viewButtons = document.querySelectorAll('.view-btn');
            
            // Add event listeners to view buttons
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const treeId = this.getAttribute('data-id');
                    fetchTreeDetails(treeId);
                });
            });
            
            // Close modal when clicking close button or outside
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
            
            // Function to fetch tree details via AJAX
            function fetchTreeDetails(treeId) {
                modalContent.innerHTML = '<div class="loading-spinner">Loading...</div>';
                modal.style.display = 'flex';
                
                // Fetch tree details using fetch API
                fetch(`../backend/get_tree_details.php?id=${treeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            modalContent.innerHTML = `<p class="error">${data.error}</p>`;
                        } else {
                            // Render tree details
                            let detailsHTML = `
                                <div class="detail-row">
                                    <div class="detail-label">Tree Name:</div>
                                    <div class="detail-value">${data.p_name}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Country:</div>
                                    <div class="detail-value">${data.country}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Province:</div>
                                    <div class="detail-value">${data.provance}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Location:</div>
                                    <div class="detail-value">${data.location}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Tree Type:</div>
                                    <div class="detail-value">${data.tree_type}</div>
                                </div>
                                
                                <div class="detail-section">
                                    <div class="detail-section-title">Additional Information</div>
                                    <div class="detail-textarea">${data.info || 'No additional information provided.'}</div>
                                </div>
                                
                                <div class="detail-section">
                                    <div class="detail-section-title">Comments</div>
                                    <div class="detail-textarea">${data.comment || 'No comments available.'}</div>
                                </div>
                            `;
                            
                            modalContent.innerHTML = detailsHTML;
                        }
                    })
                    .catch(error => {
                        modalContent.innerHTML = `<p class="error">Error fetching tree details: ${error.message}</p>`;
                    });
            }
            
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableRows = document.querySelectorAll('#stockTable tbody tr');
                    
                    tableRows.forEach(row => {
                        const name = row.cells[2].textContent.toLowerCase();
                        const country = row.cells[3].textContent.toLowerCase();
                        const province = row.cells[4].textContent.toLowerCase();
                        const location = row.cells[5].textContent.toLowerCase();
                        
                        if (name.includes(searchTerm) || 
                            country.includes(searchTerm) || 
                            province.includes(searchTerm) || 
                            location.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });


        // Tree Management JavaScript Functions
// Add this to your existing view.js or create a new JS file

// Global variables
let deleteAction = null; // 'single' or 'multiple'
let deleteTargets = []; // IDs to delete
let currentEditId = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupTreeManagement();
});

function setupTreeManagement() {
    // Setup select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButtonState();
        });
    }

    // Setup individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('stock-checkbox')) {
            updateSelectAllState();
            updateDeleteButtonState();
        }
    });

    // Setup delete selected button
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedIds = getSelectedTreeIds();
            if (selectedIds.length > 0) {
                deleteAction = 'multiple';
                deleteTargets = selectedIds;
                showDeleteConfirmation(`Delete ${selectedIds.length} selected tree(s)?`);
            }
        });
    }

    // Setup edit buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
            const row = e.target.closest('tr');
            const treeId = row.dataset.id;
            editTree(treeId, row);
        }
    });

    // Close modals when clicking overlay
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target.id);
        }
    });

    // Close buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('close-modal') || e.target.classList.contains('close-modal-btn')) {
            const modal = e.target.closest('.modal-overlay');
            if (modal) closeModal(modal.id);
        }
    });

    updateDeleteButtonState();
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.stock-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
    
    if (!selectAllCheckbox) return;
    
    if (checkedBoxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
    }
}

function updateDeleteButtonState() {
    const selectedIds = getSelectedTreeIds();
    const deleteBtn = document.getElementById('deleteSelected');
    if (deleteBtn) {
        deleteBtn.disabled = selectedIds.length === 0;
    }
}

function getSelectedTreeIds() {
    const checkedBoxes = document.querySelectorAll('.stock-checkbox:checked');
    return Array.from(checkedBoxes).map(checkbox => {
        return checkbox.closest('tr').dataset.id;
    });
}

function editTree(treeId, row) {
    if (!row) {
        row = document.querySelector(`tr[data-id="${treeId}"]`);
    }
    
    if (!row) {
        showMessage('Tree record not found!', 'error');
        return;
    }

    // Get current values from the row
    const cells = row.querySelectorAll('td');
    const plantName = cells[2].textContent.trim();
    const dbh = cells[3].textContent.trim();
    const height = cells[4].textContent.trim();
    const health = cells[5].textContent.trim();
    const recordDate = cells[6].textContent.trim();

    currentEditId = treeId;

    // Create or show edit modal
    showEditModal(treeId, plantName, dbh, height, health, recordDate);
}

function showEditModal(treeId, plantName, dbh, height, health, recordDate) {
    // Create modal if it doesn't exist
    let modal = document.getElementById('editTreeModal');
    if (!modal) {
        createEditModal();
        modal = document.getElementById('editTreeModal');
    }

    // Populate form fields
    document.getElementById('editTreeId').value = treeId;
    document.getElementById('editPlantName').value = plantName;
    document.getElementById('editDBH').value = dbh;
    document.getElementById('editHeight').value = height;
    document.getElementById('editHealth').value = health;
    document.getElementById('editRecordDate').value = recordDate;

    // Show modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function createEditModal() {
    const modalHTML = `
    <div id="editTreeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Tree Details</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editTreeForm">
                    <input type="hidden" id="editTreeId" name="p_id">
                    
                    <div class="form-group">
                        <label for="editPlantName">Plant Name:</label>
                        <input type="text" id="editPlantName" name="plant_name" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="editDBH">DBH:</label>
                        <input type="number" id="editDBH" name="dbh" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editHeight">Height:</label>
                        <input type="number" id="editHeight" name="height" step="0.1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editHealth">Health:</label>
                        <select id="editHealth" name="health" required>
                            <option value="A">is in Best Condition</option>
                            <option value="B">is in Good Condition</option>
                            <option value="C">is Not Good Condition</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="editRecordDate">Record Date:</label>
                        <input type="date" id="editRecordDate" name="record_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="close-modal-btn">Cancel</button>
                <button type="button" onclick="saveTreeChanges()" style="background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Save Changes</button>
            </div>
        </div>
    </div>`;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add basic modal styles if not present
    if (!document.querySelector('style[data-modal-styles]')) {
        const modalStyles = `
        <style data-modal-styles>
            .modal-overlay {
                display: none;
                position: fixed;
                top:0rem;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 1000;
            }
            .modal-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border-radius: 8px;
                width: 90%;
                max-width: 500px;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }
            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px;
                border-bottom: 1px solid #eee;
                background: #f8f9fa;
            }
            .modal-title {
                margin: 0;
                font-size: 1.2rem;
            }
            .close-modal {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                padding: 0;
                width: 30px;
                height: 30px;
            }
            .modal-body {
                padding: 20px;
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .form-group input, .form-group select {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            .modal-footer {
                padding: 15px 20px;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                background: #f8f9fa;
            }
            .modal-footer button {
                padding: 8px 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            .close-modal-btn {
                background: #6c757d;
                color: white;
            }
        </style>`;
        document.head.insertAdjacentHTML('beforeend', modalStyles);
    }
}

function saveTreeChanges() {
    const form = document.getElementById('editTreeForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    
    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;

    // Send AJAX request to update tree
    fetch('update_tree.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the table row with new data
            updateTableRow(formData.get('p_id'), formData);
            closeModal('editTreeModal');
            showMessage('Tree information updated successfully!', 'success');
        } else {
            showMessage('Error updating tree: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Network error occurred while updating tree', 'error');
    })
    .finally(() => {
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

function updateTableRow(treeId, formData) {
    const row = document.querySelector(`tr[data-id="${treeId}"]`);
    if (row) {
        const cells = row.querySelectorAll('td');
        cells[2].textContent = formData.get('plant_name');
        cells[3].textContent = formData.get('dbh');
        cells[4].textContent = formData.get('height');
        cells[5].textContent = formData.get('health');
        cells[6].textContent = formData.get('record_date');
    }
}

function deleteRow(element, treeId) {
    const row = element.closest('tr');
    const treeName = row.querySelector('td:nth-child(3)').textContent.trim();
    
    deleteAction = 'single';
    deleteTargets = [treeId];
    
    showDeleteConfirmation(`Delete "${treeName}"?`);
}

function showDeleteConfirmation(message) {
    // Create delete confirmation modal if it doesn't exist
    let modal = document.getElementById('deleteConfirmModal');
    if (!modal) {
        createDeleteModal();
        modal = document.getElementById('deleteConfirmModal');
    }

    document.getElementById('deleteMessage').textContent = message;
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function createDeleteModal() {
    const modalHTML = `
    <center>
    <div id="deleteConfirmModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px;">
                    <div style="color: #f44336; font-size: 48px; margin-bottom: 15px;">⚠️</div>
                    <h3 style="margin-bottom: 10px;">Are you sure?</h3>
                    <p id="deleteMessage" style="color: #666; margin-bottom: 20px;">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close-modal-btn">Cancel</button>
                <button type="button" onclick="confirmDelete()" style="background: #f44336; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Delete</button>
            </div>
        </div>
    </div>
    </center>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function confirmDelete() {
    const deleteBtn = event.target;
    const originalText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;

    // Prepare data for deletion
    const formData = new FormData();
    formData.append('action', deleteAction);
    formData.append('tree_ids', JSON.stringify(deleteTargets));

    // Send AJAX request to delete
    fetch('delete_tree.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove rows from table
            deleteTargets.forEach(treeId => {
                const row = document.querySelector(`tr[data-id="${treeId}"]`);
                if (row) {
                    row.remove();
                }
            });

            // Reset selection
            document.getElementById('selectAll').checked = false;
            updateDeleteButtonState();
            
            closeModal('deleteConfirmModal');
            
            const count = deleteTargets.length;
            showMessage(`${count} tree record${count > 1 ? 's' : ''} deleted successfully!`, 'success');
        } else {
            showMessage('Error deleting tree(s): ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Network error occurred while deleting', 'error');
    })
    .finally(() => {
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = false;
    });
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.message-container');
    existingMessages.forEach(msg => msg.remove());

    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-container ${type}-message`;
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';

    // Insert after h1
    const h1 = document.querySelector('h1');
    if (h1) {
        h1.insertAdjacentElement('afterend', messageDiv);
    } else {
        document.querySelector('.container').insertAdjacentElement('afterbegin', messageDiv);
    }

    // Auto hide after 5 seconds
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        setTimeout(() => messageDiv.remove(), 300);
    }, 5000);
}
    