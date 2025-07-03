<?php
    require_once '../../logs/backend/auth_check.php'; // Include the authentication check
    checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role

    include'../../database/connection.php';
    $select = $pdo->query("SELECT * FROM germination WHERE del = 0 ");
    $fetch = $select->fetchAll();
    
    // Fetch existing map pins
    $pins_query = $pdo->query("SELECT * FROM germination_pins ORDER BY created_at DESC");
    $pins = $pins_query->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Germination Map</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/germ.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        .map-container {
            height: 600px;
            width: 100%;
            margin: 20px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        
        .map-controls {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .control-group {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .control-group label {
            font-weight: bold;
            min-width: 120px;
        }
        
        .control-group select, .control-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .pin-list {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .pin-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .pin-item:last-child {
            border-bottom: none;
        }
        
        .pin-info {
            flex: 1;
        }
        
        .pin-name {
            font-weight: bold;
            color: #333;
        }
        
        .pin-coords {
            font-size: 12px;
            color: #666;
        }
        
        .pin-actions {
            display: flex;
            gap: 5px;
        }
        
        .pin-actions button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .pin-actions .view-btn {
            background: #007bff;
            color: white;
        }
        
        .pin-actions .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .instruction-text {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">
        <div class="sales-container-pp">
            <div class="table-header">
                <div class="table-title">Germination Location Map</div>
                <div class="table-actions">
                    <a href="germ.php">
                        <button class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                        </button>
                    </a>
                </div>
            </div>
            
            <div class="instruction-text">
                <h4><i class="fa fa-info-circle"></i> Instructions:</h4>
                <p>1. Select a tree name from the dropdown below</p>
                <p>2. Click on the map to place a pin at the desired location</p>
                <p>3. The pin will be automatically saved with the selected tree name</p>
            </div>
            
            <div class="map-controls">
                <div class="control-group">
                    <label for="treeSelect">Select Tree:</label>
                    <select id="treeSelect" style="width: 250px;">
                        <option value="" hidden>-- Select a tree name --</option>
                        <?php foreach($fetch as $row): ?>
                            <option value="<?php echo htmlspecialchars($row['plant_name']); ?>"><?php echo htmlspecialchars($row['plant_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="control-group">
                    <label>Status:</label>
                    <span id="statusText" style="color: #666; font-style: italic;">Select a tree name to start pinning</span>
                </div>
            </div>
            
            <div id="map" class="map-container"></div>
            
            <div class="pin-list">
                <h3>Saved Pins</h3>
                <div id="pinsList">
                    <?php if(count($pins) > 0): ?>
                        <?php foreach($pins as $pin): ?>
                            <div class="pin-item" data-pin-id="<?php echo $pin['id']; ?>">
                                <div class="pin-info">
                                    <div class="pin-name"><?php echo htmlspecialchars($pin['tree_name']); ?></div>
                                    <div class="pin-coords">Lat: <?php echo $pin['latitude']; ?>, Lng: <?php echo $pin['longitude']; ?></div>
                                    <div class="pin-coords">Added: <?php echo date('Y-m-d H:i', strtotime($pin['created_at'])); ?></div>
                                </div>
                                <div class="pin-actions">
                                    <button class="view-btn" onclick="viewPin(<?php echo $pin['latitude']; ?>, <?php echo $pin['longitude']; ?>)">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <button class="delete-btn" onclick="deletePin(<?php echo $pin['id']; ?>, '<?php echo htmlspecialchars($pin['tree_name']); ?>')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; font-style: italic;">No pins saved yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        const map = L.map('map').setView([-1.9441, 30.0619], 13); // Default to Kigali, Rwanda
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Store for markers
        let markers = [];
        
        // Load existing pins
        const existingPins = <?php echo json_encode($pins); ?>;
        
        // Add existing pins to map
        existingPins.forEach(pin => {
            const marker = L.marker([pin.latitude, pin.longitude]).addTo(map);
            marker.bindPopup(`
                <b>${pin.tree_name}</b><br>
                Lat: ${pin.latitude}<br>
                Lng: ${pin.longitude}<br>
                Added: ${new Date(pin.created_at).toLocaleString()}
            `);
            markers.push(marker);
        });
        
        // Get references to DOM elements
        const treeSelect = document.getElementById('treeSelect');
        const statusText = document.getElementById('statusText');
        
        // Update status based on tree selection
        treeSelect.addEventListener('change', function() {
            if (this.value) {
                statusText.textContent = `Click on the map to pin location for: ${this.value}`;
                statusText.style.color = '#28a745';
            } else {
                statusText.textContent = 'Select a tree name to start pinning';
                statusText.style.color = '#666';
            }
        });
        
        // Handle map clicks
        map.on('click', function(e) {
            const selectedTree = treeSelect.value;
            
            if (!selectedTree) {
                alert('Please select a tree name first');
                return;
            }
            
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            // Confirm before saving
            if (confirm(`Pin location for "${selectedTree}" at coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}?`)) {
                savePin(selectedTree, lat, lng);
            }
        });
        
        // Function to save pin
        function savePin(treeName, latitude, longitude) {
            const formData = new FormData();
            formData.append('tree_name', treeName);
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
            formData.append('action', 'save_pin');
            
            fetch('map_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add marker to map
                    const marker = L.marker([latitude, longitude]).addTo(map);
                    marker.bindPopup(`
                        <b>${treeName}</b><br>
                        Lat: ${latitude}<br>
                        Lng: ${longitude}<br>
                        Added: ${new Date().toLocaleString()}
                    `);
                    markers.push(marker);
                    
                    // Reset selection
                    treeSelect.value = '';
                    statusText.textContent = 'Select a tree name to start pinning';
                    statusText.style.color = '#666';
                    
                    alert('Pin saved successfully!');
                    
                    // Refresh the page to update the pins list
                    location.reload();
                } else {
                    alert('Error saving pin: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the pin.');
            });
        }
        
        // Function to view pin
        function viewPin(latitude, longitude) {
            map.setView([latitude, longitude], 16);
            
            // Find and open the popup for this pin
            markers.forEach(marker => {
                if (marker.getLatLng().lat === latitude && marker.getLatLng().lng === longitude) {
                    marker.openPopup();
                }
            });
        }
        
        // Function to delete pin
        function deletePin(pinId, treeName) {
            if (confirm(`Are you sure you want to delete the pin for "${treeName}"?`)) {
                const formData = new FormData();
                formData.append('pin_id', pinId);
                formData.append('action', 'delete_pin');
                
                fetch('map_actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pin deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error deleting pin: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the pin.');
                });
            }
        }
    </script>
</body>
</html>