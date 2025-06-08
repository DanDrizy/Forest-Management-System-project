// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get references to DOM elements
    const selectAllCheckbox = document.getElementById('selectAll');
    const stockCheckboxes = document.querySelectorAll('.stock-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    const searchInput = document.getElementById('searchInput');
    
    // Handle "Select All" checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            
            // Select or deselect all checkboxes
            stockCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
            // Enable or disable delete button based on selection
            updateDeleteButtonState();
        });
    }
    
    // Handle individual checkboxes
    stockCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButtonState);
    });
    
    // Handle individual delete buttons
    document.querySelectorAll('.delete-action').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const row = this.closest('tr');
            const id = row.getAttribute('data-id');
            
            if (confirm('Are you sure you want to delete this tree record?')) {
                deleteRecord(id, row);
            }
        });
    });
    
    // Handle bulk delete button
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedIds = [];
            
            // Get all selected IDs
            document.querySelectorAll('.stock-checkbox:checked').forEach(checkbox => {
                const row = checkbox.closest('tr');
                const id = row.getAttribute('data-id');
                if (id) {
                    selectedIds.push(id);
                }
            });
            
            if (selectedIds.length === 0) {
                alert('No records selected for deletion');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${selectedIds.length} selected tree records?`)) {
                bulkDeleteRecords(selectedIds);
            }
        });
    }
    
    // Handle search functionality
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            
            document.querySelectorAll('#stockTable tbody tr').forEach(row => {
                let matches = false;
                
                // Check all cells except checkbox and action cells
                row.querySelectorAll('td:not(.checkbox-cell):not(.action-buttons)').forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        matches = true;
                    }
                });
                
                // Show or hide row based on match
                row.style.display = matches ? '' : 'none';
            });
        });
    }
    
    // Function to update delete button state
    function updateDeleteButtonState() {
        const hasChecked = Array.from(stockCheckboxes).some(checkbox => checkbox.checked);
        
        if (deleteSelectedBtn) {
            deleteSelectedBtn.disabled = !hasChecked;
        }
    }
    
    // Function to delete a single record
    function deleteRecord(id, row) {
        // Create form data
        const formData = new FormData();
        formData.append('delete_id', id);
        
        // Send AJAX request
        fetch('../backend/delete-plant-backend.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Remove row from the table
                row.remove();
                
                // Show success message
                showNotification(data.message, 'success');
                
                // Update row numbers
                updateRowNumbers();
            } else {
                // Show error message
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the record', 'error');
        });
    }
    
    // Function to delete multiple records
    function bulkDeleteRecords(ids) {
        // Create form data
        const formData = new FormData();
        
        // Append each ID to the form data
        ids.forEach(id => {
            formData.append('delete_ids[]', id);
        });
        
        // Send AJAX request
        fetch('../backend/delete-plant.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' || data.status === 'partial') {
                // Remove deleted rows
                ids.forEach(id => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }
                });
                
                // Show success message
                showNotification(data.message, data.status === 'partial' ? 'warning' : 'success');
                
                // Reset select all checkbox
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
                
                // Update delete button state
                updateDeleteButtonState();
                
                // Update row numbers
                updateRowNumbers();
            } else {
                // Show error message
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting records', 'error');
        });
    }
    
    // Function to update row numbers after deletion
    function updateRowNumbers() {
        const rows = document.querySelectorAll('#stockTable tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('td.row-id').textContent = index + 1;
        });
    }
    
    // Function to show notification to the user
    function showNotification(message, type = 'info') {
        // Check if notification container exists, if not create it
        let notificationContainer = document.getElementById('notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.style.position = 'fixed';
            notificationContainer.style.top = '20px';
            notificationContainer.style.right = '20px';
            notificationContainer.style.zIndex = '1000';
            document.body.appendChild(notificationContainer);
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-message">${message}</div>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Style the notification
        notification.style.backgroundColor = type === 'success' ? '#4CAF50' : 
                                           type === 'error' ? '#F44336' : 
                                           type === 'warning' ? '#FF9800' : '#2196F3';
        notification.style.color = 'white';
        notification.style.padding = '15px';
        notification.style.borderRadius = '5px';
        notification.style.marginBottom = '10px';
        notification.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        notification.style.position = 'relative';
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-in-out';
        
        // Add close button functionality
        const closeButton = notification.querySelector('.notification-close');
        closeButton.style.position = 'absolute';
        closeButton.style.top = '5px';
        closeButton.style.right = '10px';
        closeButton.style.background = 'none';
        closeButton.style.border = 'none';
        closeButton.style.color = 'white';
        closeButton.style.fontSize = '20px';
        closeButton.style.cursor = 'pointer';
        closeButton.addEventListener('click', () => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notificationContainer.removeChild(notification);
            }, 300);
        });
        
        // Add to container
        notificationContainer.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.style.opacity = '1';
        }, 10);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode === notificationContainer) {
                    notificationContainer.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
});