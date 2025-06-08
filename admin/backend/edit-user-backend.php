<?php

include_once '../../database/connection.php';

if (isset($_POST['submit'])) {
    
    // Get form data

    $id = $_POST['id'];
    $names = htmlspecialchars($_POST['names']);
    $email = htmlspecialchars($_POST['email']);
    $oldPassword = trim($_POST['password']);
    $newPassword = trim($_POST['newPassword']); // Getting new password from form
    $role = htmlspecialchars($_POST['role']);
    $phone = htmlspecialchars($_POST['phone']);
    $date = date("Y-m-d H:i");
    $current_image = $_POST['current_image']; // Get the current image filename
    
    // Determine which password to use (keep old or use new)
    $passwordToUse = !empty($newPassword) ? $newPassword : $oldPassword;
    
    // Create upload directory if it doesn't exist
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Initialize image variable with current image

    
    $image = $current_image;
    
    // Handle image upload if a new image was uploaded


    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . $_FILES['image']['name']; // Add timestamp to prevent duplicate filenames
        $target = $upload_dir . $image;
        $imageFileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        
        // Validate image type
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            echo "<script>window.location.href = '../pages/edit-user.php?id=$id';</script>";
            exit;
        }
        
        // Process the file upload
        $upload_success = move_uploaded_file($_FILES['image']['tmp_name'], $target);
        if (!$upload_success) {
            echo "<script>alert('Failed to upload new image. Using existing image.');</script>";
            $image = $current_image; // Revert to current image if upload failed
        }
    }
    
    // Update user in the database
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, password = :password, email = :email, role = :role, phone = :phone, image = :image, date = :date WHERE id = :id");
        $stmt->bindParam(':name', $names);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordToUse);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        echo "<script>alert('User updated successfully!');</script>";
        echo "<script>window.location.href = '../pages/control.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        echo "<script>window.location.href = '../pages/edit-user.php?id=$id';</script>";
    }
}
?>