<?php
// Include the database connection file
require_once '../../logs/backend/auth_check.php';
// Include the authentication check
checkUserAuth('pole plant'); // Check if the user is logged in and has the required role

include '../../database/connection.php';
// Handle form submission
if ($_POST && isset($_POST['send_message'])) {
    $title = trim($_POST['title']);
    $subject = trim($_POST['subject']);
    $content = trim($_POST['content']);
    $date = date('Y-m-d H:i:s'); // Get the current date and time
    
    if (!empty($title) && !empty($subject) && !empty($content)) {
        // Here you can add your email sending logic
        // For now, we'll just show a success message
        $insert = $pdo->prepare("INSERT INTO request (title, subject, content, created_at) VALUES (?, ?, ?, ?)");
        $insert->execute([$title, $subject, $content, $date]);
        // Set success message
        $message_sent = true;
        $success_message = "Message sent successfully!";
        
        // Example: You can save to database or send email here
        /*
        $stmt = $pdo->prepare("INSERT INTO messages (title, subject, content, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$title, $subject, $content]);
        */
        
        // Clear form data after successful submission
        $title = $subject = $content = '';
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/send.css">
    <style></style>
</head>
<body>
    <?php
    include '../menu/menu.php';
    include '../../database/connection.php';

    
    
    ?>

    <div class="main-content">
        <?php include '../header/header.php'; ?>
        
        <div class="dashboard-container">
          
            <div class="message-form-container">
                <h2><i class="fa fa-envelope"></i> Send Request to sawmill </h2>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fa fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="title"><i class="fa fa-tag"></i> Title</label>
                        <input type="text" id="title" name="title" placeholder="Enter message title" 
                               value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject"><i class="fa fa-subject"></i> Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="Enter message subject" 
                               value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="content"><i class="fa fa-edit"></i> Message Content</label>
                        <textarea id="content" name="content" placeholder="Write your request message here..." required><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" class="btn-send">
                        <i class="fa fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>