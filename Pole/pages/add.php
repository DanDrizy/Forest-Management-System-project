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
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/arrangement.css">
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 4% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        
        .close-button:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .cancel-btn, .confirm-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .cancel-btn {
            background-color: #6c757d;
            color: white;
        }
        
        .confirm-btn {
            background-color: #007bff;
            color: white;
        }
        
        .cancel-btn:hover {
            background-color: #5a6268;
        }
        
        .confirm-btn:hover {
            background-color: #0056b3;
        }
        
        /* Search highlight */
        .highlight {
            background-color: yellow;
            font-weight: bold;
        }
        
        /* Hidden row for search */
        .hidden-row {
            display: none;
        }
        
        /* Delete button styling */
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
        
        .delete-btn .icon {
            width: 16px;
            height: 16px;
        }
    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php 
    
    include'../header/header.php'; 
    include'../../database/connection.php'; // Include the database connection file
    
    $select_pole_records = $pdo->query("SELECT * from pole");
    $select_pole_records->execute();
    $pole_records = $select_pole_records->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    
    ?>

        
        
    <div class="dashboard-grid-saw">
        <div class="container">
        <h1>Add Poles</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search stock entries..." onkeyup="searchTable()">
            </div>
            <div class="button-container">
                <a href="add_tree.php" style=" text-decoration: none; ">
                            <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Poles
                            </button>
                        </a>
              
                
            </div>
        </div>


<table id="stockTable">
    <thead>
        <tr>
           
            <th class="row-id">ID</th>
            <th>Name</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Measures</th>
            <th>Location</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $row_id = 1;
        foreach ($pole_records as $record) : 
            $actual_id = isset($record['id']) ? $record['id'] : $row_id;
        ?>
        <tr data-record-id="<?php echo $actual_id; ?>">
            
            <td class="row-id"> <?php echo $row_id; ?> </td>
            <td data-field="tree_name"> <?php echo $record['tree_name']; ?> </td>
            <td data-field="record_date"> <?php echo $record['record_date']; ?> </td>
            <td data-field="po_amount"> <?php echo $record['po_amount']; ?> </td>
            <td data-field="height"> <?php echo $record['height'] ?> </td>
            <td data-field="location"> <?php echo $record['location']; ?> </td>
            <td class="action-buttons">
                    <button class="edit-btn" type="button" data-record-id="<?php echo $actual_id; ?>">
                    <i class="fa fa-pencil"></i>
                    Edit
                </button>
                
                <button class="delete-btn" type="button" data-record-id="<?php echo $actual_id; ?>">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        </tr>
        <?php 
        $row_id++;
        endforeach; ?>
    </tbody>
</table>
    </div>

        </div>
        
        
    </div>

<!-- Edit Amount Dialog -->

    <div id="editAmountDialog" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeEditDialog()">&times;</span>
    <h2>Edit Record</h2>
    <div class="dialog-form">
      <input type="hidden" id="recordId" value="">
      <div class="form-group">
        <label for="treeName">Tree Name:</label>
        <input type="text" id="treeName" name="treeName" required>
      </div>
      <div class="form-group">
        <label for="recordDate">Date:</label>
        <input type="date" id="recordDate" name="recordDate" required>
      </div>
      <div class="form-group">
        <label for="currentAmount">Amount:</label>
        <input type="number" id="currentAmount" name="currentAmount" min="1" required>
      </div>
      <div class="form-group">
        <label for="height">Height:</label>
        <input type="text" id="height" name="height" required>
      </div>
      <div class="form-group">
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" required>
      </div>
      <div class="form-actions">
        <button id="cancelEdit" class="cancel-btn" onclick="closeEditDialog()">Cancel</button>
        <button id="confirmEdit" class="confirm-btn" onclick="updateRecord()">Update</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="../js/check.js"></script>

<script>
// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners...');
    
    // Add event listeners for edit buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            e.preventDefault();
            console.log('Edit button clicked');
            openEditDialog(e.target.closest('.edit-btn'));
        }
        
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            console.log('Delete button clicked');
            deleteRow(e.target.closest('.delete-btn'));
        }
    });
});

// Real-time search functionality
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('stockTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        // Reset previous highlights
        for (let j = 0; j < cells.length - 1; j++) { // Exclude action column
            const cellText = cells[j].getAttribute('data-original-text') || cells[j].textContent || cells[j].innerText;
            if (!cells[j].getAttribute('data-original-text')) {
                cells[j].setAttribute('data-original-text', cellText);
            }
            cells[j].innerHTML = cellText; // Remove previous highlights
        }
        
        // Search through all cells except the action column
        for (let j = 0; j < cells.length - 1; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().indexOf(filter) > -1) {
                found = true;
                // Highlight matching text
                if (filter !== '') {
                    const regex = new RegExp(`(${filter})`, 'gi');
                    cells[j].innerHTML = cellText.replace(regex, '<span class="highlight">$1</span>');
                }
            }
        }
        
        // Show or hide row based on search
        if (found || filter === '') {
            rows[i].style.display = '';
            rows[i].classList.remove('hidden-row');
        } else {
            rows[i].style.display = 'none';
            rows[i].classList.add('hidden-row');
        }
    }
}

// Edit dialog functions
function openEditDialog(button) {
    console.log('Opening edit dialog...');
    const row = button.closest('tr');
    const recordId = button.getAttribute('data-record-id') || row.getAttribute('data-record-id');
    
    console.log('Record ID:', recordId);
    
    // Get values from table cells using data attributes
    const treeName = row.querySelector('[data-field="tree_name"]').textContent.trim();
    const recordDate = row.querySelector('[data-field="record_date"]').textContent.trim();
    const amount = row.querySelector('[data-field="po_amount"]').textContent.trim();
    const height = row.querySelector('[data-field="height"]').textContent.trim();
    const location = row.querySelector('[data-field="location"]').textContent.trim();
    
    console.log('Data:', {treeName, recordDate, amount, height, location});
    
    // Set values in the dialog
    document.getElementById('recordId').value = recordId;
    document.getElementById('treeName').value = treeName;
    document.getElementById('recordDate').value = recordDate;
    document.getElementById('currentAmount').value = amount;
    document.getElementById('height').value = height;
    document.getElementById('location').value = location;
    
    // Show the dialog
    document.getElementById('editAmountDialog').style.display = 'block';
}

function closeEditDialog() {
    document.getElementById('editAmountDialog').style.display = 'none';
}

function updateRecord() {
    const recordId = document.getElementById('recordId').value;
    const treeName = document.getElementById('treeName').value;
    const recordDate = document.getElementById('recordDate').value;
    const amount = document.getElementById('currentAmount').value;
    const height = document.getElementById('height').value;
    const location = document.getElementById('location').value;
    
    console.log('Updating record:', {recordId, treeName, recordDate, amount, height, location});
    
    // Validate inputs
    if (!treeName || !recordDate || !amount || !height || !location) {
        alert('Please fill in all fields');
        return;
    }
    
    if (amount <= 0) {
        alert('Please enter a valid amount');
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('id', recordId);
    formData.append('tree_name', treeName);
    formData.append('record_date', recordDate);
    formData.append('po_amount', amount);
    formData.append('height', height);
    formData.append('location', location);
    
    // Send AJAX request to update record
    fetch('update_pole_record.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                // Update the table row with new values
                const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
                if (row) {
                    row.querySelector('[data-field="tree_name"]').textContent = treeName;
                    row.querySelector('[data-field="record_date"]').textContent = recordDate;
                    row.querySelector('[data-field="po_amount"]').textContent = amount;
                    row.querySelector('[data-field="height"]').textContent = height;
                    row.querySelector('[data-field="location"]').textContent = location;
                }
                
                closeEditDialog();
                alert('Record updated successfully');
                window.location.href='add.php'; // Reload the page to reflect changes
            } else {
                alert('Error updating record: ' + (data.message || 'Unknown error'));
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            alert('Error: Invalid response from server');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error updating record: ' + error.message);
    });
}

// Delete row function
function deleteRow(button) {
    if (confirm('Are you sure you want to delete this record?')) {
        const recordId = button.getAttribute('data-record-id');
        console.log('Deleting record ID:', recordId);
        
        // Send AJAX request to delete record
        fetch('delete_pole_record.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({id: recordId})
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            return response.text();
        })
        .then(text => {
            console.log('Delete raw response:', text);
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    const row = button.closest('tr');
                    row.remove();
                    alert('Record deleted successfully');
                } else {
                    alert('Error deleting record: ' + (data.message || 'Unknown error'));
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                alert('Error: Invalid response from server');
            }
        })
        .catch(error => {
            console.error('Delete fetch error:', error);
            alert('Error deleting record: ' + error.message);
        });
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editAmountDialog');
    if (event.target === modal) {
        closeEditDialog();
    }
}

// Clear search when input is empty
document.getElementById('searchInput').addEventListener('input', function() {
    if (this.value === '') {
        searchTable();
    }
});
</script>

</body>
</html>