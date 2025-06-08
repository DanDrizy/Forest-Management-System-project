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
    <title>Forestry Management System</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php
    include '../menu/menu.php';
    ?>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Add New User</h1>
                
            </div>

            <div class="form-container">
                <h2 class="form-title">Add User Information</h2>

                <form id="addUserForm" method="post" action="../backend/add-user-backend.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName" class="required-field">First Name</label>
                            <input type="text" name="firstName" id="firstName" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="lastName" class="required-field">Last Name</label>
                            <input type="text" name="lastName" id="lastName" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="required-field">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="required-field">Phone Number</label>
                            <input type="number" name="phone" id="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span class="section-title">Account Settings</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Profile Picture</label>
                            <div class="avatar-preview" id="avatarPreview"> <i class="fa fa-user"></i> </div>
                            <input type="file" id="avatar" style="display: none;" accept="image/*" name="image">
                            <label for="avatar" class="upload-btn">Upload Image</label>
                        </div>

                        <div class="form-group">
                            <label class="required-field">User Role</label>
                            <div class="role-options">
                                <input type="radio" name="role" id="role-admin" class="role-option" value="admin">
                                <label for="role-admin" class="role-label admin-role">Admin</label>

                                <input type="radio" name="role" id="role-sawmill" class="role-option" value="sawmill" checked>
                                <label for="role-sawmill" class="role-label editor-role">sales</label>

                                <input type="radio" name="role" id="role-pp" class="role-option" value="pole plant">
                                <label for="role-pp" class="role-label viewer-role">P-P</label>

                                <input type="radio" name="role" id="role-saler" class="role-option" value="sales">
                                <label for="role-saler" class="role-label viewer-role">Sawmill</label>

                                <input type="radio" name="role" id="role-siliviculture" class="role-option" value="Siliculture">
                                <label for="role-siliviculture" class="role-label viewer-role">Siliviculture</label>
                            </div>
                            <span class="help-text">Select the appropriate role for this user</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group password-group">
                            <label for="password" class="required-field">Password</label>
                            <input type="password" id="password" class="form-control" name="password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>

                        <div class="form-group password-group">
                            <label for="confirmPassword" class="required-field">Confirm Password</label>
                            <input type="password" id="confirmPassword" class="form-control" name="confirmPassword" required>
                            <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                    <button class="btn-back"><i class="fa fa-arrow-circle-left"></i> cancel</i></button>
                        <button type="submit" class="btn-submit" name="submit">Add User</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Update avatar preview based on first and last name
                function updateAvatarPreview() {
                    const firstName = document.getElementById('firstName').value;
                    const lastName = document.getElementById('lastName').value;

                    let initials = '';
                    if (firstName) initials += firstName.charAt(0).toUpperCase();
                    if (lastName) initials += lastName.charAt(0).toUpperCase();

                    if (initials) {
                        document.getElementById('avatarPreview').textContent = initials;
                    } else {
                        document.getElementById('avatarPreview').innerHTML = '<i class="fa fa-user"></i>';
                    }
                }

                document.getElementById('firstName').addEventListener('input', updateAvatarPreview);
                document.getElementById('lastName').addEventListener('input', updateAvatarPreview);

                // Toggle password visibility
                function togglePasswordVisibility(inputId, buttonId) {
                    const passwordInput = document.getElementById(inputId);
                    const toggleButton = document.getElementById(buttonId);

                    toggleButton.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);

                        // Update icon
                        if (type === 'text') {
                            this.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        `;
                        } else {
                            this.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        `;
                        }
                    });
                }

                togglePasswordVisibility('password', 'passwordToggle');
                togglePasswordVisibility('confirmPassword', 'confirmPasswordToggle');

                // Form validation for password matching
                document.getElementById('addUserForm').addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;

                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match. Please try again.');
                    }
                });

                // Back button functionality
                const backButton = document.querySelector('.btn-back');
                if (backButton) {
                    backButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (document.getElementById('firstName').value ||
                            document.getElementById('lastName').value ||
                            document.getElementById('email').value ||
                            document.getElementById('password').value) {
                            if (confirm('Are you sure you want to go back? Any unsaved changes will be lost.')) {
                                window.location.href = 'control.php';
                            }
                        } else {
                            window.location.href = 'control.php';
                        }
                    });
                }

                // Handle file upload for avatar
                document.getElementById('avatar').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const avatarPreview = document.getElementById('avatarPreview');
                            avatarPreview.innerHTML = ''; // Clear any existing content
                            avatarPreview.style.backgroundImage = `url(${e.target.result})`;
                            avatarPreview.style.backgroundSize = 'cover';
                            avatarPreview.style.backgroundPosition = 'center';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>
    </div>
</body>
</html>