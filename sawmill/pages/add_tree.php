<?php

// Include the database connection file
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.css" />
    <link rel="stylesheet" href="../css/register.css">     
    <style>  
    .main-content
    {
        overflow: auto
    }
    .main-content::-webkit-scrollbar {
        width: 8px;
    }       
    .main-content::-webkit-scrollbar-thumb {
        background: #00dc82;
        border-radius: 10px;
    }
    .main-content::-webkit-scrollbar-track {
        background: #000;
        border-radius: 10px;
    }
        .menu-item.active-tree {             
            background-color: rgba(255, 255, 255, 0.1);             
            color: white;             
            border-left: 3px solid #00dc82;         
        }
        
        /* Custom styles for the location autocomplete */
        .awesomplete {
            width: 100%;
            position: relative;
        }
        
        .awesomplete > ul {
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        
        .awesomplete > ul > li {
            padding: 10px 15px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
        }
        
        .awesomplete > ul > li:hover {
            background-color:rgb(228, 228, 228);
            color: #333;
        }
        
        .awesomplete mark {
            background: #00dc8233;
            color: #333;
            font-weight: bold;
            padding: 0;
        }
        
        .awesomplete li:hover mark {
            background: #00dc8233;
        }
        
        .awesomplete li[aria-selected="true"] {
            background-color:rgb(213, 212, 212);
            color: #333;
        }
    </style>
</head> 
<body>     
    <?php include'../menu/menu.php'; ?>          
    <div class="main-content">                            
        <div class="dashboard-grid-saw-a">         
            <div class="container">         
                <div class="form-header">             
                    <h2>Registration Form</h2>             
                    <p>Please fill tree details below</p>         
                </div>                  
                <form id="registrationForm" method="post" action=" ../backend/add-tree-backend.php" enctype="multipart/form-data">             
                    <div class="form-group">                 
                        <label for="name">Tree Name</label>                 
                        <input type="text" id="name" name="name" placeholder="Enter Tree name" required>             
                    </div>                          
                    <div class="form-group">                 
                        <label for="provider">Provider</label>                 
                        <input type="text" id="provider" name="provider" placeholder="Enter provider Tree Location" required>             
                    </div>              
                    <div class="form-group">                 
                        <label for="status">Status</label>                 
                        <select id="status" name="status" required>                     
                            <option value="" hidden>Select Status</option>                     
                            <option value="Harvested">Harvested</option>                     
                            <option value="Inproccess">Inproccess</option>                     
                            <option value="Not yet">Not yet</option>                 
                        </select>             
                    </div>                          
                    <div class="form-group">                 
                        <label for="location">Location</label>  
                        <input type="text" id="location" name="location" class="location-autocomplete" 
                               placeholder="Start typing a location..." required>
                    </div>                          
                    <div class="form-group">                 
                        <label for="amount">Amount</label>                 
                        <input type="number" id="amount" name="amount" placeholder="Enter amount" min="0" step="0.01" required>             
                    </div>                          
                    <button type="submit" name="ok" class="btn">Register</button>         
                </form>                       
            </div>            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/awesomplete/1.1.5/awesomplete.min.js"></script>
            <script>
                $(document).ready(function() {
                    // List of world cities - common locations that would be used in the application
                    // This is a shortened list - in production you would want a more comprehensive one
                    const worldCities = [
                        "New York, USA", "Los Angeles, USA", "Chicago, USA", "Houston, USA", "Phoenix, USA", 
                        "Philadelphia, USA", "San Antonio, USA", "San Diego, USA", "Dallas, USA", 
                        "San Jose, USA", "Austin, USA", "Jacksonville, USA", "Fort Worth, USA", 
                        "Columbus, USA", "San Francisco, USA", "Charlotte, USA", "Indianapolis, USA", 
                        "Seattle, USA", "Denver, USA", "Washington DC, USA", "Boston, USA", 
                        "London, UK", "Manchester, UK", "Birmingham, UK", "Liverpool, UK",
                        "Paris, France", "Marseille, France", "Lyon, France", "Toulouse, France",
                        "Berlin, Germany", "Munich, Germany", "Hamburg, Germany", "Frankfurt, Germany",
                        "Tokyo, Japan", "Osaka, Japan", "Kyoto, Japan", "Yokohama, Japan",
                        "Beijing, China", "Shanghai, China", "Guangzhou, China", "Shenzhen, China",
                        "Mumbai, India", "Delhi, India", "Bangalore, India", "Chennai, India",
                        "Sydney, Australia", "Melbourne, Australia", "Brisbane, Australia", "Perth, Australia",
                        "Toronto, Canada", "Vancouver, Canada", "Montreal, Canada", "Calgary, Canada",
                        "Mexico City, Mexico", "Cancun, Mexico", "Guadalajara, Mexico", "Monterrey, Mexico",
                        "São Paulo, Brazil", "Rio de Janeiro, Brazil", "Brasília, Brazil", "Salvador, Brazil",
                        "Moscow, Russia", "Saint Petersburg, Russia", "Novosibirsk, Russia", "Yekaterinburg, Russia",
                        "Cape Town, South Africa", "Johannesburg, South Africa", "Durban, South Africa", "Pretoria, South Africa",
                        "Cairo, Egypt", "Alexandria, Egypt", "Giza, Egypt", "Luxor, Egypt",
                        "Dubai, UAE", "Abu Dhabi, UAE", "Sharjah, UAE", "Ajman, UAE",
                        "Singapore, Singapore", "Bangkok, Thailand", "Phuket, Thailand", "Chiang Mai, Thailand",
                        "Kuala Lumpur, Malaysia", "Jakarta, Indonesia", "Bali, Indonesia", "Manila, Philippines",
                        "Seoul, South Korea", "Busan, South Korea", "Hong Kong, China", "Taipei, Taiwan",
                        "Stockholm, Sweden", "Oslo, Norway", "Copenhagen, Denmark", "Helsinki, Finland",
                        "Amsterdam, Netherlands", "Brussels, Belgium", "Zurich, Switzerland", "Vienna, Austria",
                        "Madrid, Spain", "Barcelona, Spain", "Lisbon, Portugal", "Athens, Greece",
                        "Rome, Italy", "Milan, Italy", "Venice, Italy", "Florence, Italy",
                        "Warsaw, Poland", "Prague, Czech Republic", "Budapest, Hungary", "Istanbul, Turkey", "Kigali Rwanda"
                    ];
                    
                    // Initialize Awesomplete
                    const locationInput = document.getElementById("location");
                    new Awesomplete(locationInput, {
                        list: worldCities,
                        minChars: 1,
                        maxItems: 10,
                        autoFirst: true,
                        filter: function(text, input) {
                            return Awesomplete.FILTER_CONTAINS(text, input.match(/[^,]*$/)[0]);
                        },
                        item: function(text, input) {
                            return Awesomplete.ITEM(text, input.match(/[^,]*$/)[0]);
                        },
                        replace: function(text) {
                            this.input.value = text;
                        }
                    });
                    
                    // Add a method to allow users to add custom locations if they can't find what they need
                    locationInput.addEventListener('awesomplete-selectcomplete', function() {
                        // If needed, additional validation or processing can happen here
                        console.log('Location selected:', this.value);
                    });
                    
                    // Allow form submission with custom locations not in the list
                    $('#registrationForm').on('submit', function() {
                        // You could add validation here if needed
                        return true; // Allow form submission
                    });
                });
            </script>
            <script src="../js/check.js"></script>
        </div>              
    </div> 
</body> 
</html>