<?php
    require_once 'verify_jwt.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- jQuery Library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/add_products.css">
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
    </div>
    <div class="main">
    <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manageStaff.php"><i class="fa-solid fa-user"></i> Manage Staff</a>
        <a href="manageCustomer.php"><i class="fas fa-users"></i> Customer</a>
    
    <a href="#" class="product-link"><i class="fas fa-box"></i> Product Option</a>
        <div class="dropdown-menu" style="display: none;">
        <a href="category.php" class="dropdown-item">Manage Category</a>
        <a href="add_products.php" class="dropdown-item">Manage Product</a>
        <a href="manageProductQuantity.php" class="dropdown-item">Manage Product Quantity</a>
    </div>
    
    <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
    <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
    <a href="salesReport.php"><i class="fas fa-file-alt"></i> Sales Report</a>
</div>

        <div class="content">
    <div class="dashboard-header">
        <h1>Product List</h1>
    </div>
    <div class="table-container">
        <table id="productsTable" class="display nowrap">
            <thead>
                <tr>
                    <th> Product ID</th>
                    <th> Description</th>
                    <th> Price</th>
                    <th> Quantity</th>
                    <th> Category</th>
                    <th> Status</th>
                    <th> Image</th>
                </tr>
            </thead>
            <tbody id="products-table-body">
                <!-- Rows will be populated by JavaScript -->
            </tbody>
        </table>
    </div>
        <div id="pagination" class="pagination"></div>
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

    <!-- Modal for Confirm Delete -->
    <div id="deleteProductModal" class="modal">
        <div class="modal-content">
            <div class="form-container">
                <p>Are you sure you want to delete this product?</p>
                <div class="form-actions">
                    <button id="confirmDeleteBtn" class="delete-btn">Yes</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('deleteProductModal')">No</button>
                </div>
            </div>
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
        let currentProductId;

        let productsDataTable; // Declare globally

function fetchProducts(page = 1) {

    fetch('API/searchProduct.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('products-table-body');
        tbody.innerHTML = ''; // Clear existing rows

        if (data.error) {
            console.error('Error fetching products:', data.error);
            return;
        }

        data.products.forEach(product => {
            const row = document.createElement('tr');
            row.id = `product-${product.product_id}`;
            
            const imageHTML = product.image 
                ? `<img src="data:image/jpeg;base64,${product.image}" alt="Product Image" style="width:100px;height:auto;">` 
                : 'No Image';

            row.innerHTML = `
                <td>${product.product_id}</td>
                <td>${product.description}</td>
                <td>₱${product.price}</td>
                <td>${product.quantity}</td>
                <td>${product.category_name}</td>
                <td>${product.status}</td>
                <td>${imageHTML}</td>
            `;
            tbody.appendChild(row);
        });

        // Reinitialize DataTable
        if (productsDataTable) {
            productsDataTable.destroy(); // Destroy any existing instance
        }
        productsDataTable = $('#productsTable').DataTable({
            paging: true,         // Enable pagination
            pageLength: 10,       // Rows per page
            lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
            ordering: true,       // Enable sorting
            searching: true,      // Enable search
            info: true,           // Show table info
            autoWidth: false,     // Disable auto column width
            responsive: true      // Make table responsive
        });
    })
    .catch(error => console.error('Error fetching products:', error));
}

        // Fetch products on page load
        fetchProducts();

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
