document.addEventListener('DOMContentLoaded', function() {
    const plantNameInput = document.getElementById('p_name');
    const plantIdInput = document.querySelector('input[name="plant_id"]');
    const autocompleteContainer = document.querySelector('.autocomplete-container');
    let autocompleteResults = null;
    let selectedIndex = -1;

    // Create autocomplete results container if it doesn't exist
    if (!document.querySelector('.autocomplete-results')) {
        autocompleteResults = document.createElement('div');
        autocompleteResults.className = 'autocomplete-results';
        autocompleteContainer.appendChild(autocompleteResults);
    }

    // Handle input events
    plantNameInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        if (searchTerm.length >= 2) {
            fetchPlantSuggestions(searchTerm);
        } else {
            hideAutocomplete();
        }
    });

    // Handle keyboard navigation
    plantNameInput.addEventListener('keydown', function(e) {
        const items = autocompleteResults.querySelectorAll('.autocomplete-item');
        
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
        plantNameInput.value = suggestion.label;
        plantIdInput.value = suggestion.id;
        
        // You can also populate other fields if needed
        // For example, if you have additional info fields
        
        hideAutocomplete();
    }

    function hideAutocomplete() {
        autocompleteResults.style.display = 'none';
        selectedIndex = -1;
    }
});