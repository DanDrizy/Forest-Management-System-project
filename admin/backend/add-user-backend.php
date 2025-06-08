<?php
include_once '../../database/connection.php';

if (isset($_POST['submit'])) {
    // Get form data
    $firstname = htmlspecialchars($_POST['firstName']);
    $lastname = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $confirmPassword = $_POST['confirmPassword'];
    $password = trim($_POST['password']);
    $role = htmlspecialchars($_POST['role']);
    $phone = htmlspecialchars($_POST['phone']);
    $date = date("Y-m-d H:i");
    $status = 'blocked'; // Default status
    
    // Validate passwords match
    if ($password != $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
        echo "<script>window.location.href = '../pages/add-user.php';</script>";
        exit;
    }
    
    // Combine names
    $names = $firstname . " " . $lastname;
    
    // Create upload directory if it doesn't exist
    $upload_dir = "uploads/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . $_FILES['image']['name']; // Add timestamp to prevent duplicate filenames
        $target = $upload_dir . $image;
        $imageFileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        
        // Validate image type
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
            echo "<script>window.location.href = '../pages/add-user.php';</script>";
            exit;
        }
        
        // Insert user with image
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, password, email, role, status, phone, image, date) VALUES (:name, :password, :email, :role, :status, :phone, :image, :date)");
            $stmt->bindParam(':name', $names);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                echo "<script>alert('User added successfully!');</script>";
                echo "<script>window.location.href = '../pages/add-user.php';</script>";
            } else {
                echo "<script>alert('Failed to upload image, but user was added.');</script>";
                echo "<script>window.location.href = '../pages/add-user.php';</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            echo "<script>window.location.href = '../pages/add-user.php';</script>";
        }
    } else {
        // Insert user without image
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, password, email, role, status, phone, date) VALUES (:name, :password, :email, :role, :status, :phone, :date)");
            $stmt->bindParam(':name', $names);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            
            echo "<script>alert('User added successfully!');</script>";
            echo "<script>window.location.href = '../pages/add-user.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            echo "<script>window.location.href = '../pages/add-user.php';</script>";
        }
    }
}
?>