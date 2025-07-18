<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard with Logout Popup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
        }
        .user-align
        {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        p
        {
            font-size: 10px;
        }
        .user-avatar
        {
            overflow: hidden;
            object-fit: cover;
        }
        .user-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
     
        
        
        /* Overlay for the popup */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        /* Popup container */
        .logout-popup {
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        
        /* Popup title */
        .popup-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        
        /* Popup message */
        .popup-message {
            font-size: 1rem;
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        
        /* Buttons container */
        .popup-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        
        /* Button styles */
        .popup-button {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        /* Cancel button */
        .cancel-button {
            background-color: #f1f5f9;
            color: #64748b;
        }
        
        .cancel-button:hover {
            background-color: #e2e8f0;
        }
        
        /* Logout button */
        .logout-button {
            background-color:#1a1a1a;
            color: white;
        }
        
        .logout-button:hover {
            background-color: #1a1a1a;
        }
        
        /* Animation classes */
        .popup-overlay.active {
            display: flex;
            opacity: 1;
        }
        
        .popup-overlay.active .logout-popup {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <?php
    
    $id = $_SESSION['user_id'] ?? null;
    $select = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $select->execute([$id]);
    $user = $select->fetch(PDO::FETCH_ASSOC);
    $username = $user['name'];
    $profile_picture = $user['image'] ?? 'default-avatar.png'; // Default avatar if not set

    ?>
    <div class="header">
        <div class="dashboard-title">Admin Dashboard</div>
        <div class="user-profile" id="userProfile">
            <div class="user-avatar"> <img src="../backend/uploads/<?php echo $profile_picture; ?>" alt=""> </div>
            <div class="user-name user-align"><?php echo $username; ?> <p>Logout </p> </div>
        </div>
    </div>
    
    <!-- Logout Confirmation Popup -->
    <div class="popup-overlay" id="popupOverlay">
        <div class="logout-popup">
            <div class="popup-title">Sign Out</div>
            <div class="popup-message">
                Are you sure you want to sign out of your account?
                 You'll need to sign in again to access your dashboard.</div>
            <div class="popup-buttons">
                <button class="popup-button cancel-button" id="cancelButton">Cancel</button>
                <a href="../../logs/backend/logout.php"><button class="popup-button logout-button">Sign Out</button></a>
            </div>
        </div>
    </div>
    
    <script>
        // Get elements
        const userProfile = document.getElementById('userProfile');
        const popupOverlay = document.getElementById('popupOverlay');
        const cancelButton = document.getElementById('cancelButton');
        const logoutButton = document.getElementById('logoutButton');
        
        // Show popup when user profile is clicked
        userProfile.addEventListener('click', () => {
            popupOverlay.classList.add('active');
        });
        
        // Hide popup when cancel button is clicked
        cancelButton.addEventListener('click', () => {
            popupOverlay.classList.remove('active');
        });
        


        
        // Close popup when clicking on the overlay (outside the popup)
        popupOverlay.addEventListener('click', (e) => {
            if (e.target === popupOverlay) {
                popupOverlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>