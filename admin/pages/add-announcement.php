<?php
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

    ?>

    <div class="main-content">
    <div class="container">
        <div class="page-header">
            <h1>Add New Announcement</h1>
            <p>Create and publish a new announcement to the list</p>
            <a href="announcement.php" class="btn-view-announcements">View All Announcements</a>
        </div>
        
        <div class="form-container">
            <form id="announcementForm" action="../backend/add-announcement-backend.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="announcement-title">Announcement Title</label>
                    <input type="text" id="announcement-title" name="announcement-title" class="form-control" placeholder="Enter a clear, concise title" required>
                </div>  
                
                <div class="form-group">
                    <label>Announcement Type</label>
                    <div class="announcement-type">
                        <input type="radio" name="announcement-type" id="all" class="type-option" value="All">
                        <label for="all" class="type-label label-event">All</label>

                        <input type="radio" name="announcement-type" id="type-urgent" class="type-option" value="admin">
                        <label for="type-urgent" class="type-label label-urgent">Admin</label>
                        
                        <input type="radio" name="announcement-type" id="type-event" class="type-option" value="Sales">
                        <label for="type-event" class="type-label label-event">Sales</label>
                        
                        <input type="radio" name="announcement-type" id="type-info" class="type-option" value="Pole Plant" checked>
                        <label for="type-info" class="type-label label-info">Pole Plant</label>

                        <input type="radio" name="announcement-type" id="sawmill" class="type-option" value="Sawmill" checked>
                        <label for="sawmill" class="type-label label-info">Sawmill</label>

                        <input type="radio" name="announcement-type" id="siliculture" class="type-option" value="Siliculture" checked>
                        <label for="siliculture" class="type-label label-info">Siliculture</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="announcement-date">Publication Date</label>
                    <input type="date" id="announcement-date" name="announcement-date" class="form-control" required>
                    <p class="form-note">If left blank, today's date will be used.</p>
                </div>
                
                <div class="form-group">
                    <label for="announcement-content">Announcement Content</label>
                    <textarea id="announcement-content" name="announcement-content" class="form-control" placeholder="Enter the detailed announcement content here..." required></textarea>
                </div>
                
                <button type="submit" class="btn-submit" id="previewBtn" name="ok">Make Announcement</button>
            
        </div>
        
        
        </form>
    </div>
    </div>
</body>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set default date to today
            const today = new Date();
            const formattedDate = today.toISOString().substr(0, 10);
            document.getElementById('announcement-date').value = formattedDate;
            
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
                
                // Update preview
                // document.getElementById('preview-date').textContent = formattedDate;
                // document.getElementById('preview-title').textContent = title;
                // document.getElementById('preview-content').textContent = content;
                
                // const previewTag = document.getElementById('preview-tag');
                // previewTag.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                
                // // Remove all classes and add the correct one
                // previewTag.className = 'preview-tag';
                // previewTag.classList.add('preview-' + type);
                
                // Show preview container
            //     document.getElementById('previewContainer').style.display = 'block';
                
            //     // Change border color based on type
            //     const previewContainer = document.getElementById('previewContainer');
            //     if (type === 'urgent') {
            //         previewContainer.style.borderLeftColor = '#e74c3c';
            //     } else if (type === 'event') {
            //         previewContainer.style.borderLeftColor = '#2ecc71';
            //     } else {
            //         previewContainer.style.borderLeftColor = '#f39c12';
            //     }
                
            //     // Scroll to preview
            //     previewContainer.scrollIntoView({ behavior: 'smooth' });
            // });
            
            // // Edit button click handler
            // document.getElementById('editBtn').addEventListener('click', function() {
            //     document.getElementById('previewContainer').style.display = 'none';
            //     document.getElementById('announcementForm').scrollIntoView({ behavior: 'smooth' });
            // });
            
            // Publish button click handler
            // document.getElementById('publishBtn').addEventListener('click', function() {
            //     alert('Announcement published successfully! In a real application, this would save the announcement to your database.');
            //     // Reset form
            //     document.getElementById('announcementForm').reset();
            //     document.getElementById('announcement-date').value = formattedDate;
            //     document.getElementById('previewContainer').style.display = 'none';
            // });
        // });
            });
        });
    </script>
</html>