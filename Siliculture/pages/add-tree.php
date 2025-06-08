<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Forestry Management System | Siliculture</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/add-tree.css">
    <style>
        /* Autocomplete Styles */
        .autocomplete-container {
            position: relative;
        }
        
        .autocomplete-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        
        .autocomplete-item:hover,
        .autocomplete-item.selected {
            background-color: #f5f5f5;
        }
        
        .autocomplete-item.no-results {
            color: #666;
            cursor: default;
        }
        
        .plant-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .plant-details {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <?php include'../menu/menu.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <h2 class="form-title">Add Tree Information</h2>

                <form id="addUserForm" method="post" action="../backend/add-tree-backend.php" enctype="multipart/form-data">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="p_name" class="required-field">Plant Name</label>
                            <div class="autocomplete-container">
                                <!-- Display field for plant name -->
                                <input type="text" id="p_name" class="form-control autocomplete-input" required placeholder="Start typing to search plant names..." autocomplete="off">
                                <!-- Hidden field to store the actual g_id -->
                                <input type="hidden" name="g_id" id="plant_id_hidden">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dbh_input" class="required-field">DBH</label>
                            <input type="number" name="dbh" id="dbh_input" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="health_select" class="required-field">Health</label>
                            <select name="health" id="health_select" class="form-control" required>
                                <option value="" selected hidden>Select Conditions</option>
                                <option value="A">A: is in Best condition</option>
                                <option value="B">B: is in good condition</option>
                                <option value="C">C: is in not good condition</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="height_input" class="required-field">Height</label>
                            <input type="text" name="height" id="height_input" class="form-control" required>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span class="section-title">Additional Information</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Additional Information</label>
                            <textarea name="info" id="info_textarea" class="information"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-back"><i class="fa fa-arrow-circle-left"></i> Cancel</button>
                        <button type="submit" class="btn-submit" name="submit">Add Tree</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const plantNameInput = document.getElementById('p_name');
            const plantIdInput = document.getElementById('plant_id_hidden'); // Updated to use the hidden field
            const autocompleteContainer = document.querySelector('.autocomplete-container');
            let autocompleteResults = null;
            let selectedIndex = -1;

            // Create autocomplete results container if it doesn't exist
            if (!document.querySelector('.autocomplete-results')) {
                autocompleteResults = document.createElement('div');
                autocompleteResults.className = 'autocomplete-results';
                autocompleteContainer.appendChild(autocompleteResults);
            } else {
                autocompleteResults = document.querySelector('.autocomplete-results');
            }

            // Handle input events
            plantNameInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                
                // Clear the hidden ID field when user types
                plantIdInput.value = '';
                
                if (searchTerm.length >= 2) {
                    fetchPlantSuggestions(searchTerm);
                } else {
                    hideAutocomplete();
                }
            });

            // Handle keyboard navigation
            plantNameInput.addEventListener('keydown', function(e) {
                const items = autocompleteResults.querySelectorAll('.autocomplete-item:not(.no-results)');
                
                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                        updateSelection(items);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        selectedIndex = Math.max(selectedIndex - 1, -1);
                        updateSelection(items);
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (selectedIndex >= 0 && items[selectedIndex]) {
                            selectItem(items[selectedIndex]);
                        }
                        break;
                    case 'Escape':
                        hideAutocomplete();
                        break;
                }
            });

            // Hide autocomplete when clicking outside
            document.addEventListener('click', function(e) {
                if (!autocompleteContainer.contains(e.target)) {
                    hideAutocomplete();
                }
            });

            function fetchPlantSuggestions(searchTerm) {
                fetch(`../backend/autocomplete-plants.php?term=${encodeURIComponent(searchTerm)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error:', data.error);
                            return;
                        }
                        displayAutocomplete(data);
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                    });
            }

            function displayAutocomplete(suggestions) {
                autocompleteResults.innerHTML = '';
                selectedIndex = -1;

                if (suggestions.length === 0) {
                    const noResults = document.createElement('div');
                    noResults.className = 'autocomplete-item no-results';
                    noResults.textContent = 'No plants found';
                    autocompleteResults.appendChild(noResults);
                } else {
                    suggestions.forEach((suggestion, index) => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        item.innerHTML = `
                            <div class="plant-name">${suggestion.label}</div>
                            <div class="plant-details">
                                <small>Seeds: ${suggestion.seeds || 'N/A'} | Soil: ${suggestion.soil || 'N/A'}</small>
                            </div>
                        `;
                        
                        item.addEventListener('click', function() {
                            selectItem(item, suggestion);
                        });

                        // Store suggestion data in the element
                        item.dataset.suggestion = JSON.stringify(suggestion);
                        autocompleteResults.appendChild(item);
                    });
                }

                autocompleteResults.style.display = 'block';
            }

            function updateSelection(items) {
                items.forEach((item, index) => {
                    if (index === selectedIndex) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }
                });
            }

            function selectItem(item, suggestion = null) {
                if (!suggestion) {
                    suggestion = JSON.parse(item.dataset.suggestion);
                }

                // Fill the input fields
                plantNameInput.value = suggestion.label; // Display the plant name
                plantIdInput.value = suggestion.id; // Store the g_id in hidden field
                
                console.log('Selected plant:', suggestion.label, 'ID:', suggestion.id); // Debug log
                
                hideAutocomplete();
            }

            function hideAutocomplete() {
                autocompleteResults.style.display = 'none';
                selectedIndex = -1;
            }

            // Form validation before submit
            document.getElementById('addUserForm').addEventListener('submit', function(e) {
                if (!plantIdInput.value) {
                    e.preventDefault();
                    alert('Please select a plant from the autocomplete suggestions.');
                    plantNameInput.focus();
                    return false;
                }
            });
        });
    </script>
</body>

</html>