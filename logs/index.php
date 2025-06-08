<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Management - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Added styles for password toggle */
        .input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
            z-index: 10;
        }
        
        .eye-icon {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>
    <div class="image-container">
        <div class="overlay"></div>
        <svg class="logo" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="2"/>
            <path d="M50,10 Q65,40 50,90 Q35,40 50,10" fill="none" stroke="white" stroke-width="2"/>
            <path d="M30,50 L70,50" stroke="white" stroke-width="2"/>
            <path d="M30,40 L70,40" stroke="white" stroke-width="2"/>
            <path d="M35,30 L65,30" stroke="white" stroke-width="2"/>
        </svg>
        <h1 class="title">Login</h1>
    </div>
    <div class="form-container">
        <div class="form-content">
            <form id="login-form" method="post" action="backend/index-backend.php">
                <div class="input-group">
                    <input type="email" class="input-field" name="email" placeholder="Email" required>
                </div>
                <div class="input-group row">
                    <input type="password" name="pass" class="input-field" placeholder="Password" id="password-field" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility()">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
                <button type="submit" class="btn" name="ok">Login</button>
                <div class="login-link">
                    If you don't have account <a href="signup.php">Click here</a>
                </div>
            </form>
            
        </div>
    </div>
    
    <?php if(isset($error_message)): ?>
    <script>
        alert('<?php echo $error_message; ?>');
    </script>
    <?php endif; ?>

   
</body>
<script src="backend/eye.js"></script>
</html>