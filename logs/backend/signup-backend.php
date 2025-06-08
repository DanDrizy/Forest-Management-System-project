<?php include '../../database/connection.php'; 
if(isset($_POST['signup'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];
    $role = $_POST['role'];
    $date = date('Y-m-d H:i');
    $phone = $_POST['phone'];
    
    if($pass == $cpass) {
        // $pass = md5($pass);
        
        // Modified query to check for both email AND role
        $sql = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
        $result = $pdo->query($sql);
        
        if($result->fetch()) {
            echo "<script>alert('Account with this email and role already exists'); window.location.href = '../signup.php'; </script>";
        } else {
            $name = $firstname." ".$lastname;
            $sql = "INSERT INTO users(name,email,password,role,status,phone,date) VALUES('$name','$email','$pass','$role','blocked',$phone,'$date')";
            $result = $pdo->query($sql);
            
            if($result) {
                echo "<script>alert('Account created successfully'); window.location.href = '../index.php';</script>";
            } else {
                echo "<script>alert('Account creation failed'); window.location.href = '../index.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Passwords do not match'); window.location.href = '../index.php';</script>";
    }
} 
?>