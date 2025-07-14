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
    <link rel="stylesheet" href="../css/germination_2.css">
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
                        <?php foreach($fetch as $row):
                            
                            // $value = htmlspecialchars($row['plant_name'])." | ".($row['g_id']); 
                            
                            ?>
                             <option value="<?= $row['g_id'] . '::' . htmlspecialchars($row['plant_name']) ?>"><?= htmlspecialchars($row['plant_name']) ?></option>

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
            <div class="biodiversity-form">
  <h3>🦋 Record Biodiversity Observation</h3>
  <form id="biodiversityForm" enctype="multipart/form-data">
    <label for="biodiversity_pin_id">Select Tree Pin:</label>
    <select id="biodiversity_pin_id" name="pin_id" required>
      <option value="">-- Select Pin --</option>
      <?php foreach($pins as $pin): ?>
        <option value="<?= $pin['id'] ?>">
          <?= htmlspecialchars($pin['tree_name']) ?> (<?= $pin['latitude'] ?>, <?= $pin['longitude'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <label>Species Name:</label>
    <input type="text" name="species_name" required>

    <label>Species Type:</label>
    <select name="species_type" required>
      <option value="bird">Bird</option>
      <option value="mammal">Mammal</option>
      <option value="insect">Insect</option>
      <option value="plant">Plant</option>
      <option value="fungi">Fungi</option>
      <option value="other">Other</option>
    </select>

    <label>Observation Type:</label>
    <select name="observation_type" required>
      <option value="visual">Visual</option>
      <option value="audio">Audio</option>
      <option value="tracks">Tracks</option>
      <option value="nest">Nest</option>
      <option value="other">Other</option>
    </select>

    <label>Abundance:</label>
    <select name="abundance">
      <option value="rare">Rare</option>
      <option value="uncommon">Uncommon</option>
      <option value="common" selected>Common</option>
      <option value="abundant">Abundant</option>
    </select>

    <label>Native Status:</label>
    <select name="native_status">
      <option value="native">Native</option>
      <option value="invasive">Invasive</option>
      <option value="introduced">Introduced</option>
      <option value="unknown" selected>Unknown</option>
    </select>

    <label>Observation Date:</label>
    <input type="date" name="observation_date" required>

    <label>Observation Time:</label>
    <input type="time" name="observation_time">

    <label>Weather Conditions:</label>
    <input type="text" name="weather_conditions" placeholder="e.g., sunny, cloudy">

    <label>Upload Photo (optional):</label>
    <input type="file" name="photo">

    <label>Observed By:</label>
    <input type="text" name="observed_by" required>

    <label>Notes:</label>
    <textarea name="notes" rows="3"></textarea>

    <button type="submit">➕ Save Observation</button>
  </form>
</div>

<!--  -->
<div class="soil-health-form">
  <h3>🧪 Record Soil Health Data</h3>
  <form id="soilHealthForm" enctype="multipart/form-data">
    <label>Select Tree Pin:</label>
    <select name="pin_id" required>
      <option value="">-- Select Pin --</option>
      <?php foreach($pins as $pin): ?>
        <option value="<?= $pin['id'] ?>">
          <?= htmlspecialchars($pin['tree_name']) ?> (<?= $pin['latitude'] ?>, <?= $pin['longitude'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <label>Soil pH:</label>
    <input type="number" step="0.01" name="soil_ph" required>

    <label>Organic Matter (%):</label>
    <input type="number" step="0.01" name="organic_matter_percent" required>

    <label>Nitrogen (mg/kg):</label>
    <input type="number" step="0.01" name="nitrogen_level">

    <label>Phosphorus (mg/kg):</label>
    <input type="number" step="0.01" name="phosphorus_level">

    <label>Potassium (mg/kg):</label>
    <input type="number" step="0.01" name="potassium_level">

    <label>Soil Moisture (%):</label>
    <input type="number" step="0.01" name="soil_moisture_percent">

    <label>Soil Temperature (°C):</label>
    <input type="number" step="0.01" name="soil_temperature">

    <label>Soil Texture:</label>
    <select name="soil_texture">
      <option value="loam">Loam</option>
      <option value="sand">Sand</option>
      <option value="clay">Clay</option>
      <option value="silt">Silt</option>
      <option value="mixed">Mixed</option>
    </select>

    <label>Compaction Level:</label>
    <select name="compaction_level">
      <option value="low">Low</option>
      <option value="moderate">Moderate</option>
      <option value="high">High</option>
      <option value="severe">Severe</option>
    </select>

    <label>Erosion Signs:</label>
    <select name="erosion_signs">
      <option value="none">None</option>
      <option value="minimal">Minimal</option>
      <option value="moderate">Moderate</option>
      <option value="severe">Severe</option>
    </select>

    <label>Testing Date:</label>
    <input type="date" name="testing_date" required>

    <label>Testing Method:</label>
    <input type="text" name="testing_method" placeholder="e.g., Lab test, field kit">

    <label>Upload Lab Report (optional):</label>
    <input type="file" name="lab_result_file">

    <label>Tested By:</label>
    <input type="text" name="tested_by" required>

    <label>Notes:</label>
    <textarea name="notes" rows="3"></textarea>

    <button type="submit">➕ Submit Soil Data</button>
  </form>
</div>


        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        const map = L.map('map').setView([-1.9441, 30.0619], 13); // Default to Kigali, Rwanda
        // const g_id = document.getElementById('treeSelect').value;
        // const selectedValue = document.getElementById('treeSelect').value; // e.g., "7::Eucalyptus"
        // const [g_id, tree_name] = selectedValue.split("::");

        
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
        // map.on('click', function(e) {
        //     const selectedTree = treeSelect.value;
            
        //     if (!selectedTree) {
        //         alert('Please select a tree name first');
        //         return;
        //     }
            
        //     const lat = e.latlng.lat;
        //     const lng = e.latlng.lng;
            
        //     // Confirm before saving
        //     if (confirm(`Pin location for "${selectedTree}" at coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}?`)) {
        //         savePin(selectedTree, lat, lng);

        //     }
        // });
        
        map.on('click', function(e) {
    const selectedValue = treeSelect.value;
    
    if (!selectedValue) {
        alert('Please select a tree name first');
        return;
    }

    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    if (confirm(`Pin location for "${selectedValue}" at coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}?`)) {
        savePin(selectedValue, lat, lng); // Pass the combined value here
    }
});

        // // Function to save pin
        // function savePin(treeName, latitude, longitude) {
        //     const formData = new FormData();
        //     formData.append('tree_name', treeName);
        //     formData.append('g_id', g_id);
        //     formData.append('latitude', latitude);
        //     formData.append('longitude', longitude);
        //     formData.append('action', 'save_pin');
            
        //     fetch('map_actions.php', {
        //         method: 'POST',
        //         body: formData
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             // Add marker to map
        //             const marker = L.marker([latitude, longitude]).addTo(map);
        //             marker.bindPopup(`
        //                 <b>${treeName}</b><br>
        //                 Lat: ${latitude}<br>
        //                 Lng: ${longitude}<br>
        //                 Added: ${new Date().toLocaleString()}
        //             `);
        //             markers.push(marker);
                    
        //             // Reset selection
        //             treeSelect.value = '';
        //             statusText.textContent = 'Select a tree name to start pinning';
        //             statusText.style.color = '#666';
                    
        //             alert('Pin saved successfully!');
                    
        //             // Refresh the page to update the pins list
        //             location.reload();
        //         } else {
        //             alert('Error saving pin: ' + data.message);
        //         }
        //     })
        //     .catch(error => {
        //         console.error('Error:', error);
        //         alert('An error occurred while saving the pin.');
        //     });
        // }
        function savePin(selectedValue, latitude, longitude) {
    // Split value into g_id and tree_name
    const [g_id, tree_name] = selectedValue.split("::");

    const formData = new FormData();
    formData.append('g_id', g_id);
    formData.append('tree_name', tree_name);
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
            const marker = L.marker([latitude, longitude]).addTo(map);
            marker.bindPopup(`
                <b>${tree_name}</b><br>
                Lat: ${latitude}<br>
                Lng: ${longitude}<br>
                Added: ${new Date().toLocaleString()}
            `);
            markers.push(marker);

            treeSelect.value = '';
            statusText.textContent = 'Select a tree name to start pinning';
            statusText.style.color = '#666';

            alert('Pin saved successfully!');
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
        // Handle biodiversity form submission

    document.getElementById('biodiversityForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'insert_biodiversity');
    fetch('map_actions_2.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Biodiversity observation saved!');
            this.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to save observation.');
    });
});

document.getElementById('soilHealthForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('action', 'insert_soil');

  fetch('map_actions_3.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Soil data saved successfully!');
        this.reset();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert('Failed to save soil data.');
    });
});



    </script>
</body>
</html>