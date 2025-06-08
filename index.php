<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        
        .loading-container {
            text-align: center;
            position: relative;
        }
        
        .welcome-message {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s ease forwards;
        }
        
        .loading-bar {
            width: 300px;
            height: 6px;
            background-color: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }
        
        .progress {
            height: 100%;
            width: 0%;
            background-color: #4CAF50;
            border-radius: 3px;
            transition: width 0.5s ease;
        }
        
        .loading-text {
            margin-top: 15px;
            color: #666;
            font-size: 1rem;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="welcome-message">Welcome</div>
        <div class="loading-bar">
            <div class="progress" id="progress-bar"></div>
        </div>
        <div class="loading-text" id="loading-text">Loading... 0%</div>
    </div>

    <script>
        // Configuration
        const targetPage = "main.php"; // Change this to your target page
        const loadingDuration = 3000; // Total loading time in milliseconds
        const welcomeMessages = ["Welcome", "Welcome to our site", "Getting things ready..."]; 
        
        // Variables
        const progressBar = document.getElementById('progress-bar');
        const loadingText = document.getElementById('loading-text');
        const welcomeMessage = document.querySelector('.welcome-message');
        let startTime = null;
        let messageIndex = 0;
        
        // Change welcome message every second
        const messageInterval = setInterval(() => {
            messageIndex = (messageIndex + 1) % welcomeMessages.length;
            welcomeMessage.style.opacity = 0;
            
            setTimeout(() => {
                welcomeMessage.textContent = welcomeMessages[messageIndex];
                welcomeMessage.style.opacity = 1;
            }, 300);
        }, 1500);
        
        // Handle the progress animation
        function updateProgress(timestamp) {
            if (!startTime) startTime = timestamp;
            
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / loadingDuration * 100, 100);
            
            progressBar.style.width = `${progress}%`;
            loadingText.textContent = `Loading... ${Math.floor(progress)}%`;
            
            if (progress < 100) {
                requestAnimationFrame(updateProgress);
            } else {
                clearInterval(messageInterval);
                setTimeout(() => {
                    window.location.href = 'logs/index.php';
                }, 500);
            }
        }
        
        // Start the animation
        window.addEventListener('load', () => {
            requestAnimationFrame(updateProgress);
        });
    </script>
</body>
</html>