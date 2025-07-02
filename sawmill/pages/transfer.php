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
    <title>Timber Transfer Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../../Siliculture/css/transfer.css">
    <style>
        .menu-item.active-transfer {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        .send-action {
            background-color: #00dc82;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            margin-left: 10px;
            transition: background-color 0.3s ease;
            width: 8rem;
        }
        .sent {
            background-color: rgb(0, 49, 184);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            margin-left: 10px;
            transition: background-color 0.3s ease;
            width: 8rem;
        }
        .send-action:hover {
            background-color: #00b865;
        }
        .received {
            background-color: rgb(184, 0, 55);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            margin-left: 10px;
            transition: background-color 0.3s ease;
            width: 8rem;
        }
        .received-view {
            background: #fff;
            color: rgb(184, 0, 55);
        }
        .received-view:hover {
            background: #fff;
        }
        .transfer-detail {
            background: #f8f9fa;
            display: flex;
            flex-direction: row;
            color: #000000;
            border-bottom: 1px solid #dee2e6;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 3px;
        }
        .transfer-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .transfer-summary {
            border: 1px solid #00dc82;
            height: 20rem;
            overflow-y: auto;
            padding: 1rem;
            background: #fff;
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        .pagination a, .pagination span {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
            cursor: pointer;
        }
        
        .pagination a.active {
            background-color: #00dc82;
            color: white;
            border: 1px solid #00dc82;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        
        /* Status message styles */
        .status-message {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Mark rows that are already sent */
        tr.sent-log {
            background-color: #f0f0f0;
            color: #666;
        }
        
        tr.sent-log td {
            opacity: 0.6;
        }
        
        .transfer-form {
            margin-top: 2rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            margin-top: 1rem;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #00dc82;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #00b865;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }

        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .search-bar {
            display: flex;
            margin-bottom: 1rem;
        }

        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: #00dc82;
            color: white;
            border: 1px solid #00dc82;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #00b865;
        }
    </style>
</head>
<body>
    <?php 
    include '../menu/menu.php'; 
    include '../../database/connection.php';
    
    $records_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start_from = ($page - 1) * $records_per_page;

    // Get all log IDs for client-side persistence
    $all_logs_query = $pdo->query("SELECT l_id FROM logs");
    $all_log_ids = $all_logs_query->fetchAll(PDO::FETCH_COLUMN);
    ?>
    
    <div class="main-content">
        <div class="transfer-container">
            <h2 class="transfer-header">Transfer Timber to Sales</h2>
            
            <?php
            // Display success or error messages if they exist
            if (isset($_GET['success']) && $_GET['success'] == 'transfer_complete') {
                $count = isset($_GET['count']) ? (int)$_GET['count'] : 0;
                $message = isset($_GET['message']) ? $_GET['message'] : "Transfer completed successfully!";
                echo "<div class='status-message status-success'>";
                echo htmlspecialchars($message);
                echo "</div>";
            }
            
            if (isset($_GET['error'])) {
                echo "<div class='status-message status-error'>";
                if ($_GET['error'] == 'no_logs_selected') {
                    echo "Please select at least one timber log to transfer.";
                } else if ($_GET['error'] == 'transfer_failed') {
                    $message = isset($_GET['message']) ? $_GET['message'] : "Unknown error occurred.";
                    echo "Transfer failed: " . htmlspecialchars($message);
                } else {
                    echo "An error occurred.";
                }
                echo "</div>";
            }
            ?>
            
            <!-- Search and Filter Section -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search timber logs..." onkeyup="searchLogs()">
                <button type="button" onclick="searchLogs()"><i class="fa fa-search"></i></button>
            </div>
            
            <!-- Logs Selection Table -->
            <form id="transferForm" action="../backend/process_transfer.php" method="post">
                <div class="table-container">
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                                <th>ID</th>
                                <th>Plant Name</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Height</th>
                                <th>Width</th>
                                <!-- <th>Size</th>
                                <th>Volume</th>
                                <th>Location</th> -->
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <?php
                            // Fixed query with proper JOIN syntax and pagination
                            $select = $pdo->prepare("
                                SELECT 
                                    t.t_id, t.t_amount, t.type, t.t_height, t.t_width, 
                                    t.size, t.t_volume, t.t_location, t.t_indate, t.status,
                                    l.l_id as log_id,
                                    g.plant_name,
                                    g.g_id
                                FROM timber t
                                INNER JOIN logs l ON t.l_id = l.l_id
                                INNER JOIN plant p ON l.p_id = p.p_id
                                INNER JOIN germination g ON p.g_id = g.g_id AND t.status  = 'unsend'
                                ORDER BY t.t_indate DESC
                                LIMIT :start, :records_per_page
                            ");
                            $select->bindParam(':start', $start_from, PDO::PARAM_INT);
                            $select->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
                            $select->execute();
                            $logs = $select->fetchAll();

                            $i = $start_from + 1;
                    
                            foreach ($logs as $log) {
                                // Check if log has already been transferred
                                $is_transferred = isset($log['status']) && $log['status'] === 'send';
                                $row_class = $is_transferred ? 'sent-log' : '';

                                // Create data attributes for easier JavaScript access
                                $data_attributes = [
                                    'data-log-id' => $log['t_id'],
                                    'data-name' => htmlspecialchars($log['plant_name']),
                                    'data-amount' => $log['t_amount'],
                                    'data-type' => htmlspecialchars($log['type']),
                                    'data-height' => $log['t_height'],
                                    'data-width' => $log['t_width'],
                                    'data-size' => htmlspecialchars($log['size']),
                                    'data-volume' => $log['t_volume'],
                                    'data-location' => htmlspecialchars($log['t_location']),
                                    'data-date' => $log['t_indate'],
                                    'data-status' => $is_transferred ? 'send' : 'available'
                                ];
                                
                                $data_attr_string = '';
                                foreach ($data_attributes as $attr => $value) {
                                    $data_attr_string .= $attr . '="' . $value . '" ';
                                }

                                echo "<tr class='$row_class' $data_attr_string>";
                                
                                // Disable checkbox if already transferred
                                $disabled = $is_transferred ? 'disabled' : '';
                                echo "<td><input type='checkbox' class='log-checkbox' name='selected_logs[]' value='{$log['t_id']}' data-log-id='{$log['t_id']}' onchange='updateSelection(this)' $disabled></td>";
                                
                                echo "<td>{$i}</td>";
                                echo "<td>" . htmlspecialchars($log['plant_name']) . "</td>";
                                echo "<td>{$log['t_amount']}</td>";
                                echo "<td>" . htmlspecialchars($log['type']) . "</td>";
                                echo "<td>{$log['t_height']}</td>";
                                echo "<td>{$log['t_width']}</td>";
                                echo "<td>{$log['t_indate']}</td>";
                                
                                // Show transfer status
                                $status = $is_transferred ? 'Send' : 'Available';
                                echo "<td><span class='" . ($is_transferred ? 'send' : 'send-action') . "'>{$status}</span></td>";
                                
                                echo "</tr>";

                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Transfer Details Section -->
                <div class="transfer-form">
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Confirm Transfer</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.log-checkbox:not([disabled])');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }

        // Update selection when individual checkbox is changed
        function updateSelection(checkbox) {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.log-checkbox:not([disabled])');
            const checkedBoxes = document.querySelectorAll('.log-checkbox:checked:not([disabled])');
            
            // Update select all checkbox state
            if (checkedBoxes.length === checkboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedBoxes.length === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Search functionality
        function searchLogs() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('#logsTableBody tr');
            
            tableRows.forEach(row => {
                const plantName = row.getAttribute('data-name') || '';
                const type = row.getAttribute('data-type') || '';
                const location = row.getAttribute('data-location') || '';
                
                const searchableText = (plantName + ' ' + type + ' ' + location).toLowerCase();
                
                if (searchableText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Reset form
        function resetForm() {
            document.getElementById('transferForm').reset();
            document.getElementById('selectAll').checked = false;
            document.getElementById('selectAll').indeterminate = false;
            document.getElementById('searchInput').value = '';
            searchLogs(); // Show all rows
        }

        // Form validation before submission
        document.getElementById('transferForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one timber log to transfer.');
                return false;
            }
            
            if (!confirm(`Are you sure you want to transfer ${checkedBoxes.length} timber log(s) to Sales?`)) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>