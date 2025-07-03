<?php
    require_once '../../logs/backend/auth_check.php'; // Include the authentication check
    checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

    include'../../database/connection.php';
    $select = $pdo->query("SELECT * FROM germination WHERE del = 0 ");
    $fetch = $select->fetchAll();
    $i=1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/germ.css">
    <style>
        .search-box
        {
            padding: 10px;
        }
    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">
        
        <div class="sales-container-pp">
                                          
            <!-- Sales Table -->
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">Recent Sales</div>
                    <div class="table-actions">
                        <form method="post" action="export_germination.php" style="display: inline;">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fa fa-download"></i> Export
                            </button>
                        </form>
                        <a href="germination_map.php">
                            <button class="btn btn-success" style="margin-right: 10px;">
                                <i class="fa fa-map-marker"></i> View Map
                            </button>
                        </a>
                        <a href="add_germine.php">
                            <button class="btn btn-primary">
                            <i class="fa fa-plus"></i> New Germine
                        </button>
                        </a>
                    </div>
                </div>
                
                <!-- Search Box -->
                <div class="search-container" style="margin: 20px 0;">
                    <div class="search-box">
                        
                        <input type="text" id="searchInput" placeholder="Search by Germination Name here" 
                               style="width: 100%; padding: 10px 40px 10px 40px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
                
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tree Name</th>
                            <th>seed</th>
                            <th>Germination Start</th>
                            <th>Germination End</th>
                            <th>Soil Type</th>
                            <th>Recorded Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                                    
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="pagination">
                
                </div>
            </div>
                                      
        </div>
    </div>

    <!-- Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Germination Record</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    <input type="hidden" id="updateId" name="g_id">
                    
                    <div class="form-group">
                        <label for="updatePlantName">Tree Name:</label>
                        <input type="text" id="updatePlantName" name="plant_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateSeeds">Seeds:</label>
                        <input type="number" id="updateSeeds" name="seeds" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateGSdate">Germination Start Date:</label>
                        <input type="date" id="updateGSdate" name="g_sdate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateGEdate">Germination End Date:</label>
                        <input type="date" id="updateGEdate" name="g_edate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="updateSoil">Soil Type:</label>
                        <input type="text" id="updateSoil" name="soil" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-modal-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn-modal btn-modal-primary" onclick="updateRecord()">Update</button>
            </div>
        </div>
    </div>

    <script>
        // Store original data for filtering
        const originalData = <?php echo json_encode($fetch); ?>;
        
        // Function to render table rows
        function renderTable(data) {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px;">No results found</td></tr>';
                return;
            }
            
            data.forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${row.plant_name}</td>
                    <td>${row.seeds}</td>
                    <td>${row.g_sdate}</td>
                    <td>${row.g_edate}</td>
                    <td>${row.soil}</td>
                    <td>${row.curdate}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn action-edit" onclick="openUpdateModal(${row.g_id}, '${row.plant_name}', '${row.seeds}', '${row.g_sdate}', '${row.g_edate}', '${row.soil}')"><i class="fa fa-edit"></i></button>
                            <button class="action-btn action-delete" onclick="deleteRecord(${row.g_id}, '${row.plant_name}')"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }
        
        // Function to filter data based on search input
        function filterData(searchTerm) {
            const filtered = originalData.filter(row => {
                const searchString = searchTerm.toLowerCase();
                return (
                    row.plant_name.toLowerCase().includes(searchString) ||
                    row.seeds.toString().toLowerCase().includes(searchString) ||
                    row.soil.toLowerCase().includes(searchString) ||
                    row.g_sdate.toLowerCase().includes(searchString) ||
                    row.g_edate.toLowerCase().includes(searchString) ||
                    row.curdate.toLowerCase().includes(searchString)
                );
            });
            
            renderTable(filtered);
        }
        
        // Add event listener for search input
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            filterData(searchTerm);
        });
        
        // Initial render
        renderTable(originalData);
        
        // Modal Functions
        function openUpdateModal(id, plantName, seeds, gSdate, gEdate, soil) {
            document.getElementById('updateId').value = id;
            document.getElementById('updatePlantName').value = plantName;
            document.getElementById('updateSeeds').value = seeds;
            document.getElementById('updateGSdate').value = gSdate;
            document.getElementById('updateGEdate').value = gEdate;
            document.getElementById('updateSoil').value = soil;
            
            document.getElementById('updateModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('updateModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('updateModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Update Record Function
        function updateRecord() {
            const formData = new FormData();
            formData.append('id', document.getElementById('updateId').value);
            formData.append('plant_name', document.getElementById('updatePlantName').value);
            formData.append('seeds', document.getElementById('updateSeeds').value);
            formData.append('g_sdate', document.getElementById('updateGSdate').value);
            formData.append('g_edate', document.getElementById('updateGEdate').value);
            formData.append('soil', document.getElementById('updateSoil').value);
            formData.append('action', 'update');
            
            fetch('germination_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Record updated successfully!');
                    closeModal();
                    location.reload(); // Refresh the page to show updated data
                } else {
                    alert('Error updating record: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the record.');
            });
        }
        
        // Delete Record Function
        function deleteRecord(id, plantName) {
            if (confirm(`Are you sure you want to delete the record for "${plantName}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('g_id', id);
                formData.append('action', 'delete');
                
                fetch('germination_actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Record deleted successfully!');
                        location.reload(); // Refresh the page to show updated data
                    } else {
                        alert('Error deleting record: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the record.');
                });
            }
        }
    </script>
</body>
</html>