<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('admin');
include '../../database/connection.php';

// Function to get germination statistics
function getGerminationStats($pdo) {
    $query = "SELECT 
        COUNT(*) as total_germinations,
        SUM(seeds) as total_seeds_planted,
        COUNT(CASE WHEN g_status = 'send' THEN 1 END) as successful_germinations,
        COUNT(CASE WHEN g_status = 'unsend' THEN 1 END) as pending_germinations,
        ROUND(COUNT(CASE WHEN g_status = 'send' THEN 1 END) * 100.0 / COUNT(*), 2) as success_rate
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
        COUNT(CASE WHEN health = 'A' THEN 1 END) as healthy_plants,
        COUNT(CASE WHEN p_status = 'send' THEN 1 END) as ready_plants,
        ROUND(COUNT(CASE WHEN health = 'A' THEN 1 END) * 100.0 / COUNT(*), 2) as health_rate
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
        COUNT(CASE WHEN status = 'send' THEN 1 END) as processed_timber,
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
        COUNT(CASE WHEN l_status = 'send' THEN 1 END) as processed_logs,
        COUNT(CASE WHEN l_status = 'unsend-sawmill' THEN 1 END) as pending_sawmill
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

// Function to get monthly production trends
function getMonthlyTrends($pdo) {
    $query = "SELECT 
    strftime('%Y-%m', g_sdate) AS month,
    COUNT(*) AS germinations,
    SUM(seeds) AS seeds_planted
FROM germination
WHERE g_sdate >= date('now', '-12 months')
GROUP BY strftime('%Y-%m', g_sdate)
ORDER BY month;
";
    
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
$monthlyTrends = getMonthlyTrends($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Forestry Management Analysis</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .analysis-dashboard {
            padding: 20px;
            background: #0a1a1a;
            color: #fff;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #021A1A;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #26AD85;
        }
        
        .stat-card h3 {
            color: #26AD85;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #333;
        }
        
        .stat-value {
            font-weight: bold;
            color: #00ff9d;
        }
        
        .chart-container {
            background: #021A1A;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }
        
        .chart-title {
            color: #26AD85;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .pipeline-table {
            width: 100%;
            border-collapse: collapse;
            background: #021A1A;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .pipeline-table th,
        .pipeline-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        
        .pipeline-table th {
            background: #26AD85;
            color: #000;
            font-weight: bold;
        }
        
        .pipeline-table tr:hover {
            background: rgba(38, 173, 133, 0.1);
        }
        
        .efficiency-indicator {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .high-efficiency {
            background: #26AD85;
            color: #000;
        }
        
        .medium-efficiency {
            background: #ffa500;
            color: #000;
        }
        
        .low-efficiency {
            background: #ff4444;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include '../menu/menu.php'; ?>
    
    <div class="main-content">
        <?php include '../header/header.php'; ?>
        
        <div class="analysis-dashboard">
            <h1 style="color: #26AD85; text-align: center; margin-bottom: 30px;">
                Forestry Management Analysis Dashboard
            </h1>
            
            <!-- Key Performance Indicators -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>🌱 Germination Performance</h3>
                    <div class="stat-item">
                        <span>Total Germinations:</span>
                        <span class="stat-value"><?php echo number_format($germinationStats['total_germinations']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Seeds Planted:</span>
                        <span class="stat-value"><?php echo number_format($germinationStats['total_seeds_planted']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Success Rate:</span>
                        <span class="stat-value"><?php echo $germinationStats['success_rate']; ?>%</span>
                    </div>
                    <div class="stat-item">
                        <span>Pending:</span>
                        <span class="stat-value"><?php echo $germinationStats['pending_germinations']; ?></span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>🌿 Plant Health Analysis</h3>
                    <div class="stat-item">
                        <span>Total Plants:</span>
                        <span class="stat-value"><?php echo number_format($plantStats['total_plants']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Average Height:</span>
                        <span class="stat-value"><?php echo round($plantStats['avg_height'], 2); ?> cm</span>
                    </div>
                    <div class="stat-item">
                        <span>Average DBH:</span>
                        <span class="stat-value"><?php echo round($plantStats['avg_dbh'], 2); ?> cm</span>
                    </div>
                    <div class="stat-item">
                        <span>Health Rate:</span>
                        <span class="stat-value"><?php echo $plantStats['health_rate']; ?>%</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>🪵 Timber Production</h3>
                    <div class="stat-item">
                        <span>Total Timber Logs:</span>
                        <span class="stat-value"><?php echo number_format($timberStats['total_timber_logs']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Total Volume:</span>
                        <span class="stat-value"><?php echo number_format($timberStats['total_volume']); ?> m³</span>
                    </div>
                    <div class="stat-item">
                        <span>Total Amount:</span>
                        <span class="stat-value"><?php echo number_format($timberStats['total_amount']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Processing Rate:</span>
                        <span class="stat-value"><?php echo round(($timberStats['processed_timber'] / $timberStats['total_timber_logs']) * 100, 2); ?>%</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>📊 Logs Processing</h3>
                    <div class="stat-item">
                        <span>Total Log Entries:</span>
                        <span class="stat-value"><?php echo number_format($logsStats['total_logs']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Total Amount:</span>
                        <span class="stat-value"><?php echo number_format($logsStats['total_log_amount']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Processed Logs:</span>
                        <span class="stat-value"><?php echo $logsStats['processed_logs']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span>Pending Sawmill:</span>
                        <span class="stat-value"><?php echo $logsStats['pending_sawmill']; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Production Pipeline Analysis -->
            <div class="chart-container">
                <h2 class="chart-title">Production Pipeline by Plant Type</h2>
                <table class="pipeline-table">
                    <thead>
                        <tr>
                            <th>Plant Name</th>
                            <th>Seeds Planted</th>
                            <th>Plants Grown</th>
                            <th>Logs Produced</th>
                            <th>Timber Processed</th>
                            <th>Germination Rate</th>
                            <th>Efficiency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $productionPipeline->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo ucfirst($row['plant_name']); ?></td>
                            <td><?php echo number_format($row['seeds_planted']); ?></td>
                            <td><?php echo number_format($row['plants_grown']); ?></td>
                            <td><?php echo number_format($row['logs_produced']); ?></td>
                            <td><?php echo number_format($row['timber_processed']); ?></td>
                            <td><?php echo $row['germination_to_plant_rate']; ?>%</td>
                            <td>
                                <?php 
                                $efficiency = $row['germination_to_plant_rate'];
                                if($efficiency >= 80) {
                                    echo '<span class="efficiency-indicator high-efficiency">High</span>';
                                } elseif($efficiency >= 50) {
                                    echo '<span class="efficiency-indicator medium-efficiency">Medium</span>';
                                } else {
                                    echo '<span class="efficiency-indicator low-efficiency">Low</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Monthly Trends Chart -->
            <div class="chart-container">
                <h2 class="chart-title">Monthly Germination Trends</h2>
                <canvas id="monthlyTrendsChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Status Distribution Charts -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="chart-container">
                    <h3 class="chart-title">Germination Status Distribution</h3>
                    <canvas id="germinationStatusChart" width="400" height="400"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3 class="chart-title">Plant Health Distribution</h3>
                    <canvas id="plantHealthChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Monthly Trends Chart
        const monthlyData = [
            <?php 
            $monthlyTrends->execute(); // Re-execute to reset cursor
            $months = [];
            $germinations = [];
            while($row = $monthlyTrends->fetch(PDO::FETCH_ASSOC)) {
                $months[] = "'" . $row['month'] . "'";
                $germinations[] = $row['germinations'];
            }
            ?>
        ];
        
        new Chart(document.getElementById('monthlyTrendsChart'), {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', $months); ?>],
                datasets: [{
                    label: 'Germinations',
                    data: [<?php echo implode(',', $germinations); ?>],
                    borderColor: '#26AD85',
                    backgroundColor: 'rgba(38, 173, 133, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                },
                scales: {
                    x: { 
                        ticks: { color: '#fff' },
                        grid: { color: '#333' }
                    },
                    y: { 
                        ticks: { color: '#fff' },
                        grid: { color: '#333' }
                    }
                }
            }
        });
        
        // Germination Status Chart
        new Chart(document.getElementById('germinationStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Successful', 'Pending'],
                datasets: [{
                    data: [<?php echo $germinationStats['successful_germinations']; ?>, <?php echo $germinationStats['pending_germinations']; ?>],
                    backgroundColor: ['#26AD85', '#ffa500']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                }
            }
        });
        
        // Plant Health Chart
        new Chart(document.getElementById('plantHealthChart'), {
            type: 'doughnut',
            data: {
                labels: ['Healthy (A)', 'Others'],
                datasets: [{
                    data: [<?php echo $plantStats['healthy_plants']; ?>, <?php echo $plantStats['total_plants'] - $plantStats['healthy_plants']; ?>],
                    backgroundColor: ['#26AD85', '#ff4444']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                }
            }
        });
    </script>
</body>
</html>