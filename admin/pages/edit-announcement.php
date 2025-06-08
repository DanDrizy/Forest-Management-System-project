<?php
// Include the database connection file
require_once '../../database/connection.php'; // Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('admin'); // Check if the user is logged in and has the required role
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
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
    </style>
    <?php


    include '../menu/menu.php';

    $id = $_GET['id'];
    $select = $pdo->prepare("SELECT * FROM announcements WHERE an_id = :id");
    $select->bindValue(':id', $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);


    ?>

    <div class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>Edit Announcement</h1>
            
        </div>
        
        <div class="form-container">
            <form id="announcementForm" action="../backend/edit-announcement-backend.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="announcement-title">Announcement Title</label>
                    <input type="text" id="announcement-title" name="announcement-title" class="form-control" value="<?php echo $row['title']; ?>" placeholder="Enter a clear, concise title" required>
                </div>  
                
                <div class="form-group">
                    <label>Announcement Type</label>
                    <div class="announcement-type">

                    <!-- id -->
                        <input type="text" name="id" value="<?php echo $row['an_id']; ?>" hidden>
                    <!-- id -->


                        <input type="radio" name="announcement-type" id="all" class="type-option"  value="All" <?php echo ($row['type'] === 'All') ? 'checked' : ''; ?>>
                        <label for="all" class="type-label label-event">All</label>

                        <input type="radio" name="announcement-type" id="type-urgent" class="type-option" value="admin" <?php echo ($row['type'] === 'admin') ? 'checked' : ''; ?>>
                        <label for="type-urgent" class="type-label label-urgent">Admin</label>
                        
                        <input type="radio" name="announcement-type" id="type-event" class="type-option" value="Sales" <?php echo ($row['type'] === 'Sales') ? 'checked' : ''; ?>>
                        <label for="type-event" class="type-label label-event">Sales</label>
                        
                        <input type="radio" name="announcement-type" id="type-info" class="type-option" value="Pole Plant" <?php echo ($row['type'] === 'Pole Plant') ? 'checked' : ''; ?>>
                        <label for="type-info" class="type-label label-info">Pole Plant</label>

                        <input type="radio" name="announcement-type" id="sawmill" class="type-option" value="Sawmill" <?php echo ($row['type'] === 'Sawmill') ? 'checked' : ''; ?>>
                        <label for="sawmill" class="type-label label-info">Sawmill</label>

                        <input type="radio" name="announcement-type" id="siliculture" class="type-option" value="Siliculture" <?php echo ($row['type'] === 'Siliculture') ? 'checked' : ''; ?>>
                        <label for="siliculture" class="type-label label-info">Siliculture</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="announcement-date">Publication Date</label>
                    <input type="date" id="announcement-date" name="announcement-date" value="<?php echo $row['date']; ?>" class="form-control" required>
                    <p class="form-note">If left blank, today's date will be used.</p>
                </div>
                
                <div class="form-group">
                    <label for="announcement-content">Announcement Content</label>
                    <textarea id="announcement-content" name="announcement-content" class="form-control" placeholder="Enter the detailed announcement content here..." required><?php echo $row['content']; ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit" id="previewBtn" name="ok">Make Announcement</button>
            
        </div>
        
        
        </form>
    </div>
    </div>
</body>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            
            
            // Preview button click handler
            document.getElementById('previewBtn').addEventListener('click', function() {
                const title = document.getElementById('announcement-title').value;
                const content = document.getElementById('announcement-content').value;
                const dateInput = document.getElementById('announcement-date').value;
                
                // Validate form
                if (!title || !content) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Format date for display
                const date = new Date(dateInput);
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                const formattedDate = date.toLocaleDateString('en-US', options);
                
                // Get selected announcement type
                let type = 'info';
                const typeRadios = document.getElementsByName('announcement-type');
                for (const radio of typeRadios) {
                    if (radio.checked) {
                        type = radio.value;
                        break;
                    }
                }
               
            });
        });
    </script>
</html>