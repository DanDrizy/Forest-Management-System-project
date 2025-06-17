<?php require_once '../../logs/backend/auth_check.php'; checkUserAuth('pole plant'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responding   </title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/send.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .request
        {
            background: lightgreen;
        }
        .admin-fild
        {
            margin: 20px 0 0;
            padding: 10px;
            color: #000000;

        }
        .i
        {
            font-size: 12px;
        }
    </style>
</head>
<body>
<?php 

include '../menu/menu.php';
include '../../database/connection.php';

$sel = $pdo->query("SELECT COUNT(*) FROM received");
$sel->execute();
$received_count = $sel->fetchColumn();
$sel_2 = $pdo->query("SELECT COUNT(*) FROM request");
$sel_2->execute();
$received_count_2 = $sel_2->fetchColumn();

$sel_3 = $pdo->query("SELECT * FROM request");
$sel_3->execute();
$received_count_3 = $sel_3->fetchAll(PDO::FETCH_ASSOC);

$fetch = $pdo->query("SELECT 
rec.*,
rec.content as rec_content,
request.content as req_content,
rec.title as rec_title,
request.title as req_title
FROM request 
LEFT JOIN received rec ON request.r_id = rec.re_id ORDER BY request.created_at DESC");
$fetch->execute();
$received_data = $fetch->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="main-content">
    <div class="container">
        <h1><i class="fas fa-tree"></i> Response</h1>
        
        <!-- Messages Section -->
        <div class="messages-section">
            <div class="messages-header">
                <h2><i class="fas fa-envelope"></i> Request And Requests</h2>
                <div>
                    <span class="message-count" id="messageCount"><?php echo $received_count .'&nbsp;Respond &nbsp;&nbsp;&nbsp; '. $received_count_2 .'&nbsp;&nbsp;Request' ?? 0; ?></span>
                    <button class="refresh-btn" onclick="refreshMessages()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            
            
            <?php foreach($received_data as $data): 
            if($data['req_title'] == NULL && $data['rec_title'] == NULL )
            {
                continue;
            }    
            ?>
                <div class="messages-list" id="messagesList">
                <!-- Sample messages - replace with dynamic PHP content -->
                <div class="message-item">
                    <div class="message-header">
                        <span class="message-sender"> <i class="fas fa-" title="Title"></i>  <?php echo $data['req_title']; ?></span>
                        
                        <div>
                            <span class="message-time"><?php
                            $datetime = $data['created_at'];;
                                $readable = date("F j, Y \a\\t g:i A", strtotime($datetime));
                                echo $readable; ?></span>
                            <!-- <span class="message-priority priority-high">High</span> -->
                        </div>
                    </div>
                    <div class="message-content"><?php echo 'Sent: '. $data['req_content']; ?></div>
                    <div class="respond">
                        <?php if($data['title'] == NULL): ?>
                        <p class="admin-fild" style=" color:gray; font-style: italic; " ><i class="fas fa-clock i" title="Pending"></i> No Respond</p>
                        <?php else: ?>
                        <p class="request admin-fild"><i class="fas fa-message i" title="Respond"></i> <?php echo $data['rec_content']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                
            </div>
            <?php endforeach; ?>

            <!--  -->
        </div>

        <!-- Timber Content Area (for future timber listings) -->
        <div class="timber-content">
            <!-- Your existing timber table/content would go here -->
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/check.js"></script>
<script>
    function refreshMessages() {
        // Add loading state
        const refreshBtn = document.querySelector('.refresh-btn');
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        refreshBtn.disabled = true;
        
        // Simulate API call - replace with actual AJAX call to fetch messages
        setTimeout(() => {
            // Reset button
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
            
            // You would normally update the messages here
            console.log('Messages refreshed');
        }, 1000);
    }

    // Auto-refresh messages every 5 minutes
    setInterval(refreshMessages, 300000);

    // Function to add new message dynamically
    function addMessage(sender, content, priority = 'low', time = 'Just now') {
        const messagesList = document.getElementById('messagesList');
        const messageCount = document.getElementById('messageCount');
        
        const messageHTML = `
            <div class="message-item">
                <div class="message-header">
                    <span class="message-sender">${sender}</span>
                    <div>
                        <span class="message-time">${time}</span>
                        <span class="message-priority priority-${priority}">${priority.toUpperCase()}</span>
                    </div>
                </div>
                <div class="message-content">
                    ${content}
                </div>
            </div>
        `;
        
        // messagesList.insertAdjacentHTML('afterbegin', messageHTML);


        
        // Update message count
        const currentCount = parseInt(messageCount.textContent);
        messageCount.textContent = currentCount + 1;
    }

    // Example usage:
    // addMessage('System', 'New timber batch available for review', 'high');
</script>

</body>
</html>