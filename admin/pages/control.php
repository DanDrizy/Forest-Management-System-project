<?php
require_once '../../logs/backend/auth_check.php'; // Include the authentication check
checkUserAuth('admin'); // Check if the user is logged in and has the required role
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z5l5e5e5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/control.css">
    <title>Forestry Management System</title>
    <style></style>
</head>

<body>
    <?php
    include '../menu/menu.php';
    ?>

    <div class="main-content">
        <?php
        include '../../database/connection.php';

        $select = $pdo->query("SELECT * FROM users ORDER BY status DESC ");
        $select->execute();
        ?>

        <div class="container">
            <div class="page-header">
                <h1 class="page-title">User Management</h1>
                <a href="add-user.php"><button class="btn-add-user">+ Add New User</button></a>
            </div>

            <!-- <div class="search-container">
                <div class="search-box">
                    <a href="control-search.php"><svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg></a>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search users by name, email or role..." oninput="filterUsers()">
                </div>
            </div> -->

            <div class="search-container">
                <div class="search-box">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search users by name, email or role..." oninput="showSearchResults()" autocomplete="off">

                    <!-- Search Results Dropdown -->
                    <div id="searchResults" class="search-results-dropdown" style="display: none;">
                        <div class="search-results-header">
                            <span id="resultsCount">0 results found</span>
                            <button id="clearSearch" class="clear-search-btn" onclick="clearSearch()">Clear</button>
                        </div>
                        <div id="searchResultsList" class="search-results-list">
                            <!-- Dynamic results will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-container">
                <table class="users-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php
                        while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <tr>
                                <td>
                                    <div class="user-name">
                                        <div class="user-avatar">
                                            <?php
                                            if ($row['image'] == "") { ?>
                                                <i class="fa fa-user"></i>
                                            <?php
                                            } else {
                                                $image = $row['image'];
                                                echo "<img src='../backend/uploads/$image' alt='User Image' class='user-image'>";
                                            }
                                            ?>
                                        </div>
                                        <div class="user-info">
                                            <span><?php echo $row['name']; ?></span>
                                            <span class="user-email"><?php echo $row['email']; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    if ($row['role'] == 'admin') {
                                    ?>
                                        <span class="role-badge role-admin">Admin</span>
                                    <?php
                                    } else if ($row['role'] == 'sales') {
                                    ?>
                                        <span class="role-badge role-editor">Sales</span>
                                    <?php
                                    } else if ($row['role'] == 'Siliculture') {
                                    ?>
                                        <span class="role-badge role-viewer">Silviculture</span>
                                    <?php
                                    } else if ($row['role'] == 'pole plant') {
                                    ?>
                                        <span class="role-badge role-viewer">Pole Plants</span>
                                    <?php
                                    } else if ($row['role'] == 'sawmill') {
                                    ?>
                                        <span class="role-badge role-viewer">Sawmill</span>
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($row['status'] == 'active') {
                                    ?>
                                        <span class="status-indicator status-active"></span>Active
                                    <?php
                                    } else if ($row['status'] == 'blocked') {
                                    ?>
                                        <span class="status-indicator status-blocked"></span>Blocked
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['date']; ?></td>
                                <td>
                                    <div class="actions">
                                        <!-- edit -->
                                        <a href="user-edit.php?id=<?php echo $row['id']; ?>">
                                            <button class="btn-action btn-edit" title="Edit User">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                        </a>

                                        <!-- block & activate -->
                                        <?php
                                        if ($row['status'] == 'active') {
                                        ?>
                                            <a href="../backend/staus-user.php?id=<?php echo $row['id']; ?>&status=active">
                                                <button class="btn-action btn-block" title="Block User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                                    </svg>
                                                </button>
                                            </a>
                                        <?php
                                        } else if ($row['status'] == 'blocked') {
                                        ?>
                                            <a href="../backend/staus-user.php?id=<?php echo $row['id']; ?>&status=blocked">
                                                <button class="btn-action btn-block" style="background-color: #e6f7ef; color: #27ae60;" title="Unblock User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                    </svg>
                                                </button>
                                            </a>
                                        <?php
                                        }
                                        ?>
                                        <!-- delete button with onclick event -->
                                        <button class="btn-action btn-delete" title="Delete User" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div id="noResults" class="no-results" style="display: none;">
                    <p>No users found matching your search criteria.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal-container">
            <div class="modal-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <h3 class="modal-title">Confirm Delete</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="cancelDelete">Cancel</button>
                <button class="btn-confirm-delete" id="confirmDelete">Delete User</button>
            </div>
        </div>
    </div>

    <!-- Filter users script -->
    <script>
        function filterUsers() {
            // Your existing filter code if any
        }
    </script>

    <!-- Delete confirmation script -->
    <script>
        // Variable to store the user ID to be deleted
        let userId = null;

        // Function to show confirmation dialog
        function confirmDelete(id) {
            userId = id;
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }

        // Handle cancel button click
        document.getElementById('cancelDelete').addEventListener('click', function() {
            // Hide the modal
            document.getElementById('deleteConfirmModal').style.display = 'none';
            userId = null;
        });

        // Handle confirm button click
        document.getElementById('confirmDelete').addEventListener('click', function() {
            // If user confirms, navigate to the delete URL
            if (userId) {
                window.location.href = '../backend/user-delete.php?id=' + userId;
            }
        });

        // Close the modal if user clicks outside of it
        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
                userId = null;
            }
        });


        let allUsers = []; // Store all users data
let searchTimeout;

// Extract users data when page loads
document.addEventListener('DOMContentLoaded', function() {
    extractUsersData();
});

function extractUsersData() {
    const tableRows = document.querySelectorAll('#tableBody tr');
    allUsers = [];
    
    tableRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            // Extract user data
            const userInfo = cells[0].querySelector('.user-info');
            const userName = userInfo.children[0].textContent.trim();
            const userEmail = userInfo.children[1].textContent.trim();
            
            // Extract image
            const avatarImg = cells[0].querySelector('.user-avatar img');
            const avatarIcon = cells[0].querySelector('.user-avatar i');
            const userImage = avatarImg ? avatarImg.src : null;
            
            // Extract role
            const roleBadge = cells[1].querySelector('.role-badge');
            const userRole = roleBadge.textContent.trim();
            const roleClass = roleBadge.className;
            
            // Extract status
            const statusText = cells[2].textContent.trim();
            const statusIndicator = cells[2].querySelector('.status-indicator');
            const statusClass = statusIndicator ? statusIndicator.className : '';
            
            // Extract date
            const joinDate = cells[3].textContent.trim();
            
            // Extract user ID from edit link
            const editLink = cells[4].querySelector('a[href*="user-edit.php"]');
            const userId = editLink ? editLink.href.split('id=')[1] : null;
            
            allUsers.push({
                id: userId,
                name: userName,
                email: userEmail,
                role: userRole,
                roleClass: roleClass,
                status: statusText,
                statusClass: statusClass,
                date: joinDate,
                image: userImage,
                hasIcon: !!avatarIcon
            });
        }
    });
}

function showSearchResults() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    if (searchTerm === '') {
        searchResults.style.display = 'none';
        return;
    }
    
    // Debounce search for better performance
    searchTimeout = setTimeout(() => {
        const filteredUsers = allUsers.filter(user => {
            return user.name.toLowerCase().includes(searchTerm) ||
                   user.email.toLowerCase().includes(searchTerm) ||
                   user.role.toLowerCase().includes(searchTerm) ||
                   user.status.toLowerCase().includes(searchTerm);
        });
        
        displaySearchResults(filteredUsers, searchTerm);
    }, 200);
}

function displaySearchResults(users, searchTerm) {
    const searchResults = document.getElementById('searchResults');
    const resultsCount = document.getElementById('resultsCount');
    const resultsList = document.getElementById('searchResultsList');
    
    resultsCount.textContent = `${users.length} result${users.length !== 1 ? 's' : ''} found`;
    
    if (users.length === 0) {
        resultsList.innerHTML = '<div class="no-search-results">No users found matching your search.</div>';
    } else {
        resultsList.innerHTML = users.map(user => `
            <div class="search-result-item" onclick="selectUser('${user.id}')">
                <div class="search-result-avatar">
                    ${user.image ? 
                        `<img src="${user.image}" alt="User Image">` : 
                        `<i class="fa fa-user"></i>`
                    }
                </div>
                <div class="search-result-info">
                    <div class="search-result-name">${highlightText(user.name, searchTerm)}</div>
                    <div class="search-result-email">${highlightText(user.email, searchTerm)}</div>
                </div>
                <div class="search-result-role ${user.roleClass}">
                    ${highlightText(user.role, searchTerm)}
                </div>
                <div class="search-result-status ${user.statusClass}"></div>
            </div>
        `).join('');
    }
    
    searchResults.style.display = 'block';
}

function highlightText(text, searchTerm) {
    if (!searchTerm) return text;
    
    const regex = new RegExp(`(${searchTerm})`, 'gi');
    return text.replace(regex, '<span class="search-highlight">$1</span>');
}

function selectUser(userId) {
    // You can customize this function based on what you want to do when a user is selected
    // For example, scroll to the user in the table or open their profile
    
    // Hide search results
    document.getElementById('searchResults').style.display = 'none';
    
    // Find and highlight the user row in the table
    const tableRows = document.querySelectorAll('#tableBody tr');
    tableRows.forEach(row => {
        row.style.backgroundColor = ''; // Reset background
        const editLink = row.querySelector('a[href*="user-edit.php"]');
        if (editLink && editLink.href.includes(`id=${userId}`)) {
            row.style.backgroundColor = '#fff3cd';
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 3000);
        }
    });
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchResults').style.display = 'none';
}

// Hide search results when clicking outside
document.addEventListener('click', function(event) {
    const searchContainer = document.querySelector('.search-container');
    if (!searchContainer.contains(event.target)) {
        document.getElementById('searchResults').style.display = 'none';
    }
});

// Prevent search results from hiding when clicking inside the dropdown
document.getElementById('searchResults').addEventListener('click', function(event) {
    event.stopPropagation();
});
    </script>

</body>

</html>