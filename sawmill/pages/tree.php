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
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/search-delete.css">
    <style>
        .menu-item.active-tree {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid #00dc82;
        }
        
        /* Comment dialog styles */
        .comment-dialog {
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
        
        .comment-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .comment-content h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #00dc82;
            padding-bottom: 10px;
        }
        
        .comment-text {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #00dc82;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .dialog-buttons {
            text-align: right;
            margin-top: 20px;
        }
        
        .btn-close {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-close:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">

    <?php include'../header/header.php'; ?>

        <div class="dashboard-grid-saw">
        <div class="container">
        <h1>Received Harvest</h1>
        
        <div class="header-actions">
            <div class="search-bar">
                <input type="text" placeholder="Search stock entries..." id="searchInput">
                <span class="search-icon"> <i class="fa fa-search"></i> </span>
            </div>
        </div>
        
        <?php
        // Include the database connection file
        include_once '../../database/connection.php';
        
        // Fetch the tree data from the database
        $stmt = $pdo->prepare("SELECT * 
FROM logs
LEFT JOIN plant ON logs.p_id = plant.p_id
INNER JOIN germination ON plant.g_id = germination.g_id
WHERE logs.l_status = 'sent'");
        $stmt->execute();
        $trees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $i = 1; // Initialize the counter variable
        ?>
        
        <table id="stockTable">
            <thead>
                <tr>
                    <th class="row-id">ID</th>
                    <th>Name</th>
                    <th>Compartment</th>
                    <th>DBH</th>
                    <th>Health</th>
                    <th>Recorded</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trees as $tree): ?>
                <tr data-id="<?php echo $tree['p_id']; ?>">
                    <td class="row-id"><?php echo $i; $i++; ?></td>
                    <td><?php echo htmlspecialchars($tree['plant_name']); ?></td>
                    <td><?php echo htmlspecialchars($tree['Compartment']); ?></td>
                    <td><?php echo htmlspecialchars($tree['DBH']); ?></td>
                    <td><?php echo htmlspecialchars($tree['health']); ?></td>
                    <td><?php echo htmlspecialchars($tree['l_indate']); ?></td>
                    <td class="action-buttons">
                        <div class="view_comment delete-action" 
                             style="background:lightskyblue; color:darkblue; cursor:pointer;" 
                             title="View Comments" 
                             onclick="viewComment('<?php echo htmlspecialchars($tree['coments'], ENT_QUOTES); ?>')">
                            <i class="fa fa-eye"></i>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Comment Dialog -->
    <div id="commentDialog" class="comment-dialog">
        <div class="comment-content">
            <h3>Comment Details</h3>
            <div class="comment-text" id="commentText">
                <!-- Comment content will be inserted here -->
            </div>
            <div class="dialog-buttons">
                <button class="btn-close" onclick="closeCommentDialog()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Search functionality only
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('stockTable');
            const rows = table.querySelectorAll('tbody tr');

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Comment viewing functions
        function viewComment(comment) {
            const dialog = document.getElementById('commentDialog');
            const commentText = document.getElementById('commentText');
            
            // Check if comment is empty or null
            if (!comment || comment.trim() === '') {
                commentText.innerHTML = '<em style="color: #666;">No comments available for this entry.</em>';
            } else {
                commentText.textContent = comment;
            }
            
            dialog.style.display = 'flex';
        }

        function closeCommentDialog() {
            document.getElementById('commentDialog').style.display = 'none';
        }

        // Close dialog when clicking outside of it
        document.addEventListener('click', function(event) {
            const dialog = document.getElementById('commentDialog');
            if (event.target === dialog) {
                closeCommentDialog();
            }
        });

        // Close dialog with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCommentDialog();
            }
        });
    </script>
        </div>
    </div>
</body>
</html>