<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role


include '../../database/connection.php';

// Pagination settings
$records_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Get all log IDs for client-side persistence
$all_logs_query = $pdo->query("SELECT l_id FROM logs");
$all_log_ids = $all_logs_query->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Logs - Siliculture Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/transfer.css">
    <style>
        .transfer-detail {
            background: #00dc82;
            display: flex;
            flex-direction: row;
            color: #000000;
            border-bottom: 1px solid #000000;


        }

        .transfer-details {
            display: flex;
            flex-direction: row;
            gap: 2rem;


        }

        .transfer-summary {
            border: 1px solid #00dc82;
            height: 20rem;
            overflow-y: auto;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .pagination a,
        .pagination span {
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
            text-decoration: line-through;
        }
    </style>
</head>

<body>
    <?php include '../menu/menu.php'; ?>

    <div class="main-content">

        <div class="transfer-container">
            <h2 class="transfer-header">Transfer Logs to Sawmill</h2>

            <?php
            // Display success or error messages if they exist
            if (isset($_GET['success']) && $_GET['success'] == 'transfer_complete') {
                $count = isset($_GET['count']) ? (int)$_GET['count'] : 0;
                echo "<div class='status-message status-success'>";
                echo "Transfer completed successfully! $count logs have been sent to sawmill.";
                echo "</div>";
            }

            if (isset($_GET['error'])) {
                echo "<div class='status-message status-error'>";
                if ($_GET['error'] == 'no_logs_selected') {
                    echo "Please select at least one log to transfer.";
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
                <input type="text" id="searchInput" placeholder="Search logs...">
                <button type="button" onclick="searchLogs()"><i class="fa fa-search"></i></button>
            </div>

            <!-- Logs Selection Table -->
            <form id="transferForm" action="../backend/process_transfer.php" method="post">
                <div class="table-container">
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Volume-1</th>
                                <th>Volume-2</th>
                                <th>Dimention-2</th>
                                <th>Dimention-2</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <!-- This will be populated dynamically from the database with pagination -->
                            <?php
                            // Query with pagination
                            $select = $pdo->prepare("SELECT  *
                                                            FROM logs l
                                                            JOIN plant p ON l.p_id = p.p_id
                                                            JOIN germination g ON p.g_id = g.g_id
                                                            WHERE (l.l_status = 'unsend' OR l.l_status = 'unsend-sawmill')
                                                            LIMIT :start, :records_per_page;
                                                            ");


                            $select->bindParam(':start', $start_from, PDO::PARAM_INT);
                            $select->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
                            $select->execute();
                            $logs = $select->fetchAll();

                            $i = $start_from + 1;

                            foreach ($logs as $log) {
                                $select_plant = $pdo->prepare("SELECT * FROM logs,plant,germination WHERE logs.p_id = plant.p_id AND plant.g_id = germination.g_id AND plant.p_id = :p_id");
                                $select_plant->bindParam(':p_id', $log['p_id'], PDO::PARAM_INT);
                                $select_plant->execute();
                                $plant = $select_plant->fetch();

                                // Check if log has already been transferred
                                $is_transferred = isset($log['l_status']) && $log['l_status'] === 'sent';
                                $row_class = $is_transferred ? 'sent-log' : '';

                                echo "<tr data-log-id='{$log['l_id']}' data-name='{$plant['plant_name']}' data-amount='{$log['amount']}' data-volume 1='{$log['v1']} data-volume 2= {$log['v2']}' class='$row_class'>";

                                // Disable checkbox if already transferred
                                $disabled = $is_transferred ? 'disabled' : '';
                                echo "<td><input type='checkbox' class='log-checkbox' data-log-id='{$log['l_id']}' onclick='updateSelection(this)' $disabled></td>";

                                echo "<td>{$i}</td>";
                                echo "<td>{$plant['plant_name']}</td>";
                                echo "<td>{$log['amount']}</td>";
                                echo "<td>{$log['v1']}</td>";
                                echo "<td>{$log['v2']}</td>";
                                echo "<td>{$log['d1']}</td>";
                                echo "<td>{$log['d2']}</td>";
                                echo "<td>{$log['indate']}</td>";

                                // Show transfer status
                                $status = $is_transferred ? 'Sent' : 'Available';
                                echo "<td>{$status}</td>";

                                echo "</tr>";

                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Hidden input field to store selected log IDs -->
                <div id="selectedLogsContainer"></div>

                <!-- Pagination Links -->
                <div class="pagination">
                    <?php
                    // Count the total number of records
                    $total_records_query = $pdo->query("SELECT COUNT(*) FROM logs");
                    $total_records = $total_records_query->fetchColumn();
                    $total_pages = ceil($total_records / $records_per_page);

                    // Previous button
                    if ($page > 1) {
                        echo "<a href='javascript:void(0)' onclick='changePage(" . ($page - 1) . ")'>&laquo; Previous</a>";
                    }

                    // Page numbers
                    for ($i = 1; $i <= $total_pages; $i++) {
                        if ($i == $page) {
                            echo "<a class='active' href='javascript:void(0)' onclick='changePage($i)'>$i</a>";
                        } else {
                            echo "<a href='javascript:void(0)' onclick='changePage($i)'>$i</a>";
                        }
                    }

                    // Next button
                    if ($page < $total_pages) {
                        echo "<a href='javascript:void(0)' onclick='changePage(" . ($page + 1) . ")'>Next &raquo;</a>";
                    }
                    ?>
                </div>

                <!-- Transfer Details Section -->
                <div class="transfer-form">
                    <div class="transfer-summary" id="transferSummary">

                        <h3>Transfered Summary</h3>
                        <br>
                        <br>
                        <p>Selected logs : <span id="selectedCount">0</span></p>
                        <p>Total amount : <span id="totalAmount">0</span></p>
                        <div class="transfer-details" id="transferDetails">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Transfer Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Add any notes about this transfer..."></textarea>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary">Confirm Transfer</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Store selected logs in a Set to persist across pagination
        let selectedLogs = new Set();
        let logDetails = {};

        // Populate the all log IDs for initial state
        const allLogIds = <?php echo json_encode($all_log_ids); ?>;

        // Make the Transfer menu item active
        document.addEventListener('DOMContentLoaded', function() {
            const transferMenuItem = document.querySelector('.menu-item[href*="transfer"]');
            if (transferMenuItem) {
                transferMenuItem.classList.add('active-transfer');
            }

            // Initialize the form and restore selections
            initializeForm();
        });

        // Initialize form and restore selections from localStorage if available
        function initializeForm() {
            // Try to restore selections from localStorage
            const savedSelections = localStorage.getItem('transferLogSelections');
            if (savedSelections) {
                try {
                    const savedData = JSON.parse(savedSelections);
                    selectedLogs = new Set(savedData.selectedLogs);
                    logDetails = savedData.logDetails;

                    // Update checkboxes based on saved selections
                    updateCheckboxes();
                    // Update summary
                    updateTransferSummary();
                } catch (e) {
                    console.error("Error restoring selections:", e);
                    // Clear corrupted data
                    localStorage.removeItem('transferLogSelections');
                }
            }
        }

        // Save selections to localStorage
        function saveSelections() {
            localStorage.setItem('transferLogSelections', JSON.stringify({
                selectedLogs: Array.from(selectedLogs),
                logDetails: logDetails
            }));
        }

        // Change page with AJAX to prevent form reset
        function changePage(pageNum) {
            // Save current selections
            saveSelections();

            // Navigate to the new page
            window.location.href = "?page=" + pageNum;
        }

        // Update checkbox states based on selectedLogs Set
        function updateCheckboxes() {
            const checkboxes = document.querySelectorAll('.log-checkbox');
            checkboxes.forEach(checkbox => {
                const logId = checkbox.getAttribute('data-log-id');
                checkbox.checked = selectedLogs.has(logId);
            });

            // Update "select all" checkbox
            updateSelectAllCheckbox();
        }

        // Update the select all checkbox state
        function updateSelectAllCheckbox() {
            const checkboxes = document.querySelectorAll('.log-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');

            if (checkboxes.length === 0) return;

            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            const someChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }

        // Toggle all checkboxes
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.log-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                updateSelection(checkbox);
            });
        }

        // Update selection when a checkbox is clicked
        function updateSelection(checkbox) {
            const logId = checkbox.getAttribute('data-log-id');
            const row = checkbox.closest('tr');

            if (checkbox.checked) {
                selectedLogs.add(logId);
                // Store log details for the summary
                logDetails[logId] = {
                    id: row.cells[1].textContent,
                    name: row.getAttribute('data-name'),
                    amount: parseInt(row.getAttribute('data-amount')),
                    measure: row.getAttribute('data-measure')
                };
            } else {
                selectedLogs.delete(logId);
                delete logDetails[logId];
            }

            // Update the transfer summary
            updateTransferSummary();
            // Save current selections
            saveSelections();
            // Update select all checkbox state
            updateSelectAllCheckbox();
        }

        // Search logs functionality
        function searchLogs() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#logsTableBody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchInput) ? '' : 'none';
            });
        }

        // Update the transfer summary based on selected logs
        function updateTransferSummary() {
            document.getElementById('selectedCount').textContent = selectedLogs.size;

            let totalAmount = 0;
            const transferDetails = document.getElementById('transferDetails');
            transferDetails.innerHTML = '';

            // Generate the form inputs for submission
            const selectedLogsContainer = document.getElementById('selectedLogsContainer');
            selectedLogsContainer.innerHTML = '';

            // Add hidden inputs for each selected log
            selectedLogs.forEach(logId => {
                const details = logDetails[logId];
                if (!details) return;

                totalAmount += details.amount;

                // Create the display detail
                const detail = document.createElement('p');
                detail.classList.add('transfer-detail');
                detail.textContent = `Number: ${details.id}: ${details.name} = ${details.amount} Amount `;
                transferDetails.appendChild(detail);

                // Create hidden input for form submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_logs[]';
                hiddenInput.value = logId;
                selectedLogsContainer.appendChild(hiddenInput);
            });

            document.getElementById('totalAmount').textContent = totalAmount;
        }

        // Reset the form and clear selections
        function resetForm() {
            document.getElementById('transferForm').reset();
            document.getElementById('selectAll').checked = false;
            document.getElementById('selectAll').indeterminate = false;

            // Clear selections
            selectedLogs.clear();
            logDetails = {};

            // Update UI
            updateCheckboxes();
            updateTransferSummary();

            // Clear saved selections
            localStorage.removeItem('transferLogSelections');
        }
    </script>
</body>

</html>