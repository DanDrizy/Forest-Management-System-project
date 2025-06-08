<?php
require_once '../../logs/backend/auth_check.php';
checkUserAuth('pole plant');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Timber</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        
        .menu-item.active-tree {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        .search-bar input {
            padding: 8px 14px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .container h1 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<?php include '../menu/menu.php'; ?>

<div class="main-content">
    <?php 
    include '../header/header.php'; 
    include '../../database/connection.php';

    $select_sawmill_transfer = $pdo->query("
        SELECT *
        FROM timber
        INNER JOIN logs ON timber.l_id = logs.l_id
        INNER JOIN plant ON logs.p_id = plant.p_id
        INNER JOIN germination ON plant.g_id = germination.g_id
        WHERE t_amount > 0 AND del = 0 AND timber.status = 'unsend'
    ");

    $select_sawmill_transfer->execute();
    $sawmill_transfers = $select_sawmill_transfer->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    ?>

    <div class="dashboard-grid-saw">
        <div class="container">
            <h1><i class="fa fa-tree"></i> Available Timber</h1>

            <div class="header-actions">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search logs entries...">
                </div>
            </div>

            <table id="stockTable">
                <thead>
                    <tr>
                        <th class="row-id">ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sawmill_transfers as $sawmill_transfer): ?>
                        <tr>
                            <td class="row-id"><?php echo $i++; ?></td>
                            <td><?php echo $sawmill_transfer['plant_name']; ?></td>
                            <td><?php echo $sawmill_transfer['t_indate']; ?></td>
                            <td><?php echo $sawmill_transfer['t_amount']; ?></td>
                            <td><?php echo $sawmill_transfer['t_location']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/check.js"></script>
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#stockTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>

</body>
</html>
