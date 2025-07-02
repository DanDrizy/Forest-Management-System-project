<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('admin'); // Check if the user is logged in and has the required role

include '../../database/connection.php'; // Include the database connection
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/\admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Forestry Management System</title>
    <style></style>
</head>

<body>
    <style>
        .menu-item.active-annouce svg {
            color: #00ff9d;
        }

        .menu-item.active-annouce {
            border-left: 3px solid #00ff9d;
            color: white;
            background-color: rgba(0, 255, 157, 0.1);
        }

        .announcements-header button {
            padding: 10px;
            margin: 10px;
            border: none;
            background: #3498db;
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
        }
        
        .announcement-item {
            position: relative;
        }
        
        .action-buttons {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            color: grey;
            border: none;
            background: none;
            padding: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.2s;
        }
        
        .delete-btn:hover {
            color: #e74c3c;
        }
        
        .update-btn:hover {
            color: #3498db;
        }
        
        .confirmation-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .dialog-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .dialog-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .dialog-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            min-width: 100px;
        }
        
        .confirm-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .cancel-delete {
            background-color: #7f8c8d;
            color: white;
        }
    </style>
    <?php
    include '../menu/menu.php';
    ?>

    <div class="main-content">
        <?php include '../header/header.php'; ?>

        <div>
            <div class="announcements-container">
                <div class="announcements-header">
                    <div class="left">
                        <h1>Announcements </h1>
                        <p>Stay updated with the latest news and events</p>
                    </div>
                    <div class="right">
                        <a href="add-announcement.php"><button>Add New Announcement</button></a>
                    </div>
                </div>

                <ul class="announcements-list">
                    <?php

                    $select_data = $pdo->query("SELECT * FROM announcements ORDER BY date DESC");
                    while ($row = $select_data->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['type'] == 'admin') {
                    ?>
                            <li class="announcement-item urgent">
                                <span class="announcement-date"><?php echo $row['date']; ?></span>
                                <h3 class="announcement-title"><?php echo $row['title']; ?></h3>
                                <p class="announcement-content"><?php echo $row['content']; ?></p>
                                <span class="announcement-tag tag-urgent">Admin</span>
                                <div class="action-buttons">
                                    <a href="edit-announcement.php?id=<?php echo $row['an_id']; ?>">
                                        <button class="action-btn update-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <button class="action-btn delete-btn" onclick="showDeleteConfirmation(<?php echo $row['an_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                        <?php } else if ($row['type'] == 'Sales') { ?>
                            <li class="announcement-item event">
                                <span class="announcement-date"><?php echo $row['date']; ?></span>
                                <h3 class="announcement-title"><?php echo $row['title']; ?></h3>
                                <p class="announcement-content"><?php echo $row['content']; ?></p>
                                <span class="announcement-tag tag-event">Saler</span>
                                <div class="action-buttons">
                                    <a href="edit-announcement.php?id=<?php echo $row['an_id']; ?>">
                                        <button class="action-btn update-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <button class="action-btn delete-btn" onclick="showDeleteConfirmation(<?php echo $row['an_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                        <?php } else if ($row['type'] == 'Sawmill') { ?>
                            <li class="announcement-item info">
                                <span class="announcement-date"><?php echo $row['date']; ?></span>
                                <h3 class="announcement-title"><?php echo $row['title']; ?></h3>
                                <p class="announcement-content"><?php echo $row['content']; ?></p>
                                <span class="announcement-tag tag-info">Sawmill</span>
                                <div class="action-buttons">
                                    <a href="edit-announcement.php?id=<?php echo $row['an_id']; ?>">
                                        <button class="action-btn update-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <button class="action-btn delete-btn" onclick="showDeleteConfirmation(<?php echo $row['an_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                        <?php } else if ($row['type'] == 'Siliculture') { ?>
                            <li class="announcement-item">
                                <span class="announcement-date"><?php echo $row['date']; ?></span>
                                <h3 class="announcement-title"><?php echo $row['title']; ?></h3>
                                <p class="announcement-content"><?php echo $row['content']; ?></p>
                                <span class="announcement-tag tag-info">Sillivicalture</span>
                                <div class="action-buttons">
                                    <a href="edit-announcement.php?id=<?php echo $row['an_id']; ?>">
                                    <button class="action-btn update-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <button class="action-btn delete-btn" onclick="showDeleteConfirmation(<?php echo $row['an_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                        <?php } else if ($row['type'] == 'pole plant') { ?>
                            <li class="announcement-item">
                                <span class="announcement-date"><?php echo $row['date']; ?></span>
                                <h3 class="announcement-title"><?php echo $row['title']; ?></h3>
                                <p class="announcement-content"><?php echo $row['content']; ?></p>
                                <span class="announcement-tag tag-info">Pole Plant</span>
                                <div class="action-buttons">
                                    <a href="edit-announcement.php?id=<?php echo $row['an_id']; ?>">
                                        <button class="action-btn update-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <button class="action-btn delete-btn" onclick="showDeleteConfirmation(<?php echo $row['an_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                    
                    <?php } else if ($row['type'] == 'All') { ?>
                            <li class="announcement-item">
                                <span class="announcement-date"><?php echo $row['date']; ?></span>
                                <h3 class="announcement-title"><?php echo $row['title']; ?></h3>
                                <p class="announcement-content"><?php echo $row['content']; ?></p>
                                <span class="announcement-tag tag-event">All Users</span>
                                <div class="action-buttons">
                                    <a href="edit-announcement.php?id=<?php echo $row['an_id']; ?>">
                                        <button class="action-btn update-btn"><i class="fa-solid fa-pen-to-square"></i></button>
                                    </a>
                                    <button class="action-btn delete-btn" onclick="showDeleteConfirmation(<?php echo $row['an_id']; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Confirmation Dialog -->
    <div id="deleteConfirmationDialog" class="confirmation-dialog">
        <div class="dialog-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this announcement?</p>
            <div class="dialog-buttons">
                <button class="cancel-delete" onclick="closeDeleteConfirmation()">Cancel</button>
                <button class="confirm-delete" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
    
    <script>
        // Store the announcement ID to be deleted
        let announcementToDelete = null;
        
        // Show confirmation dialog
        function showDeleteConfirmation(anId) {
            announcementToDelete = anId;
            const dialog = document.getElementById('deleteConfirmationDialog');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            
            // Show the dialog
            dialog.style.display = 'flex';
            
            // Set up the confirm button action
            confirmBtn.onclick = function() {
                if (announcementToDelete) {
                    window.location.href = '../backend/announcement-delete.php?id=' + announcementToDelete;
                }
            };
        }
        
        // Close confirmation dialog
        function closeDeleteConfirmation() {
            const dialog = document.getElementById('deleteConfirmationDialog');
            dialog.style.display = 'none';
            announcementToDelete = null; // Reset the ID
        }
        
        // Close dialog if user clicks outside the dialog content
        window.onclick = function(event) {
            const dialog = document.getElementById('deleteConfirmationDialog');
            if (event.target === dialog) {
                closeDeleteConfirmation();
            }
        };
    </script>
</body>

</html>