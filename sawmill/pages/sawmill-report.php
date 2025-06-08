<?php
// Include the database connection file
require_once '../../logs/backend/auth_check.php';
// Include the authentication check
checkUserAuth('sawmill');
// Check if the user is logged in and has the required role
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
    <style>
        .menu-item.active-report{
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        
        /* Date filter and export styles */
        .filter-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            flex-wrap: wrap;
        }
        
        .date-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .date-group label {
            font-weight: 500;
            color: #333;
        }
        
        .date-group input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .filter-btn, .export-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .filter-btn {
            background-color: #007bff;
            color: white;
        }
        
        .filter-btn:hover {
            background-color: #0056b3;
        }
        
        .export-btn {
            background-color: #28a745;
            color: white;
        }
        
        .export-btn:hover {
            background-color: #1e7e34;
        }
        
        .clear-btn {
            background-color: #6c757d;
            color: white;
        }
        
        .clear-btn:hover {
            background-color: #545b62;
        }
        
        .report-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .summary-card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-width: 150px;
        }
        
        .summary-card h3 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #007bff;
        }
        
        .summary-card p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php
    include '../menu/menu.php';
    include '../../database/connection.php'; // Include the database connection file
    
    // Get date filter parameters
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    
    // Build the query with date filtering
    $query = "SELECT 
        g.*,
        p.*,
        t.*,
        l.*,
        l.height AS l_height,
        p.health AS l_health,
        l.amount AS l_amount,
        l_indate,
        l_status 
    FROM germination g 
    JOIN plant p ON p.g_id = g.g_id 
    JOIN logs l ON l.p_id = p.p_id 
    JOIN timber t ON t.l_id = l.l_id 
    WHERE t.t_amount > 0 
    AND (t.status = 'unsend' OR t.status = 'send')";
    
    // Add date filtering if dates are provided
    if (!empty($start_date) && !empty($end_date)) {
        $query .= " AND DATE(t.t_indate) BETWEEN :start_date AND :end_date";
    } elseif (!empty($start_date)) {
        $query .= " AND DATE(t.t_indate) >= :start_date";
    } elseif (!empty($end_date)) {
        $query .= " AND DATE(t.t_indate) <= :end_date";
    }
    
    $query .= " ORDER BY t.t_indate DESC";
    
    $select_report = $pdo->prepare($query);
    
    // Bind parameters if dates are provided
    if (!empty($start_date)) {
        $select_report->bindParam(':start_date', $start_date);
    }
    if (!empty($end_date)) {
        $select_report->bindParam(':end_date', $end_date);
    }
    
    $select_report->execute();
    $fetch = $select_report->fetchAll();
    
    // Calculate summary statistics
    $total_records = count($fetch);
    $total_amount = 0;
    $unique_types = [];
    
    foreach ($fetch as $item) {
        $total_amount += $item['t_amount'];
        $unique_types[$item['type']] = true;
    }
    
    $i = 1;
    ?>

    <div class="main-content">
        
        <div class="dashboard-grid-saw">
            <div class="container">
                <h1>Sawmill Report</h1>
                
                <!-- Date Filter Section -->
                <div class="filter-section">
                    <form method="GET" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                        <div class="date-group">
                            <label for="start_date">From:</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        
                        <div class="date-group">
                            <label for="end_date">To:</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        
                        <button type="submit" class="filter-btn">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        
                        <a href="?" class="filter-btn clear-btn" style="text-decoration: none; display: inline-block;">
                            <i class="fa fa-refresh"></i> Clear
                        </a>
                        
                        <button type="button" class="export-btn" onclick="exportToCSV()">
                            <i class="fa fa-download"></i> Export CSV
                        </button>
                        
                        <button type="button" class="export-btn" onclick="exportToPDF()" style="background-color: #dc3545;">
                            <i class="fa fa-file-pdf-o"></i> Export PDF
                        </button>
                    </form>
                </div>
                
                <!-- Report Summary -->
               
                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" placeholder="Search stock entries..." id="searchInput">
                        <!-- <span class="search-icon"> <i class="fa fa-search"></i> </span> -->
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
                            <th>Type</th>
                            <th>HxW Size</th>
                            <th>Amount</th>
                            <th>Location</th>
                            <th>Inserted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fetch)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                                    No records found for the selected date range.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fetch as $item): ?>
                                <tr data-id="<?php echo $item['t_id']; ?>">
                                    <td class="checkbox-cell">
                                        <input type="checkbox" class="checkbox stock-checkbox">
                                    </td>
                                    <td class="row-id"><?php echo $i; $i++; ?></td>
                                    <td><?php echo htmlspecialchars($item['plant_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['type']); ?></td>
                                    <td><?php echo htmlspecialchars($item['t_height']); ?>x<?php echo htmlspecialchars($item['t_width']); ?> : <?php echo htmlspecialchars($item['size']); ?></td>
                                    <td><?php echo htmlspecialchars($item['t_amount']); ?></td>
                                    <td><?php echo htmlspecialchars($item['t_location']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($item['t_indate'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
            <script src="../js/check.js"></script>
            
            <script>
                // Search functionality
                $(document).ready(function() {
                    $("#searchInput").on("keyup", function() {
                        var value = $(this).val().toLowerCase();
                        $("#stockTable tbody tr").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });
                });

                // Export to CSV function
                function exportToCSV() {
                    var csv = [];
                    var rows = document.querySelectorAll("#stockTable tr");
                    
                    for (var i = 0; i < rows.length; i++) {
                        var row = [], cols = rows[i].querySelectorAll("td, th");
                        
                        for (var j = 1; j < cols.length; j++) { // Skip checkbox column
                            if (cols[j].innerText !== undefined) {
                                row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
                            }
                        }
                        csv.push(row.join(","));
                    }

                    // Create download link
                    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
                    var downloadLink = document.createElement("a");
                    downloadLink.download = "sawmill_report_" + new Date().toISOString().slice(0,10) + ".csv";
                    downloadLink.href = window.URL.createObjectURL(csvFile);
                    downloadLink.style.display = "none";
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }

                // Export to PDF function
                function exportToPDF() {
                    // Get current URL parameters for date filtering
                    var urlParams = new URLSearchParams(window.location.search);
                    var startDate = urlParams.get('start_date') || '';
                    var endDate = urlParams.get('end_date') || '';
                    
                    // Create form for PDF export
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'export_pdf.php'; // You'll need to create this file
                    form.target = '_blank';
                    
                    // Add hidden inputs for date range
                    if (startDate) {
                        var startInput = document.createElement('input');
                        startInput.type = 'hidden';
                        startInput.name = 'start_date';
                        startInput.value = startDate;
                        form.appendChild(startInput);
                    }
                    
                    if (endDate) {
                        var endInput = document.createElement('input');
                        endInput.type = 'hidden';
                        endInput.name = 'end_date';
                        endInput.value = endDate;
                        form.appendChild(endInput);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }

                // Auto-submit form when dates change (optional)
                document.getElementById('start_date').addEventListener('change', function() {
                    if (this.value && document.getElementById('end_date').value) {
                        this.form.submit();
                    }
                });

                document.getElementById('end_date').addEventListener('change', function() {
                    if (this.value && document.getElementById('start_date').value) {
                        this.form.submit();
                    }
                });
            </script>
        </div>
    </div>
</body>
</html>