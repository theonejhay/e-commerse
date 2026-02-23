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
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
    <a href="products.php" class="dropdown-item">Products</a>
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
        <h1>Product Management</h1>
    </div>
    <div class="add-container">
        <button class="add-product-btn" onclick="openAddProductModal()">Add New Product</button>   
    </div>
    <div class="table-container">
        <table id="productsTable" class="display nowrap">
            <thead>
                <tr>
                    <th> Product ID</th>
                    <th> Description</th>
                    <th> Price</th>
                    <th> Category</th>
                    <th> Image</th>
                    <th> Status</th>
                    <th> Actions</th>
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

<!-- Modal for Add Product -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <h2>Add New Product</h2>
        <div class="form-container">
            <form id="productForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="productDescription">Product Description</label>
                    <input type="text" id="productDescription" name="description" required>
                </div>
                <div class="form-group">
                    <label for="productPrice">Price</label>
                    <input type="number" id="productPrice" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="productCategory">Category</label>
                    <select id="productCategory" name="category" required>
                        <option value="">Select a category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="productImage">Product Image</label>
                    <input type="file" id="productImage" name="image" accept="image/*">
                </div>
                <div class="form-actions">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('addProductModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Edit Product -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <div class="form-container">
            <form id="editProductForm">
                <input type="hidden" id="editProductId" name="product_id">
                <div class="form-group">
                    <label for="editProductDescription">Product Description</label>
                    <input type="text" id="editProductDescription" name="description" required>
                </div>
                <div class="form-group">
                    <label for="editProductPrice">Price</label>
                    <input type="number" id="editProductPrice" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="editProductCategory">Category</label>
                    <select id="editProductCategory" name="category" required>
                        <option value="">Select a category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editProductStatus">Status</label>
                    <select id="editProductStatus" name="status" required>
                        <option value="available">Available</option>
                        <option value="not available">Not Available</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="save-btn">Update</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('editProductModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal for Add Image -->
<div id="addImageModal" class="modal">
    <div class="modal-content">
        <div class="form-container">
            <form id="imageForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="imageProductId">Product ID</label>
                    <input type="text" id="imageProductId" name="product_id" required readonly>
                </div>
                <div class="form-group">
                    <label for="productImage">Product Image</label>
                    <input type="file" id="productImage" name="image" accept="image/*" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="save-btn">Upload</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('addImageModal')">Cancel</button>
                </div>
            </form>
            <div id="modalAlert" style="display:none; margin-top: 10px;"></div>
        </div>
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
        let productsDataTable; 

function fetchProducts(page = 1) {
    fetch('API/searchProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
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

        const rows = data.products.map(product => {
            const imageHTML = product.image 
                ? `<img src="data:image/jpeg;base64,${product.image}" alt="Product Image" style="width:100px;height:auto;">` 
                : 'No Image';

            return [
                product.product_id,
                product.description,
                `₱${product.price}`,
                product.category_name,
                imageHTML,
                product.status,
                `
                <button onclick="addProductImage('${product.product_id}')"><i class="fas fa-image"></i></button>
                <button onclick="editProduct(${product.product_id}, '${product.description}', ${product.price}, '${product.category}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="confirmDelete(${product.product_id})"><i class="fas fa-trash"></i> Delete</button>
                `
            ];
        });

        if (productsDataTable) {
            productsDataTable.clear(); // Clear existing DataTable rows
            productsDataTable.rows.add(rows); // Add new rows
            productsDataTable.draw(); // Redraw the table
        } else {
            productsDataTable = $('#productsTable').DataTable({
                data: rows,
                columns: [
                    { title: 'ID' },
                    { title: 'Description' },
                    { title: 'Price' },
                    { title: 'Category' },
                    { title: 'Image' },
                    { title: 'Status' },
                    { title: 'Actions' },
                ],
                paging: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                ordering: true,
                searching: true,
                info: true,
                autoWidth: false,
                responsive: true,
            });
        }
    })
    .catch(error => console.error('Error fetching products:', error));
}



        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            clearModalAlert();
        }

        function confirmDelete(productId) {
            currentProductId = productId;
            openModal('deleteProductModal');
            // Attach event listener for delete button click
            document.getElementById('confirmDeleteBtn').addEventListener('click', onDeleteButtonClick);
        }

function onDeleteButtonClick() {
    fetch(`API/deleteProduct.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${currentProductId}`,
    })
    .then(response => {
        if (response.ok) {
            // Attempt to remove the row using the DOM
            const row = document.getElementById(`product-${currentProductId}`);
            if (row) {
                row.remove();
            } else {
            }

            // Optionally, re-fetch the products to ensure the table is accurate
            fetchProducts();

            closeModal('deleteProductModal');
        } else {
            console.error('Error deleting product');
        }
    })
    .catch(error => console.error('Error deleting product:', error));
}

async function loadCategories(selectElementId) {
    try {
        const categorySelect = document.getElementById(selectElementId);

        // Check if the categorySelect element is found
        if (!categorySelect) {
            console.error(`Element with ID '${selectElementId}' not found.`);
            return; // Exit the function early if the element isn't found
        }

        const response = await fetch('API/getCategory.php');
        const categories = await response.json();

        // Clear existing options and add the default option
        categorySelect.innerHTML = '<option value="">Select a category</option>';

        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.category_id;
            option.textContent = category.category_name;
            categorySelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}


function openAddProductModal() {
    loadCategories('productCategory'); // Populate categories
    document.getElementById('productForm').reset(); // Clear previous input
    openModal('addProductModal'); // Open modal
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}


document.getElementById('productForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);

    try {
        // Step 1: Add the product with the image
        const response = await fetch('API/addProduct.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        var modalAlert = document.getElementById('modalAlert');

        if (response.ok) {
            modalAlert.style.display = 'block';
            modalAlert.style.color = 'green';
            modalAlert.innerText = result.message || 'Product added successfully';

            event.target.reset();
                closeModal('addProductModal');
                fetchProducts(); // Refresh the product list
           
        } else {
            console.error('Error adding product:', result.message);
            modalAlert.style.display = 'block';
            modalAlert.style.color = 'red';
            modalAlert.innerText = 'Failed to add product: ' + result.message;
        }
    } catch (error) {
        console.error('Error:', error);
        var modalAlert = document.getElementById('modalAlert');
        modalAlert.style.display = 'block';
        modalAlert.style.color = 'red';
        modalAlert.innerText = 'An unexpected error occurred. Please try again later.';
    }
});

function editProduct(productId, description, price, category) {
    document.getElementById('editProductId').value = productId;
    document.getElementById('editProductDescription').value = description;
    document.getElementById('editProductPrice').value = price;

    // Load categories and preselect the current category
    loadCategories('editProductCategory').then(() => {
        document.getElementById('editProductCategory').value = category;
    });

    openModal('editProductModal');
}

         // Function to open the modal
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

// Function to close the modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Function to add an image to the product
function addProductImage(productId) {
    document.getElementById('imageProductId').value = productId;
    openModal('addImageModal');
}


// Function to clear modal alerts
function clearModalAlert() {
    var modalAlert = document.getElementById('modalAlert');
    modalAlert.style.display = 'none';
    modalAlert.innerText = '';
}

document.getElementById('imageForm').addEventListener('submit', (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);

    fetch('API/addImage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()
        .then(data => ({ status: response.status, body: data }))
    )
    .then(result => {
        const { status, body } = result;
        var modalAlert = document.getElementById('modalAlert');

        if (status === 200) {
            modalAlert.style.display = 'block';
            modalAlert.style.color = 'green';
            modalAlert.innerText = body.message;
            
            setTimeout(() => {
                closeModal('addImageModal');
                fetchProducts(); 
            }, 2000);
        } else {
            console.error('Error:', body.message);
            modalAlert.style.display = 'block';
            modalAlert.style.color = 'red';
            modalAlert.innerText = 'Failed to update product image: ' + body.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        var modalAlert = document.getElementById('modalAlert');
        modalAlert.style.display = 'block';
        modalAlert.style.color = 'red';
        modalAlert.innerText = 'An unexpected error occurred. Please try again later.';
    });
});

//  Close the modal when clicking outside of it
window.onclick = function(event) {
    var modal = document.getElementById('addImageModal');
    if (event.target === modal) {
        closeModal('addImageModal');
    }
}

document.getElementById('editProductForm').addEventListener('submit', (event) => {
    event.preventDefault();

    const formData = new FormData(event.target);
    const productData = {};
    formData.forEach((value, key) => {
        productData[key] = value;
    });

    fetch('API/updateProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(productData)
    })
    .then(response => {
        if (response.ok) {
            const row = document.getElementById(`product-${productData.product_id}`);
            if (row) {
                row.innerHTML = `
                    <td>${productData.product_id}</td>
                    <td>${productData.description}</td>
                    <td>₱${productData.price}</td>
                    <td>${productData.category}</td>
                    <td>${productData.status}</td>
                    <td>
                        <button onclick="editProduct(${productData.product_id}, '${productData.description}', ${productData.price}, '${productData.category}', '${productData.status}')"><i class="fas fa-edit"></i> Edit</button>
                        <button onclick="confirmDelete(${productData.product_id})"><i class="fas fa-trash"></i> Delete</button>
                    </td>
                `;
            }
            closeModal('editProductModal');
            location.reload();
        } else {
            console.error('Error updating product');
        }
    })
    .catch(error => console.error('Error updating product:', error));
});



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
