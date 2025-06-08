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
        .menu-item.active-report{
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }

        /* New CSS for additional features only */
        .date-filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .date-filters {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .date-filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .date-filter-group label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9em;
        }

        .date-filter-group input {
            padding: 8px 12px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.9em;
            transition: border-color 0.3s ease;
        }

        .date-filter-group input:focus {
            outline: none;
            border-color: #00dc82;
            box-shadow: 0 0 0 3px rgba(0, 220, 130, 0.1);
        }

        .filter-btn, .reset-btn, .export-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .filter-btn {
            background: #00dc82;
            color: white;
        }

        .filter-btn:hover {
            background: #00c474;
        }

        .reset-btn {
            background: #6c757d;
            color: white;
        }

        .reset-btn:hover {
            background: #5a6268;
        }

        .export-btn {
            background: #007bff;
            color: white;
        }

        .export-btn:hover {
            background: #0056b3;
        }

        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 6px;
            overflow: hidden;
            top: 100%;
            right: 0;
            border: 1px solid #dee2e6;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #f8f9fa;
        }

        .export-dropdown:hover .dropdown-content {
            display: block;
        }

        .results-info {
            margin-top: 15px;
            padding: 10px;
            background: #e8f4fd;
            border-radius: 6px;
            color: #0c5460;
            font-weight: 500;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .date-filters {
                flex-direction: column;
                align-items: stretch;
            }
            
            .date-filter-group {
                width: 100%;
            }
            
            .date-filter-group input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php 
    include'../menu/menu.php';
    include '../../database/connection.php';
    $select_report = $pdo->query("SELECT 
    g.*,
    p.*,
    l.height AS l_height,
    p.health AS l_health,
    l.amount AS l_amount,
    l_indate,
    l_status
FROM germination g
JOIN plant p ON p.g_id = g.g_id
JOIN logs l ON l.p_id = p.p_id
WHERE l.amount > 0 
  AND (l.l_status = 'unsend' OR l.l_status = 'unsend-sawmill');
 ");
    $select_report->execute();
    $fetch = $select_report->fetchAll();
    $i = 1;
    
    
    
    
    ?>
    
    <div class="main-content">
        <div class="dashboard-grid-saw">
            <div class="container">
                <h1>Sawmill Report</h1>
                
                <!-- New Date Filter Section -->
                <div class="date-filter-section">
                    <div class="date-filters">
                        <div class="date-filter-group">
                            <label for="startDate">Start Date:</label>
                            <input type="date" id="startDate" name="startDate">
                        </div>
                        <div class="date-filter-group">
                            <label for="endDate">End Date:</label>
                            <input type="date" id="endDate" name="endDate">
                        </div>
                        <button class="filter-btn" onclick="applyDateFilter()">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                        <button class="reset-btn" onclick="resetFilter()">
                            <i class="fa fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
                
                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search stock entries..." onkeyup="searchTable()">
                        <span class="search-icon"> <i class="fa fa-search"></i> </span>
                    </div>
                    <div class="button-container">
                        <!-- New Export Dropdown -->
                        <div class="export-dropdown">
                            <button class="export-btn">
                                <i class="fa fa-download"></i> Export
                            </button>
                            <div class="dropdown-content">
                                <a href="#" onclick="exportToCSV()"><i class="fa fa-file-o"></i> Export as CSV</a>
                                <a href="#" onclick="exportToExcel()"><i class="fa fa-file-excel-o"></i> Export as Excel</a>
                                <a href="#" onclick="exportToPDF()"><i class="fa fa-file-pdf-o"></i> Export as PDF</a>
                                <a href="#" onclick="printReport()"><i class="fa fa-print"></i> Print Report</a>
                            </div>
                        </div>
                        
                        <!-- <button id="deleteSelected" class="delete-btn" onclick="deleteSelected()">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button> -->
                    </div>
                </div>
                
                <table id="stockTable">
                    <thead>
                        <tr>
                            <!-- <th class="checkbox-cell">
                                <input type="checkbox" id="selectAll" class="checkbox" onchange="toggleAllCheckboxes()">
                            </th> -->
                            <th class="row-id">ID</th>
                            <th>Name</th>
                            <th>Logs Amount</th>
                            <th>DBH</th>
                            <th>Height</th>
                            <th>Created</th>
                            <th>Health</th>
                            <th>Status</th>
                            <!-- <th>Actions</th> -->
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach( $fetch as $data): ?>
                        <tr data-id="1" data-date="2025-04-24">
                            <!-- <td class="checkbox-cell">
                                <input type="checkbox" class="checkbox stock-checkbox">
                            </td> -->
                            <td class="row-id"><?php echo $i; $i++; ?></td>
                            <td><?php echo $data['plant_name'] ?></td>
                            <td><?php echo $data['l_amount'] ?></td>
                            <td><?php echo $data['DBH'] ?></td>
                            <td><?php echo $data['l_height'] ?></td>
                            <td><?php echo $data['l_indate'] ?></td>
                            <td><?php echo $data['l_health'] ?></td>
                            <td><?php echo $data['l_status'] == 'unsend' ? '<font color=green>Available</font>' : '<font color=darkred>Sawmill</font>'; ?></td>
                            
                        </tr>
                        <?php endforeach; ?>
                        
                    </tbody>
                </table>
                
                <!-- New Results Info -->
                <div class="results-info" id="resultsInfo">
                    Showing <span id="visibleRows">6</span> of <span id="totalRows">6</span> harvest entries
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="../js/check.js"></script>
    
    <script>
        // Initialize date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const thirtyDaysAgo = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            document.getElementById('startDate').value = thirtyDaysAgo;
            document.getElementById('endDate').value = today;
            
            updateResultsInfo();
        });

        // Enhanced search functionality
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('stockTable');
            const rows = table.getElementsByTagName('tr');
            let visibleCount = 0;

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }

            document.getElementById('visibleRows').textContent = visibleCount;
        }

        // Date filtering functionality
        function applyDateFilter() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates');
                return;
            }

            const start = new Date(startDate);
            const end = new Date(endDate);
            const rows = document.querySelectorAll('#tableBody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowDate = new Date(row.getAttribute('data-date'));
                
                if (rowDate >= start && rowDate <= end) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('visibleRows').textContent = visibleCount;
        }

        function resetFilter() {
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
            
            document.getElementById('searchInput').value = '';
            updateResultsInfo();
        }

        function updateResultsInfo() {
            const totalRows = document.querySelectorAll('#tableBody tr').length;
            const visibleRows = document.querySelectorAll('#tableBody tr:not([style*="display: none"])').length;
            
            document.getElementById('visibleRows').textContent = visibleRows;
            document.getElementById('totalRows').textContent = totalRows;
        }

        // Enhanced checkbox functionality
        function toggleAllCheckboxes() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.stock-checkbox');
            
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = selectAll.checked;
                }
            });
        }

        // Enhanced delete functionality
        function deleteRow(element) {
            if (confirm('Are you sure you want to delete this harvest entry?')) {
                const row = element.closest('tr');
                row.remove();
                updateResultsInfo();
            }
        }

        function deleteSelected() {
            const selectedCheckboxes = document.querySelectorAll('.stock-checkbox:checked');
            
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one entry to delete');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selectedCheckboxes.length} selected entries?`)) {
                selectedCheckboxes.forEach(checkbox => {
                    checkbox.closest('tr').remove();
                });
                
                document.getElementById('selectAll').checked = false;
                updateResultsInfo();
            }
        }

        // Export functionality
        function getVisibleTableData() {
            const data = [];
            const rows = document.querySelectorAll('#tableBody tr:not([style*="display: none"])');
            
            // Add headers
            data.push(['ID', 'Date', 'Species', 'Quantity', 'Supplier', 'Delivery Date']);
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const rowData = [
                    cells[1].textContent, // ID
                    cells[2].textContent, // Date
                    cells[3].textContent, // Species
                    cells[4].textContent, // Quantity
                    cells[5].textContent, // Supplier
                    cells[6].textContent  // Delivery Date
                ];
                data.push(rowData);
            });
            
            return data;
        }

        function exportToCSV() {
            const data = getVisibleTableData();
            const csvContent = data.map(row => row.join(',')).join('\n');
            
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `harvest_trees_report_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        function exportToExcel() {
            const data = getVisibleTableData();
            let html = '<table border="1">';
            
            data.forEach(row => {
                html += '<tr>';
                row.forEach(cell => {
                    html += `<td>${cell}</td>`;
                });
                html += '</tr>';
            });
            html += '</table>';
            
            const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `harvest_trees_report_${new Date().toISOString().split('T')[0]}.xls`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        function exportToPDF() {
            window.print();
        }

        function printReport() {
            const printWindow = window.open('', '_blank');
            const data = getVisibleTableData();
            
            let html = `
                <html>
                <head>
                    <title>Sawmill Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #2c3e50; text-align: center; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                        th { background-color: #00dc82; color: white; }
                        tr:nth-child(even) { background-color: #f9f9f9; }
                        .report-info { margin-bottom: 20px; color: #666; }
                    </style>
                </head>
                <body>
                    <h1>🌲 Harvest Trees Report</h1>
                    <div class="report-info">
                        <p>Generated on: ${new Date().toLocaleDateString()}</p>
                        <p>Total entries: ${data.length - 1}</p>
                    </div>
                    <table>
            `;
            
            data.forEach((row, index) => {
                html += index === 0 ? '<tr>' : '<tr>';
                row.forEach(cell => {
                    html += index === 0 ? `<th>${cell}</th>` : `<td>${cell}</td>`;
                });
                html += '</tr>';
            });
            
            html += `
                    </table>
                </body>
                </html>
            `;
            
            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>