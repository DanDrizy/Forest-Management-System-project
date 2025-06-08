<?php  
// Include the database connection file
require_once '../../logs/backend/auth_check.php';
// Include the authentication check
checkUserAuth('sawmill'); // Check if the user is logged in and has the required role
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .email-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        
        .email-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .email-tabs {
            display: flex;
            gap: 10px;
        }
        
        .email-tab {
            padding: 10px 20px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .email-tab.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .email-list {
            padding: 20px;
        }
        
        .email-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        
        .email-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-color: #007bff;
        }
        
        .email-item.unread {
            border-left: 4px solid #007bff;
            background: #f8f9ff;
        }
        
        .email-sender {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .email-subject {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
        }
        
        .email-preview {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .email-date {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }
        
        .email-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-email {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }
        
        .btn-reply:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .btn-delete:hover {
            background: #dc3545;
            color: white;
            border-color: #dc3545;
        }
        
        .email-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: none;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .compose-form {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        
        .btn-send {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-send:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <?php 
    include '../menu/menu.php';
    include '../../database/connection.php'; // Include the database connection file
    
    // Fixed query - using correct column names from your database
    $select_sawmill_transfer = $pdo->query("SELECT user,r_id, title, subject, content, created_at, read_status FROM request WHERE user = 'pole' ORDER BY created_at DESC");
    $select_sawmill_transfer->execute();
    $emails = $select_sawmill_transfer->fetchAll(PDO::FETCH_ASSOC);

    // Count unread messages
    $unread_count_query = $pdo->query("SELECT COUNT(*) as unread_count FROM request WHERE read_status = 0");
    $unread_count = $unread_count_query->fetch(PDO::FETCH_ASSOC)['unread_count'];
    ?>
    
    <div class="main-content">
        <?php include '../header/header.php'; ?>
        
        <!-- Email System -->
        <div class="email-container" style=" overflow: auto; " >
            <div class="email-header">
                <h2><i class="fas fa-envelope"></i> Messages & Communications</h2>
                <div class="email-tabs">
                    <button class="email-tab active" onclick="showEmailTab('inbox')">
                        <i class="fas fa-inbox"></i> Inbox
                    </button>
                    <button class="email-tab" onclick="showEmailTab('compose')">
                        <i class="fas fa-edit"></i> Compose
                    </button>
                </div>
            </div>
            
            <!-- Inbox Tab -->
            <div id="inbox-tab" class="email-tab-content">
                <div class="email-list">
                    <?php if (empty($emails)): ?>
                        <div style="text-align: center; padding: 40px; color: #999;">
                            <i class="fas fa-envelope-open-text" style="font-size: 48px; margin-bottom: 15px;"></i>
                            <p>No messages found</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($emails as $email): ?>
                            <div class="email-item <?php echo ($email['read_status'] == 0) ? 'unread' : ''; ?>" 
                                 onclick="viewEmail(<?php echo $email['r_id']; ?>)">
                                <div class="email-sender">
                                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars(strtoupper($email['user']) ?? 'PolePlant'); ?>
                                </div>
                                <div class="email-subject">
                                    <?php echo htmlspecialchars($email['title'] ?? 'No Subject'); ?>
                                </div>
                                <div class="email-preview">
                                    <?php 
                                    $content = $email['content'] ?? $email['subject'] ?? 'No content';
                                    echo htmlspecialchars(substr($content, 0, 100)) . (strlen($content) > 100 ? '...' : ''); 
                                    ?>
                                </div>
                                <div class="email-date">
                                    <i class="fas fa-clock"></i> 
                                    <?php 
                                    $date = new DateTime($email['created_at']);
                                    echo $date->format('M j, Y g:i A');
                                    ?>
                                </div>
                                <div class="email-actions" onclick="event.stopPropagation();">
                                    <button class="btn-email btn-reply" onclick="replyToEmail(<?php echo $email['r_id']; ?>)">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                    <button class="btn-email btn-delete" onclick="deleteEmail(<?php echo $email['r_id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Compose Tab -->
            <div id="compose-tab" class="email-tab-content" style="display: none;">
                <form class="compose-form" onsubmit="sendEmail(event)">
                    <div class="form-group">
                        <label for="email-to">To:</label>
                        <input type="text" id="email-to" name="to" placeholder="Enter recipient" required>
                    </div>
                    <div class="form-group">
                        <label for="email-subject">Subject:</label>
                        <input type="text" id="email-subject" name="subject" placeholder="Enter subject" required>
                    </div>
                    <div class="form-group">
                        <label for="email-message">Message:</label>
                        <textarea id="email-message" name="message" placeholder="Type your message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Email View Modal -->
    <div id="email-modal" class="email-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-subject">Email Subject</h3>
                <span class="close" onclick="closeEmailModal()">&times;</span>
            </div>
            <div id="modal-body">
                <!-- Email content will be loaded here -->
            </div>
            <div style="margin-top: 20px;">
                <button class="btn-email btn-reply" onclick="showReplyForm()">
                    <i class="fas fa-reply"></i> Reply
                </button>
            </div>
            
            <!-- Reply Form (hidden by default) -->
            <div id="reply-form" style="display: none; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <form onsubmit="sendReply(event)">
                    <input type="hidden" id="reply-to-id" name="reply_to_id">
                    <div class="form-group">
                        <label for="reply-message">Your Reply:</label>
                        <textarea id="reply-message" name="reply_message" placeholder="Type your reply here..." required></textarea>
                    </div>
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showEmailTab(tabName) {
            // Hide all tabs
            document.getElementById('inbox-tab').style.display = 'none';
            document.getElementById('compose-tab').style.display = 'none';
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.email-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').style.display = 'block';
            
            // Add active class to clicked tab button
            const clickedTab = Array.from(document.querySelectorAll('.email-tab')).find(tab => 
                tab.textContent.toLowerCase().includes(tabName)
            );
            if (clickedTab) {
                clickedTab.classList.add('active');
            }
        }
        
        function viewEmail(emailId) {
            // Make AJAX request to get email details
            fetch('get_email_details.php?id=' + emailId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modal-subject').textContent = data.title || 'No Subject';
                    document.getElementById('modal-body').innerHTML = `
                        <div style="margin-bottom: 15px;">
                            <strong>From:</strong> ${data.sender || 'PolePlant'}<br>
                            <strong>Date:</strong> ${data.formatted_date || data.created_at}<br>
                            <strong>Subject:</strong> ${data.subject || 'N/A'}
                        </div>
                        <div style="border-top: 1px solid #eee; padding-top: 15px;">
                            ${data.content || data.message || 'No content available'}
                        </div>
                    `;
                    document.getElementById('reply-to-id').value = emailId;
                    document.getElementById('email-modal').style.display = 'block';
                    
                    // Mark as read
                    markAsRead(emailId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading email details. Please check if get_email_details.php exists.');
                });
        }
        
        function closeEmailModal() {
            document.getElementById('email-modal').style.display = 'none';
            document.getElementById('reply-form').style.display = 'none';
        }
        
        function showReplyForm() {
            document.getElementById('reply-form').style.display = 'block';
        }
        
        function replyToEmail(emailId) {
            viewEmail(emailId);
            setTimeout(() => {
                showReplyForm();
            }, 500);
        }
        
        function sendEmail(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('send_email.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    event.target.reset();
                    showEmailTab('inbox');
                    location.reload(); // Refresh to show new message
                } else {
                    alert('Error sending message: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending message. Please check if send_email.php exists.');
            });
        }
        
        function sendReply(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            fetch('send_reply.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reply sent successfully!');
                    closeEmailModal();
                    location.reload(); // Refresh to show new reply
                } else {
                    alert('Error sending reply: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending reply. Please check if send_reply.php exists.');
            });
        }
        
        function deleteEmail(emailId) {
            if (confirm('Are you sure you want to delete this message?')) {
                fetch('delete_email.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: emailId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Message deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting message: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting message. Please check if delete_email.php exists.');
                });
            }
        }
        
        function markAsRead(emailId) {
            fetch('mark_as_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({id: emailId})
            })
            .catch(error => {
                console.error('Error marking as read:', error);
            });
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('email-modal');
            if (event.target == modal) {
                closeEmailModal();
            }
        }
    </script>
</body>
</html>