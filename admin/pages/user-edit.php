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
    include '../../database/connection.php';
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
    ?>

    <div class="main-content">
        <div class="container">
            <div class="page-header">

                
            </div>

            <div class="form-container">
                <h2 class="form-title">Edit User Information</h2>   

                <form id="addUserForm" method="post" action="../backend/edit-user-backend.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">

                        <!-- id -->
                            <input type="text" name="id" id="id" class="form-control" value="<?php echo $user['id']; ?>" hidden>
                        <!-- image -->
                        <input type="hidden" name="current_image" value="<?php echo $user['image']; ?>">


                            <label for="firstName" class="required-field">Name</label>
                            <input type="text" name="names" id="firstName" class="form-control" value="<?php echo $user['name']; ?>" required>
                        </div>

                        
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="required-field">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="required-field">Phone Number</label>
                            <input type="number" name="phone" id="phone" class="form-control" value="<?php echo $user['phone']; ?>" required>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span class="section-title">Account Settings</span>
                    </div>

                    <div class="form-row">
                        

                            <div class="form-group">
                            <label>Profile Picture</label>
                            <div class="avatar-preview" id="avatarPreview" >
                                <?php if (!empty($user['image']) && file_exists('../backend/uploads/' . $user['image'])): ?>
                                    <img src="../backend/uploads/<?php echo $user['image']; ?>" alt="Avatar" class="avatar-image" style="width: 80px; height: 80px ; object-fit: cover" >
                                <?php else: ?>
                                <i class="fa fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" id="avatar" style="display: none;" accept="image/*" name="image">
                            <label for="avatar" class="upload-btn">Upload New Image</label>
                            <?php if (!empty($user['image'])): ?>
                                <p class="help-text">Leave empty to keep current image</p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="required-field">User Role</label>
                            <div class="role-options">
                                <input type="radio" name="role" id="role-admin" class="role-option" value="admin" <?php echo ($user['role'] == 'admin') ? 'checked' : ''; ?>>
                                <label for="role-admin" class="role-label admin-role">Admin</label>

                                <input type="radio" name="role" id="role-sawmill" class="role-option" value="sales" <?php echo ($user['role'] == 'sales') ? 'checked' : ''; ?>>
                                <label for="role-sawmill" class="role-label editor-role">sales</label>

                                <input type="radio" name="role" id="role-pp" class="role-option" value="pole plant" <?php echo ($user['role'] == 'pole plant') ? 'checked' : ''; ?>>
                                <label for="role-pp" class="role-label viewer-role">P-P</label>

                                <input type="radio" name="role" id="role-saler" class="role-option" value="sawmill" <?php echo ($user['role'] == 'sawmill') ? 'checked' : ''; ?>>
                                <label for="role-saler" class="role-label viewer-role">Sawmill</label>

                                <input type="radio" name="role" id="role-siliviculture" class="role-option" value="Siliculture" <?php echo ($user['role'] == 'Siliculture') ? 'checked' : ''; ?>>
                                <label for="role-siliviculture" class="role-label viewer-role">Siliviculture</label>
                            </div>
                            <span class="help-text">Select the appropriate role for this user</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group password-group">
                            <label for="password" class="required-field">Old Password</label>
                            <input type="password" id="password" class="form-control" name="password" value="<?php echo $user['password']; ?>"  required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>

                        <div class="form-group password-group">
                            <label for="confirmPassword" class="required-field">New Password</label>
                            <input type="password" id="confirmPassword" class="form-control" name="newPassword">
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
                        <button type="submit" class="btn-submit" name="submit"> <i class="fa fa-pencil"></i> Edit</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
            

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
                document.addEventListener('DOMContentLoaded', function() {
                // Initialize avatar preview with existing image if available
                const avatarPreview = document.getElementById('avatarPreview');
                const currentImage = "<?php echo (!empty($user['image']) && file_exists('uploads/' . $user['image'])) ? 'uploads/' . $user['image'] : ''; ?>";
                
                if (currentImage) {
                    avatarPreview.innerHTML = ''; // Clear any existing content
                    avatarPreview.style.backgroundImage = `url(${currentImage})`;
                    avatarPreview.style.backgroundSize = 'cover';
                    avatarPreview.style.backgroundPosition = 'center';
                }
                
                // Rest of your existing avatar change handler code
            });
            });
        </script>
    </div>
</body>
</html>