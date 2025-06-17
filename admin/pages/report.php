<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('admin'); // Check if the user is logged in and has the required role
include '../../database/connection.php';

// Function to get germination statistics
function getGerminationStats($pdo) {
    $query = "SELECT 
        COUNT(*) as total_germinations,
        SUM(seeds) as total_seeds_planted,
        SUM(CASE WHEN g_status = 'send' THEN 1 ELSE 0 END) as successful_germinations,
        SUM(CASE WHEN g_status = 'unsend' THEN 1 ELSE 0 END) as pending_germinations,
        ROUND(SUM(CASE WHEN g_status = 'send' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as success_rate
    FROM germination";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get plant health analysis
function getPlantHealthStats($pdo) {
    $query = "SELECT 
        COUNT(*) as total_plants,
        AVG(p_height) as avg_height,
        AVG(DBH) as avg_dbh,
        SUM(CASE WHEN health = 'A' THEN 1 ELSE 0 END) as healthy_plants,
        SUM(CASE WHEN p_status = 'send' THEN 1 ELSE 0 END) as ready_plants,
        ROUND(SUM(CASE WHEN health = 'A' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as health_rate
    FROM plant";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get timber production stats
function getTimberStats($pdo) {
    $query = "SELECT 
        COUNT(*) as total_timber_logs,
        SUM(t_volume) as total_volume,
        SUM(t_amount) as total_amount,
        SUM(CASE WHEN status = 'send' THEN 1 ELSE 0 END) as processed_timber,
        AVG(t_height) as avg_timber_height,
        AVG(t_width) as avg_timber_width
    FROM timber";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get logs analysis
function getLogsStats($pdo) {
    $query = "SELECT 
        COUNT(*) as total_logs,
        SUM(amount) as total_log_amount,
        AVG(height) as avg_log_height,
        SUM(CASE WHEN l_status = 'send' THEN 1 ELSE 0 END) as processed_logs,
        SUM(CASE WHEN l_status = 'unsend-sawmill' THEN 1 ELSE 0 END) as pending_sawmill
    FROM logs";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get production pipeline analysis
function getProductionPipeline($pdo) {
    $query = "SELECT 
        g.plant_name,
        COUNT(g.g_id) as seeds_planted,
        COUNT(p.p_id) as plants_grown,
        COUNT(l.l_id) as logs_produced,
        COUNT(t.t_id) as timber_processed,
        ROUND(COUNT(p.p_id) * 100.0 / COUNT(g.g_id), 2) as germination_to_plant_rate
    FROM germination g
    LEFT JOIN plant p ON g.g_id = p.g_id
    LEFT JOIN logs l ON p.p_id = l.p_id
    LEFT JOIN timber t ON l.l_id = t.L_id
    GROUP BY g.plant_name
    ORDER BY seeds_planted DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt;
}

// Get all statistics
$germinationStats = getGerminationStats($pdo);
$plantStats = getPlantHealthStats($pdo);
$timberStats = getTimberStats($pdo);
$logsStats = getLogsStats($pdo);
$productionPipeline = getProductionPipeline($pdo);

// Current date for report
$reportDate = date('F j, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Forestry Management Analysis Report</title>
    <link rel="stylesheet" href="../css/table.css">
    <style>
        .menu-item.active-report svg {
            color: #00ff9d;
        }

        .menu-item.active-report {
            border-left: 3px solid #00ff9d;
            color: white;
            background-color: rgba(0, 255, 157, 0.1);
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #021A1A;
            border-radius: 10px;
        }

        .report-title {
            color: #26AD85;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .report-date {
            color: #fff;
            font-size: 1.1em;
        }

        .report-section {
            margin-bottom: 40px;
        }

        .section-title {
            color: #26AD85;
            font-size: 1.5em;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #26AD85;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            background: #021A1A;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .report-table th,
        .report-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #333;
            color: #fff;
        }

        .report-table th {
            background: #26AD85;
            color: #000;
            font-weight: bold;
        }

        .report-table tr:nth-child(even) {
            background: rgba(38, 173, 133, 0.05);
        }

        .metric-value {
            font-weight: bold;
            color: #00ff9d;
        }

        .print-btn {
            background: #26AD85;
            color: #000;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .print-btn:hover {
            background: #00ff9d;
        }

        /* Print styles */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            
            .main-content {
                background: white !important;
            }
            
            .report-table {
                background: white !important;
            }
            
            .report-table th {
                background: #ddd !important;
                color: black !important;
            }
            
            .report-table td {
                color: black !important;
            }
            
            .report-header {
                background: white !important;
            }
            
            .report-title {
                color: black !important;
            }
            
            .section-title {
                color: black !important;
                border-bottom-color: black !important;
            }
            
            .metric-value {
                color: black !important;
            }
            
            .print-btn {
                display: none;
            }
            
            .sidebar, .header {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php include '../menu/menu.php'; ?>
    
    <div class="main-content">
        <?php include '../header/header.php'; ?>

        <div class="container">
            <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
            
            <div class="report-header">
                <h1 class="report-title">Forestry Management Analysis Report</h1>
                <p class="report-date">Generated on: <?php echo $reportDate; ?></p>
            </div>

            <!-- Executive Summary Table -->
            <div class="report-section">
                <h2 class="section-title">Executive Summary</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total Records</th>
                            <th>Success Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Germination</td>
                            <td class="metric-value"><?php echo number_format($germinationStats['total_germinations']); ?></td>
                            <td class="metric-value"><?php echo $germinationStats['success_rate']; ?>%</td>
                            <td><?php echo $germinationStats['pending_germinations']; ?> Pending</td>
                        </tr>
                        <tr>
                            <td>Plant Health</td>
                            <td class="metric-value"><?php echo number_format($plantStats['total_plants']); ?></td>
                            <td class="metric-value"><?php echo $plantStats['health_rate']; ?>%</td>
                            <td><?php echo $plantStats['ready_plants']; ?> Ready</td>
                        </tr>
                        <tr>
                            <td>Timber Production</td>
                            <td class="metric-value"><?php echo number_format($timberStats['total_timber_logs']); ?></td>
                            <td class="metric-value"><?php echo $timberStats['total_timber_logs'] > 0 ? round(($timberStats['processed_timber'] / $timberStats['total_timber_logs']) * 100, 2) : 0; ?>%</td>
                            <td><?php echo $timberStats['processed_timber']; ?> Processed</td>
                        </tr>
                        <tr>
                            <td>Logs Processing</td>
                            <td class="metric-value"><?php echo number_format($logsStats['total_logs']); ?></td>
                            <td class="metric-value"><?php echo $logsStats['total_logs'] > 0 ? round(($logsStats['processed_logs'] / $logsStats['total_logs']) * 100, 2) : 0; ?>%</td>
                            <td><?php echo $logsStats['pending_sawmill']; ?> Pending Sawmill</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Detailed Germination Report -->
            <div class="report-section">
                <h2 class="section-title">Germination Performance Details</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Germinations</td>
                            <td class="metric-value"><?php echo number_format($germinationStats['total_germinations']); ?></td>
                            <td>Total number of germination records</td>
                        </tr>
                        <tr>
                            <td>Seeds Planted</td>
                            <td class="metric-value"><?php echo number_format($germinationStats['total_seeds_planted']); ?></td>
                            <td>Total seeds planted across all batches</td>
                        </tr>
                        <tr>
                            <td>Successful Germinations</td>
                            <td class="metric-value"><?php echo $germinationStats['successful_germinations']; ?></td>
                            <td>Germinations marked as successful</td>
                        </tr>
                        <tr>
                            <td>Success Rate</td>
                            <td class="metric-value"><?php echo $germinationStats['success_rate']; ?>%</td>
                            <td>Percentage of successful germinations</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Plant Health Analysis -->
            <div class="report-section">
                <h2 class="section-title">Plant Health Analysis</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Plants</td>
                            <td class="metric-value"><?php echo number_format($plantStats['total_plants']); ?></td>
                            <td>Count</td>
                        </tr>
                        <tr>
                            <td>Average Height</td>
                            <td class="metric-value"><?php echo round($plantStats['avg_height'], 2); ?></td>
                            <td>cm</td>
                        </tr>
                        <tr>
                            <td>Average DBH</td>
                            <td class="metric-value"><?php echo round($plantStats['avg_dbh'], 2); ?></td>
                            <td>cm</td>
                        </tr>
                        <tr>
                            <td>Healthy Plants (Grade A)</td>
                            <td class="metric-value"><?php echo $plantStats['healthy_plants']; ?></td>
                            <td>Count</td>
                        </tr>
                        <tr>
                            <td>Health Rate</td>
                            <td class="metric-value"><?php echo $plantStats['health_rate']; ?>%</td>
                            <td>Percentage</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Production Pipeline by Plant Type -->
            <div class="report-section">
                <h2 class="section-title">Production Pipeline by Plant Type</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Plant Name</th>
                            <th>Seeds Planted</th>
                            <th>Plants Grown</th>
                            <th>Logs Produced</th>
                            <th>Timber Processed</th>
                            <th>Germination Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $productionPipeline->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo ucfirst($row['plant_name']); ?></td>
                            <td class="metric-value"><?php echo number_format($row['seeds_planted']); ?></td>
                            <td class="metric-value"><?php echo number_format($row['plants_grown']); ?></td>
                            <td class="metric-value"><?php echo number_format($row['logs_produced']); ?></td>
                            <td class="metric-value"><?php echo number_format($row['timber_processed']); ?></td>
                            <td class="metric-value"><?php echo $row['germination_to_plant_rate']; ?>%</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Timber and Logs Summary -->
            <div class="report-section">
                <h2 class="section-title">Timber & Logs Production Summary</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Metric</th>
                            <th>Value</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="4">Timber</td>
                            <td>Total Timber Logs</td>
                            <td class="metric-value"><?php echo number_format($timberStats['total_timber_logs']); ?></td>
                            <td>Count</td>
                        </tr>
                        <tr>
                            <td>Total Volume</td>
                            <td class="metric-value"><?php echo number_format($timberStats['total_volume']); ?></td>
                            <td>m³</td>
                        </tr>
                        <tr>
                            <td>Average Height</td>
                            <td class="metric-value"><?php echo round($timberStats['avg_timber_height'], 2); ?></td>
                            <td>m</td>
                        </tr>
                        <tr>
                            <td>Average Width</td>
                            <td class="metric-value"><?php echo round($timberStats['avg_timber_width'], 2); ?></td>
                            <td>cm</td>
                        </tr>
                        <tr>
                            <td rowspan="3">Logs</td>
                            <td>Total Logs</td>
                            <td class="metric-value"><?php echo number_format($logsStats['total_logs']); ?></td>
                            <td>Count</td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td class="metric-value"><?php echo number_format($logsStats['total_log_amount']); ?></td>
                            <td>Units</td>
                        </tr>
                        <tr>
                            <td>Average Height</td>
                            <td class="metric-value"><?php echo round($logsStats['avg_log_height'], 2); ?></td>
                            <td>m</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="noResults" class="no-results" style="display: none;">
                <p>No data found for the analysis report.</p>
            </div>
        </div>
    </div>
</body>
</html>