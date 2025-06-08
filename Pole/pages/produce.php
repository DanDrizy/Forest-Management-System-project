<?php

// Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('pole plant'); // Check if the user is logged in and has the required role
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
        .menu-item.active-transfer {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }

        .active-btn {

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

        }

        .active-btn:hover {
            background-color: #00b865;
        }

        .unactive-btn {
            background-color: rgb(220, 0, 99);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            margin-left: 10px;
            transition: background-color 0.3s ease;

        }

        .unactive-btn:hover {

            background-color: rgb(195, 0, 88);


        }

        .btns {
            width: 8rem;
        }

        .received-btn {
            background-color: rgb(201, 201, 201);
            color: rgb(97, 97, 97);
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            margin-left: 10px;
            transition: background-color 0.3s ease;
            cursor: default;
            font-style: italic;
            font-size: 12px;

        }

        .stockin
        {
            color: darkred;
        }


    </style>
</head>

<body>
    <?php include '../menu/menu.php'; ?>

    <div class="main-content">

        <?php
        include '../header/header.php';

        include '../../database/connection.php';

        $i = 1;
        $select_pole_records = $pdo->query("SELECT * from pole");
        $select_pole_records->execute();
        $pole_records = $select_pole_records->fetchAll(PDO::FETCH_ASSOC);

        ?>


        <div class="dashboard-grid-saw">
            <div class="container">
                <h1>Harvest Trees</h1>

                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" placeholder="Search stock entries...">
                        <span class="search-icon"> <i class="fa fa-search"></i> </span>
                    </div>
                    <div class="button-container">
                        <a href="add_tree.php" style=" text-decoration: none; ">
                            <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Product
                            </button>
                        </a>
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
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Measure</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($pole_records as $log) { ?>
                            <tr data-id="1">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="checkbox stock-checkbox">
                                </td>
                                <td class="row-id"> <?php echo $i;
                                                    $i++; ?> </td>
                                <td> <?php echo $log['tree_name']; ?> </td>
                                <td> <?php echo $log['date']; ?> </td>
                                <td> <?php echo $log['amount']; ?> </td>
                                <td> <?php echo $log['volume'] . " " . $log['measure']; ?> </td>
                                <td> <?php echo $log['location']; ?> </td>
                                <td>



                                    <?php if ($log['status'] == 'active') { ?>
                                        <a href="../backend/activate.php?id=<?php echo $log['pole_transfer_id']; ?>&action=active" class="status-link ">
                                            <button class="unactive-btn btns"> <i class="fa fa-times"></i> Unactive</button>
                                        </a>
                                    <?php } else if ($log['status'] == 'unactive') { ?>
                                        <a href="../backend/activate.php?id=<?php echo $log['pole_transfer_id']; ?>&action=unactive" class="status-link ">
                                            <button class="active-btn btns"><i class="fa fa-check"></i> Active</button>
                                        </a>
                                    <?php } else if ($log['status'] == 'received') { ?>
                                        <!-- <a href="../backend/activate.php?id=<?php echo $log['pole_transfer_id']; ?>&action=unactive" class="status-link "> -->
                                        <button class="received-btn btns"> <i class="fa fa-check"></i> Received</button>
                                        <!-- </a> -->
                                        <!-- </a> -->
                                    <?php }else if ($log['status'] == 'instock') { ?>
                                        <!-- <a href="../backend/activate.php?id=<?php echo $log['pole_transfer_id']; ?>&action=unactive" class="status-link "> -->
                                        <button class="received-btn btns stockin "> <i class="fa fa-exchange"></i> inStock</button>
                                        <!-- </a> -->
                                        <!-- </a> -->
                                    <?php } ?>

                                </td>
                                <td class="action-buttons">

                                    <?php if ($log['status'] == 'active') { ?>

                                        <button class="received-btn"><i class="fa fa-check"></i>Disable</button>
                                    <?php } else if ($log['status'] == 'unactive') { ?>

                                        <a href="edit-transfer.php?id=<?php echo $log['pole_transfer_id']; ?>" style=" text-decoration: none; ">
                                            <button class="edit-btn">
                                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </a>

                                    <?php } else  if ($log['status'] == 'received' || $log['status'] == 'instock' ) { ?>


                                        <button class="received-btn"><i class="fa fa-check"></i>Disable</button>


                                    <?php } ?>
                                    <div class="delete-action" onclick="deleteRow(this)">
                                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>


                    </tbody>
                </table>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
            <script src="../js/check.js"></script>
        </div>

    </div>
</body>

</html>