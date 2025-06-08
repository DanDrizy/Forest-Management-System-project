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
    <link rel="stylesheet" href="../css/stockin.css">
    <style>
        /* Loading overlay styles */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 18px;
            z-index: 9999;
            flex-direction: column;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced delete button styles */
        .delete-action {
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .delete-action:hover {
            background-color: #ff4444;
            color: white;
        }

        .delete-action:hover .icon {
            stroke: white;
        }

        /* Confirmation dialog styles */
        .confirm-dialog {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9998;
        }

        .confirm-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .confirm-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .confirm-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .confirm-btn.danger {
            background: #dc3545;
            color: white;
        }

        .confirm-btn.secondary {
            background: #6c757d;
            color: white;
        }

        .confirm-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <?php include '../menu/menu.php'; ?>

    <div class="main-content">
        <?php
        include '../header/header.php';
        include '../../database/connection.php';
        ?>

        <div class="dashboard-grid-i">
            <div class="stock-container">
                <h1>Stock In Inventory</h1>

                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" id="searchInput" placeholder="Search stock entries...">
                    </div>
                    <div class="button-container">
                        <a href="add-product.php" style=" text-decoration: none; ">
                            <button id="addStockBtn" class="add-btn" onclick="openAddModal()">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Stock in
                            </button>
                        </a>
                    </div>
                </div>

                <table id="stockTable">
                    <thead>
                        <tr>
                            <th class="row-id">ID</th>
                            <th>Product</th>
                            <th>Quantity/Amount</th>
                            <th>Measure</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th>inDate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $select = $pdo->prepare("SELECT * from stockin,timber,logs,plant,germination WHERE stockin.t_id = timber.t_id AND timber.l_id = logs.l_id AND logs.p_id = plant.p_id AND plant.g_id = germination.g_id ");
                        $select->execute();
                        $fetch = $select->fetchAll();

                        $i = 1;

                        foreach ($fetch as $row) {
                        ?>

                            <tr data-stock-id="<?php echo $row['in_id']; ?>" data-amount="<?php echo $row['s_amount']; ?>" data-price="<?php echo $row['price']; ?>">
                                <td class="row-id"><?php echo $i;
                                                    $i++; ?></td>
                                <td class="product-name"><span class="product-name-text"><?php echo $row['plant_name']; ?></span></td>
                                <td class="quantity"><span class="quantity-text"><?php echo $row['s_amount']; ?></span></td>
                                <td class="quantity"><span class="quantity-text">
                                        <font style=" color: grey; ">H:</font> <?php echo $row['t_height']; ?> <font style=" color: grey; ">W:</font> <?php echo $row['t_width']; ?>
                                    </span></td>
                                <td class="measures"><span class="measures-text"><?php echo number_format($row['price']); ?> Rwf</span></td>
                                <td class="measures total-price"><span class="measures-text"><?php echo  number_format($row['price'] * $row['s_amount']); ?> Rwf</span></td>

                                <td class="inDate"><span class="inDate-text"><?php echo $row['s_indate']; ?></span></td>
                                <td>
                                    <div class="action-container">
                                        <button class="edit-btn" onclick="openEditModal(this)">
                                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <div class="delete-action" onclick="confirmDelete(this)">
                                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Stock Item</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editForm">
                <input type="hidden" id="editStockId" name="stock_id">

                <div class="form-group">
                    <label for="editProduct">Product Name:</label>
                    <input type="text" id="editProduct" name="product" readonly>
                </div>

                <div class="form-group">
                    <label for="editAmount">Amount/Quantity:</label>
                    <input type="number" id="editAmount" name="amount" min="1" step="1" readonly>
                </div>

                <div class="form-group">
                    <label for="editPrice">Price (Rwf):</label>
                    <input type="number" id="editPrice" name="price" min="0" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="editTotal">Total Price (Rwf):</label>
                    <input type="text" id="editTotal" name="total" readonly>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirmation Dialog -->
    <div id="confirmDialog" class="confirm-dialog" style="display: none;">
        <div class="confirm-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this stock item?</p>
            <p><strong>This action cannot be undone.</strong></p>
            <div class="confirm-buttons">
                <button class="confirm-btn secondary" onclick="cancelDelete()">Cancel</button>
                <button class="confirm-btn danger" onclick="proceedDelete()">Delete</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Global variable to store the element to be deleted
        let elementToDelete = null;

        $(document).ready(function() {
            // Real-time search functionality
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();

                $('#stockTable tbody tr').each(function() {
                    const row = $(this);
                    const productName = row.find('.product-name-text').text().toLowerCase();
                    const quantity = row.find('.quantity-text').text().toLowerCase();
                    const price = row.find('.measures-text').text().toLowerCase();
                    const inDate = row.find('.inDate-text').text().toLowerCase();

                    // Check if any of the searchable fields contain the search term
                    const isMatch = productName.includes(searchTerm) ||
                        quantity.includes(searchTerm) ||
                        price.includes(searchTerm) ||
                        inDate.includes(searchTerm);

                    if (isMatch || searchTerm === '') {
                        row.removeClass('hidden-row').show();
                    } else {
                        row.addClass('hidden-row').hide();
                    }
                });
            });

            // Edit form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    stock_id: $('#editStockId').val(),
                    amount: $('#editAmount').val(),
                    price: $('#editPrice').val()
                };

                // AJAX call to update the stock item
                $.ajax({
                    url: 'update_stock.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            closeEditModal();
                            showLoadingOverlay('Stock item updated successfully! Refreshing data...');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            alert('Error updating stock item: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error occurred while updating stock item.');
                    }
                });
            });

            // Calculate total when amount or price changes
            $('#editAmount, #editPrice').on('input', function() {
                const amount = parseFloat($('#editAmount').val()) || 0;
                const price = parseFloat($('#editPrice').val()) || 0;
                const total = amount * price;
                $('#editTotal').val(number_format(total) + ' Rwf');
            });
        });

        // Show loading overlay
        function showLoadingOverlay(message) {
            const overlay = $(`
                <div id="loading-overlay">
                    <div class="loading-spinner"></div>
                    <div>${message}</div>
                </div>
            `);
            $('body').append(overlay);
        }

        // Confirm delete function
        function confirmDelete(element) {
            elementToDelete = element;
            $('#confirmDialog').show();
        }

        // Cancel delete
        function cancelDelete() {
            elementToDelete = null;
            $('#confirmDialog').hide();
        }

        // Proceed with delete
        function proceedDelete() {
            if (elementToDelete) {
                deleteRow(elementToDelete);
            }
            $('#confirmDialog').hide();
        }

        // Delete row function with AJAX
        function deleteRow(element) {
            const row = $(element).closest('tr');
            const stockId = row.data('stock-id');
            const productName = row.find('.product-name-text').text();

            // Show loading overlay
            showLoadingOverlay('Deleting stock item...');

            // AJAX call to delete from database
            $.ajax({
                url: 'delete_stock.php',
                method: 'POST',
                data: { stock_id: stockId },
                dataType: 'json',
                success: function(response) {
                    $('#loading-overlay').remove();
                    
                    if (response.success) {
                        // Remove the row from table with animation
                        row.fadeOut(300, function() {
                            $(this).remove();
                            // Update row numbers
                            updateRowNumbers();
                        });
                        
                        // Show success message
                        showSuccessMessage('Stock item deleted successfully!');
                    } else {
                        alert('Error deleting stock item: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loading-overlay').remove();
                    console.error('Delete error:', error);
                    alert('Error occurred while deleting stock item. Please try again.');
                }
            });
        }

        // Update row numbers after deletion
        function updateRowNumbers() {
            $('#stockTable tbody tr').each(function(index) {
                $(this).find('.row-id').text(index + 1);
            });
        }

        // Show success message
        function showSuccessMessage(message) {
            const successDiv = $(`
                <div style="position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 15px 20px; border-radius: 5px; z-index: 10000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    ${message}
                </div>
            `);
            
            $('body').append(successDiv);
            
            setTimeout(() => {
                successDiv.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // Open edit modal
        function openEditModal(button) {
            const row = $(button).closest('tr');
            const stockId = row.data('stock-id');
            const productName = row.find('.product-name-text').text();
            const currentAmount = row.data('amount');
            const currentPrice = row.data('price');

            $('#editStockId').val(stockId);
            $('#editProduct').val(productName);
            $('#editAmount').val(currentAmount);
            $('#editPrice').val(currentPrice);

            // Calculate initial total
            const total = currentAmount * currentPrice;
            $('#editTotal').val(number_format(total) + ' Rwf');

            $('#editModal').show();
        }

        // Close edit modal
        function closeEditModal() {
            $('#editModal').hide();
            $('#editForm')[0].reset();
        }

        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if (event.target.id === 'editModal') {
                closeEditModal();
            }
            if (event.target.id === 'confirmDialog') {
                cancelDelete();
            }
        });

        // Number formatting function
        function number_format(number) {
            return parseFloat(number).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // Escape key to close modals
            if (e.key === 'Escape') {
                if ($('#editModal').is(':visible')) {
                    closeEditModal();
                }
                if ($('#confirmDialog').is(':visible')) {
                    cancelDelete();
                }
            }
        });
    </script>
</body>

</html>