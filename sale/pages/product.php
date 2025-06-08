<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('sales'); // Check if the user is logged in and has the required role
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="../css/product.css">

    <style>
        .sale-btn {
            background-color: #00dc82;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .sale-btn:hover {
            background-color: #00b865;
        }

        .clossed {
            background-color: rgb(255, 0, 68);
        }

        .clossed:hover {
            background-color: rgb(255, 5, 95);
        }

        .btn-operation {
            width: 7rem;
        }
        .instock-btn{

            width: 7rem;
            background-color: rgb(209, 209, 209);
            color: darkred;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-style: italic;
            
        }
        .no-records
        {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            padding: 8rem 0;
        }
        .no-records i {
            font-size: 100px;
            color: rgb(250, 96, 112);
            margin-bottom: 20px;
        }
        .no-records p {
            
            color:rgb(250, 96, 112);
            padding: 10px;
            width: 15rem;
            border-radius: 10px;
        }
        
        /* Search result highlighting */
        .search-highlight {
            background-color: yellow;
            font-weight: bold;
        }
        
        /* Hidden rows for search */
        .hidden-row {
            display: none;
        }
        
        /* No search results message */
        .no-search-results {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-style: italic;
            display: none;
        }
    </style>


</head>

<body>
    <?php include '../menu/menu.php'; ?>

    <div class="main-content">

        <?php

        include '../header/header.php';

        include '../../database/connection.php';

        $select_sale_record = $pdo->prepare("SELECT * FROM timber,logs,plant,germination WHERE timber.l_id = logs.l_id AND logs.p_id = plant.p_id AND plant.g_id = germination.g_id AND timber.status = 'send' AND timber.t_amount > 0 ORDER BY timber.t_indate DESC");
        $select_sale_record->execute();
        $fetch_sale_record = $select_sale_record->fetchAll();


        $i = 1;

        ?>



        <div class="pro-container">
            <h1>Products</h1>

            <div class="search-container">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Search products by name, type, size, or date..." autocomplete="off">
                    <!-- <span class="search-icon"><i class="fa fa-search"></i></span> -->
                </div>
               
            </div>

            <?php if (count($fetch_sale_record) == 0) { ?>
                <div class="no-records">
                    <i class=" fa fa-ban "> </i>
                    <p>No products available.</p>
                </div>
                <?php exit; } ?>

            <table id="productTable">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll" class="checkbox">
                        </th>
                        <th class="no-cell">No</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Recorded Date</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php  foreach ($fetch_sale_record as $row) {  ?>

                            <tr data-id="1" class="product-row">
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="checkbox product-checkbox">
                                </td>
                                <td class="no-cell"> <?php echo $i;
                                                        $i++; ?> </td>
                                <td class="searchable-name"> <?php echo $row['plant_name']; ?> </td>
                                <td class="searchable-amount"> <?php  echo $row['t_amount']; ?> </td>
                                <td class="searchable-type"> <?php  echo $row['type']; ?> </td>
                                <td class="searchable-size"> <?php  echo $row['size']; ?> </td>
                                <td class="searchable-width"> <?php  echo $row['t_width']; ?> cm </td>
                                <td class="searchable-height"> <?php  echo $row['t_height']; ?> cm </td>
                                <td class="searchable-date"> <?php  echo $row['t_indate']; ?> </td>

                                
                            </tr>
                    <?php }

                    // } 

                    ?>

                </tbody>
            </table>
            
            <!-- No search results message -->
            <div class="no-search-results" id="noSearchResults">
                <i class="fa fa-search"></i>
                <p>No products match your search criteria.</p>
            </div>
        </div>

        <script>
            // Real-time search functionality
            function initializeSearch() {
                const searchInput = document.getElementById('searchInput');
                const productRows = document.querySelectorAll('.product-row');
                const noResultsDiv = document.getElementById('noSearchResults');
                const productTable = document.getElementById('productTable');
                
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    let visibleRowCount = 0;
                    
                    productRows.forEach(function(row) {
                        // Get all searchable content from the row
                        const searchableContent = [
                            row.querySelector('.searchable-name')?.textContent || '',
                            row.querySelector('.searchable-amount')?.textContent || '',
                            row.querySelector('.searchable-type')?.textContent || '',
                            row.querySelector('.searchable-size')?.textContent || '',
                            row.querySelector('.searchable-width')?.textContent || '',
                            row.querySelector('.searchable-height')?.textContent || '',
                            row.querySelector('.searchable-date')?.textContent || ''
                        ].join(' ').toLowerCase();
                        
                        // Clear previous highlights
                        clearHighlights(row);
                        
                        if (searchTerm === '' || searchableContent.includes(searchTerm)) {
                            row.classList.remove('hidden-row');
                            visibleRowCount++;
                            
                            // Highlight matching text if there's a search term
                            if (searchTerm !== '') {
                                highlightText(row, searchTerm);
                            }
                        } else {
                            row.classList.add('hidden-row');
                        }
                    });
                    
                    // Show/hide "no results" message
                    if (visibleRowCount === 0 && searchTerm !== '') {
                        noResultsDiv.style.display = 'block';
                        productTable.style.display = 'none';
                    } else {
                        noResultsDiv.style.display = 'none';
                        productTable.style.display = 'table';
                    }
                    
                    // Update checkboxes and counters
                    updateSelectAllCheckbox();
                    updateDeleteButton();
                });
            }
            
            // Function to highlight matching text
            function highlightText(row, searchTerm) {
                const searchableCells = row.querySelectorAll('[class*="searchable-"]');
                
                searchableCells.forEach(function(cell) {
                    const originalText = cell.textContent;
                    const regex = new RegExp(`(${escapeRegExp(searchTerm)})`, 'gi');
                    const highlightedText = originalText.replace(regex, '<span class="search-highlight">$1</span>');
                    
                    if (highlightedText !== originalText) {
                        cell.innerHTML = highlightedText;
                    }
                });
            }
            
            // Function to clear highlights
            function clearHighlights(row) {
                const searchableCells = row.querySelectorAll('[class*="searchable-"]');
                
                searchableCells.forEach(function(cell) {
                    // Remove highlight spans and restore original text
                    const highlightedElements = cell.querySelectorAll('.search-highlight');
                    highlightedElements.forEach(function(element) {
                        element.outerHTML = element.textContent;
                    });
                });
            }
            
            // Escape special regex characters
            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }

            // Check if any checkboxes are selected and enable/disable the delete button
            function updateDeleteButton() {
                const checkboxes = document.querySelectorAll('.product-checkbox:checked');
                const deleteButton = document.getElementById('deleteSelected');

                if (deleteButton) {
                    if (checkboxes.length > 0) {
                        deleteButton.removeAttribute('disabled');
                    } else {
                        deleteButton.setAttribute('disabled', 'disabled');
                    }
                }
            }

            // Delete a single row
            function deleteRow(element) {
                const row = element.closest('tr');
                row.remove();
                updateSelectAllCheckbox();
                updateDeleteButton();
            }

            // Delete all selected rows
            function deleteSelectedRows() {
                const selectedRows = document.querySelectorAll('.product-checkbox:checked');
                selectedRows.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    row.remove();
                });

                updateSelectAllCheckbox();
                updateDeleteButton();
            }

            // Update "Select All" checkbox state
            function updateSelectAllCheckbox() {
                const visibleCheckboxes = document.querySelectorAll('.product-row:not(.hidden-row) .product-checkbox');
                const selectAllCheckbox = document.getElementById('selectAll');

                if (visibleCheckboxes.length === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.disabled = true;
                } else {
                    const allChecked = document.querySelectorAll('.product-row:not(.hidden-row) .product-checkbox:checked').length === visibleCheckboxes.length;
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.disabled = false;
                }
            }

            // Set up event listeners
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize search functionality
                initializeSearch();
                
                // Select all checkbox
                const selectAllCheckbox = document.getElementById('selectAll');
                selectAllCheckbox.addEventListener('change', function() {
                    const visibleCheckboxes = document.querySelectorAll('.product-row:not(.hidden-row) .product-checkbox');
                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                    updateDeleteButton();
                });

                // Individual checkboxes
                document.addEventListener('change', function(e) {
                    if (e.target && e.target.classList.contains('product-checkbox')) {
                        updateSelectAllCheckbox();
                        updateDeleteButton();
                    }
                });

                // Delete selected button
                const deleteButton = document.getElementById('deleteSelected');
                if (deleteButton) {
                    deleteButton.addEventListener('click', deleteSelectedRows);
                }
            });
        </script>




    </div>
</body>

</html>