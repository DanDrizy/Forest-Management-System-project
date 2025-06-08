<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Management - Sign Up</title>
    <link rel="stylesheet" href="css/signup-backend.css">
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
            width: 15px;
            height: 15px;
            position: relative;
            left: 12rem;
        
        }
        
        /* Adjust position for the second password field in row layout */
        .input-group.row .password-toggle:last-of-type {
            right: calc(50% + 10px);
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
        <h1 class="title">SignUp</h1>
    </div>
    <div class="form-container">
        <div class="form-content">
            <form id="signupForm" action="backend/signup-backend.php" method="post">
                <div class="input-group">
                    <select name="role" id="" class="input-field">
                        <option value="sales">Sales</option>
                        <option value="sawmill">Sawmill</option>
                        <option value="pole plant">Pole Plant</option>
                        <option value="Siliculture">Siliculture</option>
                    </select>
                    
                </div>
                <div class="input-group">
                    <input type="email" class="input-field" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="text" class="input-field" name="firstname" placeholder="First Name" id="password" required>
                    
                </div>
                <div class="input-group">
                    <input type="text" class="input-field" name="lastname" placeholder="Last Name" id="confirm-password" required>
                </div>
                <div class="input-group">
                    <input type="number" class="input-field" name="phone" placeholder="(+250) 070 000 000" id="confirm-password" required>
                </div>
                <div class="input-group row">
                    <input type="password" class="input-field" name="pass" placeholder="Password" id="password-field" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility('password-field')">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                    
                </div>
                <div class="input-group row">
                    
                <input type="password" class="input-field" name="cpass" placeholder="Confirm Password" id="confirm-password-field" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility('confirm-password-field')">
                        <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                    
                </div>
                <button type="submit" class="btn" name="signup">SignUp</button>
                <div class="login-link">
                    If you have account <a href="index.php">Click here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = event.currentTarget.querySelector('.eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                `;
            }
        }
    </script>
</body>
</html>