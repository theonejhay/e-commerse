<?php
require_once 'verify_jwt.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/category.css">
    <link rel="stylesheet" href="style.css"> <!-- Ensure your style.css is linked -->
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="images/logo.jpg" alt="Logo">
        </div>
        <div class="nav">
            <span class="nav-label">Current Time:</span>
            <span class="current-time" id="currentTime"></span>
            <span class="nav-label">Current Date:</span>
            <span id="currentDate"></span>
            <span class="nav-label">Current Day:</span>
            <span id="currentDay"></span>
            <i id="toggleSidebarBtn"></i>
            <a id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
    </div>

    <div class="main">
        <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manageStaff.php"><i class="fa-solid fa-user"></i> Manage Staff</a>
        <a href="manageCustomer.php"><i class="fas fa-users"></i> Customer</a>
    
    <a href="#" class="product-link"><i class="fas fa-box"></i> Product Option</a>
    <div class="dropdown-menu" style="display: none;">
        <a href="products.php" class="dropdown-item">Products</a>
        <a href="category.php" class="dropdown-item">Manage Category</a>
        <a href="add_products.php" class="dropdown-item">Manage Product</a>
        <a href="manageProductQuantity.php" class="dropdown-item">Manage Product Quantity</a>
    </div>
    
    <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
    <a href="salesReport.php"><i class="fas fa-file-alt"></i> Sales Report</a>
</div>

        <!-- Manage Categories Content -->
        <div class="content">
            <h1>Manage Categories</h1>

            <!-- Add New Category Form -->
            <div>
                <h3>Add New Category</h3>
                <form id="addCategoryForm">
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" required>
                    <button type="submit">Add Category</button>
                </form>
            </div>

            <!-- Categories Table -->
            <h3>Existing Categories</h3>
            <table id="categoriesTable">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesBody">
                    <!-- Categories will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logout Modal -->
<div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Logout Confirmation</h2>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const day = now.toLocaleDateString('en-US', { weekday: 'long' });
            const date = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

            document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
            document.getElementById('currentDay').textContent = day;
            document.getElementById('currentDate').textContent = date;
        }

        // Update time initially and every second
        updateClock();
        setInterval(updateClock, 1000);
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchCategories();

            // Handle Add Category form submission
            document.getElementById('addCategoryForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const category_name = document.getElementById('category_name').value;
                fetch('API/manageCategories.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'add',
                        category_name: category_name
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        fetchCategories(); // Refresh categories list
                        event.target.reset();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Fetch and display categories
function fetchCategories() {
    fetch('API/manageCategories.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('categoriesBody');
        tbody.innerHTML = ''; // Clear current categories

        // Check if categories exist in the response
        if (data.categories && Array.isArray(data.categories)) {
            data.categories.forEach(category => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${category.category_name}</td>
                    <td>
                        <button onclick="editCategory(${category.category_id}, '${category.category_name}')">Edit</button>
                        <button onclick="deleteCategory(${category.category_id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            console.error('No categories found or invalid response format');
        }
    })
    .catch(error => console.error('Error:', error));
}


        // Edit category
        function editCategory(category_id, category_name) {
            const newName = prompt('Edit Category Name:', category_name);
            if (newName) {
                fetch('API/manageCategories.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'update',
                        category_id: category_id,
                        category_name: newName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        fetchCategories(); // Refresh categories list
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Delete category
        function deleteCategory(category_id) {
            if (confirm('Are you sure you want to delete this category?')) {
                fetch('API/manageCategories.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'delete',
                        category_id: category_id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        fetchCategories(); // Refresh categories list
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
    // Get the modal
    const modal = document.getElementById('logoutModal');

    // Get the cancel button, logout button, and confirm button
    const cancelBtn = document.getElementById('cancelLogout');
    const logoutBtn = document.getElementById('logoutBtn');
    const confirmBtn = document.getElementById('confirmLogout');

    // Get the toggle button/icon and sidebar
    const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
    const sidebar = document.getElementById('sidebar');

    // Only add event listeners if the elements are found
    if (logoutBtn && modal) {
        logoutBtn.onclick = function() {
            modal.style.display = "block";
        };
    }

    if (cancelBtn && modal) {
        cancelBtn.onclick = function() {
            modal.style.display = "none";
        };
    }

    if (confirmBtn) {
        confirmBtn.onclick = function() {
            window.location.href = "adminLogout.php";
        };
    }

    if (toggleSidebarBtn && sidebar) {
        toggleSidebarBtn.addEventListener('click', function() {
            if (sidebar.style.display === 'none' || sidebar.style.display === '') {
                sidebar.style.display = 'block'; // Show the sidebar
            } else {
                sidebar.style.display = 'none'; // Hide the sidebar
            }
        });
    }   

    // Close modal if clicking outside it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
});

document.addEventListener("DOMContentLoaded", function() {
    const productLink = document.querySelector(".product-link");
    const dropdownMenu = document.querySelector(".dropdown-menu");

    productLink.addEventListener("click", function(event) {
        event.preventDefault();  // Prevents the default link action
        dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";  // Toggles visibility
    });
});
    </script>
</body>
</html>
