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
        .menu-item.active-tree {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
</style>
<style></style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid-saw">
        <div class="container">
        <h1>Harvest Trees</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" placeholder="Search stock entries...">
                <span class="search-icon"> <i class="fa fa-search"></i> </span>
            </div>
            <div class="button-container">
                <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Tree
                </button>
                <button id="deleteSelected" class="delete-btn" abled>
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
                    <th>Country</th>
                    <th>Provance</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr data-id="1">
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox stock-checkbox">
                    </td>
                    <td class="row-id">1</td>
                    <td>2025-04-24</td>
                    <td>Douglas fir</td>
                    <td>250 units</td>
                    <td>Woodland Suppliers Ltd.</td>
                    <td><span class="status-badge status-received">Received</span></td>
                    <td class="action-buttons">
                        
                        <button class="edit-btn">
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
                <tr data-id="2">
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox stock-checkbox">
                    </td>
                    <td class="row-id">2</td>
                    <td>2025-04-25</td>
                    <td>Eucalyptus</td>
                    <td>120 units</td>
                    <td>Global Wood Co.</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td class="action-buttons">
                        
                        <button class="edit-btn">
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
                <tr data-id="3">
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox stock-checkbox">
                    </td>
                    <td class="row-id">3</td>
                    <td>2025-04-26</td>
                    <td>Cedar</td>
                    <td>350 units</td>
                    <td>Forest Products Inc.</td>
                    <td><span class="status-badge status-partial">Partial</span></td>
                    <td class="action-buttons">
                        
                        <button class="edit-btn">
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
                <tr data-id="4">
                    <td class="checkbox-cell">
                        <input type="checkbox" class="checkbox stock-checkbox">
                    </td>
                    <td class="row-id">4</td>
                    <td>2025-04-26</td>
                    <td>Pinus sylvestris</td>
                    <td>175 units</td>
                    <td>Northern Woods LLC</td>
                    <td><span class="status-badge status-received">Received</span></td>
                    <td class="action-buttons">
                        
                        <button class="edit-btn">
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
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="../js/check.js"></script>
        </div>
        
    </div>
</body>
</html>