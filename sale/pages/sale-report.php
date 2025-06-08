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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/report.css">
    <style>

        .stockin-color
        {
            border-bottom:2px solid  lightgreen;
            border-left:2px solid  lightgreen;
            color: rgb(19, 144, 3);

        }
        .stockout-color
        {
            border-bottom:2px solid  #ffa3f3;
            border-left:2px solid  #ffa3f3;
            color:rgb(144, 3, 125);
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

    </style>
</head>
<body>
    <?php 
    
    include'../menu/menu.php';
    include '../../database/connection.php'; // Include the database connection file
    $select_stock_item = "SELECT *
                            FROM stockin
                            LEFT JOIN stockout ON stockin.in_id = stockout.in_id
                            JOIN timber ON stockin.t_id = timber.t_id
                            JOIN logs ON timber.l_id = logs.l_id
                            JOIN plant ON logs.p_id = plant.p_id
                            JOIN germination ON plant.g_id = germination.g_id
                            WHERE stockout.out_amount > 0 OR stockout.out_id IS NULL
                            ORDER BY stockin.in_id DESC";
    $result = $pdo->query($select_stock_item);
    if (!$result) {
        die("Query failed: " . $pdo->errorInfo()[2]);
    }
    $stock_items = $result->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    
    ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        
        
        <div class="dashboard-grid-i">
        <div class="stock-container">
        <h1>Report</h1>
        
        <div class="header-actions">
            <a href="#" class="search-bar">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search stock entries..." onkeyup="searchTable()">
            </div>
            </a>
            <div class="button-container">
                <button id="addStockBtn" class="add-btn" onclick="exportToCSV()">
                    
                    <i class="fa fa-download"></i>
                    Export
                </button>
                
            </div>
        </div>
        
        <table id="stockTable">
            <thead>
                <tr>
                    
                    <th class="row-id">ID</th>
                    <th>Name</th>
                    <th class="stockin-color" >Stockin - Quantity</th>
                    <th class="stockin-color" >Price</th>
                    <th class="stockout-color" >Stockout - Quantity</th>
                    <th class="stockout-color" >Price</th>
                    <th class="stockin-color" >stocked in Date</th>
                    <th class="stockout-color" >stocked out Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stock_items as $item): ?>
                    <tr data-id="1">
                    
                    <td class="row-id"><?php echo $i; $i++; ?></td>
                    <td><?php echo $item['plant_name'] ?></td>
                    <td class="stockin-color" ><?php echo number_format($item['s_amount']) ?? 0; ?></td>
                    <td class="stockin-color" > <?php echo number_format($item['price']) ?? 0 ?> Rwf</td>
                    <td class="stockout-color" > <?php echo number_format($item['out_amount']) ?? 0 ?> </td>
                    <td class="stockout-color" > <?php echo number_format($item['out_price']) ?? 0 ?> Rwf</td>
                    <td class="stockin-color" > <?php echo $item['s_indate'] ?? 0; ?> </td>
                    <td class="stockout-color" > <?php echo $item['out_date'] ?? 'No Stock-out'; ?> </td>
                    
                </tr>
                <?php endforeach; ?>
                <!-- <tr data-id="2">
                    
                    <td class="row-id">2</td>
                    <td>Product-Name</td>
                    <td>Eucalyptus</td>
                    <td>$250</td>
                    <td><span class="status-badge status-partial">Stock-In</span></td>
                    <td> 22-01-2022 </td>

                    
                </tr> -->
                
            </tbody>
        </table>
        
        <div id="no-results" class="no-results" style="display: none;">
            No results found for your search.
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
            let visibleRows = 0;
            
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                // Search through all cells in the row
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
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
            const noResults = document.getElementById('no-results');
            if (visibleRows === 0 && filter !== '') {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }
        
        // Export to CSV functionality
        function exportToCSV() {
            const table = document.getElementById('stockTable');
            const rows = table.querySelectorAll('tr');
            let csvContent = '';
            
            // Get headers
            const headers = [];
            const headerCells = rows[0].querySelectorAll('th');
            headerCells.forEach(cell => {
                headers.push('"' + cell.textContent.replace(/"/g, '""') + '"');
            });
            csvContent += headers.join(',') + '\n';
            
            // Get visible data rows only
            const tbody = table.getElementsByTagName('tbody')[0];
            const dataRows = tbody.getElementsByTagName('tr');
            
            for (let i = 0; i < dataRows.length; i++) {
                // Only export visible rows
                if (dataRows[i].style.display !== 'none') {
                    const cells = dataRows[i].querySelectorAll('td');
                    const rowData = [];
                    
                    cells.forEach(cell => {
                        let cellText = cell.textContent || cell.innerText;
                        cellText = cellText.replace(/"/g, '""'); // Escape quotes
                        rowData.push('"' + cellText + '"');
                    });
                    
                    csvContent += rowData.join(',') + '\n';
                }
            }
            
            // Create and download the file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'stock_report_' + new Date().toISOString().split('T')[0] + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        
        // Remove the old openAddModal function call since we're using exportToCSV now
        // You can remove the onclick="openAddModal()" from the button if it exists elsewhere
    </script>
        </div>
        
        
    </div>
</body>
</html>