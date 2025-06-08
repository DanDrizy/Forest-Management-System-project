<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role


include'../../database/connection.php';

$id = $_GET['id'];

$select_data = $pdo->query("SELECT * FROM plant WHERE p_id = $id ");
$select_data->execute();
$data = $select_data->fetchAll(PDO::FETCH_ASSOC);


?>


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
</head>

<style>
    input,textarea{

        border: none;
    }



    

</style>

<body>
    <?php
    // include '../menu/menu.php';
    ?>

    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Add Tree</h1>

            </div>

            <div class="form-container">
                <h2 class="form-title">View Information</h2>

                <form id="addUserForm" method="post" action="../backend/add-tree-backend.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName" >Tree Name</label>
                            <input type="text" name="p_name" id="p_name" value="<?php echo $data[0]['p_name']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="lastName" >Country</label>
                            <input type="text" name="country" value="<?php echo $data[0]['country']; ?>" id="country"  required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" >Provance</label>
                            <input type="text" name="provance" value="<?php echo $data[0]['provance']; ?>" id="provance" required>
                        </div>

                        <div class="form-group">
                            <label >Location</label>
                            <input type="text" name="location" value="<?php echo $data[0]['location']; ?>" id="location" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="tree-type">
                            <label for="tree-type-search"  class="required-field">Tree Type</label>
                            <div class="autocomplete-container">
                                <input type="text" id="tree-type-search" class="form-control" placeholder="Search tree types..." required>
                                <input type="hidden" name="tree-type" id="tree-type" required>
                                <div id="autocomplete-results" class="autocomplete-results"></div>
                            </div>
                        </div>

                    </div>

                    
                    

                    <div class="form-row">
                        <div class="form-group">
                            <label>Additional Information</label>
                            <textarea name="additionalInfo" id="" class=" information "><?php echo $data[0]['info']; ?></textarea>

                        </div>

                        <div class="form-group">
                           
                            <div class="comment">
                                <label for="comment" class="comment-label">Comment</label>
                                <textarea  value="" id="comment" class="comment-input"><?php echo $data[0]['comment']; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">



                    </div>

                    <div class="form-actions">
                        <button class="btn-back"><i class="fa fa-arrow-circle-left"></i> Back</i></button>
                        <button type="submit" class="btn-submit" name="submit">update Tree</button>

                    </div>
                </form>
            </div>
        </div>

         <script>
            // Tree type autocomplete functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Sample of trees 
                const treeTypes = [
                    "Oak", "Pine", "Maple", "Cedar", "Spruce", "Fir", "Birch", "Ash", "Beech", 
                    "Cherry", "Cypress", "Elm", "Hickory", "Juniper", "Larch", "Mahogany", 
                    "Poplar", "Redwood", "Sequoia", "Sycamore", "Walnut", "Willow", "Yew",
                    "Acacia", "Alder", "Apple", "Aspen", "Baobab", "Bamboo", "Eucalyptus"
                ];
                
                const searchInput = document.getElementById('tree-type-search');
                const hiddenInput = document.getElementById('tree-type');
                const resultsContainer = document.getElementById('autocomplete-results');
                
                // Function to display matching results
                function showResults(filteredResults) {
                    resultsContainer.innerHTML = '';
                    
                    if (filteredResults.length > 0) {
                        filteredResults.forEach(result => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.textContent = result;
                            
                            item.addEventListener('click', function() {
                                searchInput.value = result;
                                hiddenInput.value = result;
                                resultsContainer.style.display = 'none';
                            });
                            
                            resultsContainer.appendChild(item);
                        });
                        
                        resultsContainer.style.display = 'block';
                    } else {
                        resultsContainer.style.display = 'none';
                    }
                }
                
                // Search input event listener
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    
                    if (query.length > 0) {
                        const filteredResults = treeTypes.filter(type => 
                            type.toLowerCase().includes(query)
                        );
                        showResults(filteredResults);
                    } else {
                        resultsContainer.style.display = 'none';
                    }
                });
                
                // Handle keyboard navigation
                searchInput.addEventListener('keydown', function(e) {
                    const items = resultsContainer.querySelectorAll('.autocomplete-item');
                    const currentSelected = resultsContainer.querySelector('.selected');
                    
                    if (items.length > 0) {
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            if (!currentSelected) {
                                items[0].classList.add('selected');
                            } else {
                                const currentIndex = Array.from(items).indexOf(currentSelected);
                                if (currentIndex < items.length - 1) {
                                    currentSelected.classList.remove('selected');
                                    items[currentIndex + 1].classList.add('selected');
                                }
                            }
                        } else if (e.key === 'ArrowUp') {
                            e.preventDefault();
                            if (currentSelected) {
                                const currentIndex = Array.from(items).indexOf(currentSelected);
                                if (currentIndex > 0) {
                                    currentSelected.classList.remove('selected');
                                    items[currentIndex - 1].classList.add('selected');
                                }
                            }
                        } else if (e.key === 'Enter' && currentSelected) {
                            e.preventDefault();
                            searchInput.value = currentSelected.textContent;
                            hiddenInput.value = currentSelected.textContent;
                            resultsContainer.style.display = 'none';
                        }
                    }
                });
                
                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (e.target !== searchInput && e.target !== resultsContainer) {
                        resultsContainer.style.display = 'none';
                    }
                });

                // Back button functionality
                const backButton = document.querySelector('.btn-back');
                if (backButton) {
                    backButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (document.getElementById('p_name').value ||
                            document.getElementById('country').value ||
                            document.getElementById('location').value ||
                            document.getElementById('tree-type-search').value ||
                            document.getElementById('provance').value) {
                            if (confirm('Are you sure you want to go back? Any unsaved changes will be lost.')) {
                                window.location.href = 'plant.php';
                            }
                        } else {
                            window.location.href = 'control.php';
                        }
                    });
                }
            });
        </script>
    </div>
</body>

</html>