<?php
session_start();

// Check if the user is logged in (session variable is set)
if (!isset($_SESSION['customer_id'])) {
    // Redirect to login page if customer_id is not set in session
    header("Location: index.php");
    exit();
}

// Retrieve the customer ID from the session
$customer_id = $_SESSION['customer_id'];
?>

<script>
    const customerId = '<?php echo $customer_id; ?>';
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/homepage.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Index</title>
</head>
<body>


<!-- Loading Indicator -->
<div id="loadingIndicator" class="loading-spinner" style="display: none;">
    <div class="spinner-container">
        <div class="spinner"></div>
        <span>Loading...</span>
    </div>
</div>

<div class="header-section">
  <div class="header navbar navbar-expand-lg">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
<div class="restaurant-header">
  <div class="restaurant-container">
    <div class="restaurant-details">
      </div>
      </div>
  </div>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
          <a class="nav-link" href="index.php"> Home </a>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link cart-link" href="#" onclick="openCartModal()">
                Cart <span id="cartBadge" class="badge bg-danger"></span>
            </a>
        </li>

          <li class="nav-item">
            <a class="nav-link" href="#" onclick="openLogoutModal()">Log Out
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <!-- Profile Image and Username -->
  <div class="profile-container">
            <a class="nav-link" href="account.php">
              <img id="profile-image" src="default_profile.png" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 50%;">
              <span id="profile-username">Account</span>
            </a>
            </div>
            
<!-- Search Bar Below Navbar -->
<div class="search-bar-container">
  <div class="container">
    <form id="searchForm" class="d-flex" role="search" onsubmit="event.preventDefault();">
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-search"></i> <!-- Icon for search input -->
        </span>
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="query" id="searchQuery">
      </div>
    </form>
  </div>
</div>

<div class="main-content">
    <div class="product-container">
        <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
    Categories
</button>
<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <li><button class="dropdown-item" onclick="fetchProducts(1, '', 'Combo')"><i class="fas fa-box"></i> Combo</button></li>
    <li><button class="dropdown-item" onclick="fetchProducts(1, '', 'Solo')"><i class="fas fa-hamburger"></i> Solo</button></li>
    <li><button class="dropdown-item" onclick="fetchProducts(1, '', 'Special')"><i class="fas fa-star"></i> Special</button></li>
    <li><button class="dropdown-item" onclick="fetchProducts(1, '', 'Drinks')"><i class="fas fa-glass-martini-alt"></i> Drinks</button></li>
</ul>

        </div>
        <div id="products" class="products-list">
            <!-- Products will be dynamically added here -->
        </div>
        <div id="pagination" class="pagination-controls"></div>
    </div>
    
    <div class="cart-container">
    <div class="cart">
        <h2><i class="fas fa-shopping-cart"></i> Cart Items</h2>
        <table class="cart-items">
            <thead>
                <tr>
                    <th><i class="fas fa-box"></i> Product</th>
                    <th><i class="fas fa-sort-numeric-up"></i> Quantity</th>
                    <th><i class="fas fa-sort-numeric-up"></i> Price</th>
                </tr>
            </thead>
            <tbody id="cartItemsBody">
                <!-- Cart items will be dynamically added here -->
            </tbody>
        </table>
        <div id="cartPagination" class="cart-pagination-controls"></div> <!-- Pagination controls -->
        <p class="cart-total">
            <i class="fas fa-calculator"></i> PAGE TOTAL: ₱0 <!-- Page total icon added -->
        </p>
        <p class="overall-cart-total">
            <i class="fas fa-calculator"></i> TOTAL: ₱0 <!-- Overall total icon added -->
        </p>
        <button class="cart-addToCart" onclick="placeOrder()">
            <i class="fas fa-check"></i> Place Order
        </button>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal logout-modal">
    <div class="modal-content logout-modal-content">
        <h2>Logout Confirmation</h2>
        <p>Are you sure you want to log out?</p>
        <div class="modal-buttons">
            <button class="logout-btn" onclick="confirmLogout()">Logout</button>
            <button class="cancel-btn" onclick="closeLogoutModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Cart Modal -->
<div id="cartModal" class="modal">
    <div class="cart-modal-content">
        <span class="close" onclick="closeCartModal()">&times;</span>
        <h2>Your Cart</h2>
        <div id="cartModalItems">
            <!-- Cart items will be dynamically inserted here -->
        </div>
<!-- Modal Structure for cancelOrder Confirmation -->
<div id="cancelOrderModal" class="cancel-order-modal">
  <div class="cancel-order-modal-content">
    <span class="cancel-order-modal-close">&times;</span>
    <p id="cancelOrderModalMessage"></p>
    <div id="cancelOrderModalButtons" style="display: none;">
      <button id="cancelOrderCancelBtn">Cancel</button>
      <button id="cancelOrderConfirmBtn">Confirm</button>
    </div>
  </div>
</div>
</div>
</div>

<!-- Pickup Date and Time Modal -->
<div id="pickupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePickupModal()">&times;</span>
        <h2>Set Pickup Date and Time</h2>
        <form id="pickupForm">
    <label for="pickupDate">Pickup Date:</label>
    <input type="date" id="pickupDate" required>

    <label for="pickupTime">Pickup Time:</label>
    <input type="time" id="pickupTime" min="07:00" max="18:00" required>

    <label for="otpCode">OTP Code:</label>
    <input type="text" id="otpCode" placeholder="Enter OTP" maxlength="6" required>

    <p class="note">Note: Pickup time is only available between 7 AM and 6 PM.</p>

    <button type="submit">Confirm Pickup</button>
</form>
    </div>
</div>

<!-- Orders Modal -->
<div id="ordersModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeOrdersModal()">&times;</span>
        <h2>Order Details for Customer ID: <span id="modalCustomerId"></span></h2>
        <p>Pickup Date: <span id="modalPickupDate"></span></p>
        <p>Pickup Time: <span id="modalPickupTime"></span></p>
        <p>Remaining Time: <span id="remainingTime"></span></p>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Total Amount</th>
                    <th>Total Orders</th>
                    <th>Confirmation Status</th>
                </tr>
            </thead>
            <tbody id="modalOrderTableBody">
                <!-- Data will be dynamically inserted here -->
            </tbody>
        </table>
    </div>
</div>


<!-- Checkout Confirmation Modal -->
<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCheckoutModal()">&times;</span>
        <p>Are you sure you want to place this order?</p>
        <button onclick="confirmCheckout()">Yes, Place Order</button>
        <button onclick="closeCheckoutModal()">Cancel</button>
    </div>
</div>

<!-- Out of Stock Modal -->
<div id="outOfStockModal" class="modal out-of-stock-modal">
    <div class="modal-content out-of-stock-modal-content">
        <span class="close" onclick="closeOutOfStockModal()">&times;</span>
        <h2>Out of Stock</h2>
        <p>This product is out of stock.</p>
        <div class="modal-buttons">
            <button class="okay-btn" onclick="closeOutOfStockModal()">Okay</button>
        </div>
    </div>
</div>

<!-- Modal Structure for placeOrder Confirmation -->
<div id="placeOrderModal" class="place-order-modal">
  <div class="place-order-modal-content">
    <span class="place-order-modal-close">&times;</span>
    <p id="placeOrderModalMessage"></p>
    <div id="placeOrderModalButtons" style="display: none;">
      <button id="placeOrderCancelBtn">Cancel</button>
      <button id="placeOrderConfirmBtn">Confirm</button>
    </div>
  </div>
</div>

<!-- Pickup Time Validation Modal -->
<div id="pickupTimeValidationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('pickupTimeValidationModal')">&times;</span>
        <p id="pickupTimeValidationMessage"></p>
        <button class="confirm-button" onclick="confirmPickupTime()">Confirm</button>
        <button class="cancel-button" onclick="closeModal('pickupTimeValidationModal')">Cancel</button>
    </div>
</div>

<!-- Late Pickup Modal -->
<div id="latePickupModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('latePickupModal')">&times;</span>
        <p id="latePickupModalMessage"></p>
        <button class="confirm-button" onclick="confirmLatePickup()">Confirm</button>
        <button class="cancel-button" onclick="closeModal('latePickupModal')">Cancel</button>
    </div>
</div>

<!-- General Pickup Modal -->
<div id="pickupGeneralModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('pickupGeneralModal')">&times;</span>
        <p id="pickupGeneralModalMessage"></p>
        <button class="confirm-button" onclick="confirmGeneralPickup()">OK</button>
        <button class="cancel-button" onclick="closeModal('pickupGeneralModal')">Cancel</button>
    </div>
</div>

<div id="noSelectionModal" class="modal">
    <div class="modal-content">
        <span class="no-selection-modal-close" style="cursor: pointer;">&times;</span>
        <h2>No Selection</h2>
        <p>Please select at least one item to cancel.</p>
        <button id="noSelectionCloseBtn">Close</button>
    </div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"></p>
    </div>
</div>

<script>
    let cartTotal = 0;
    const products = {};  // To store product details
    let savedCart = JSON.parse(localStorage.getItem(`cart_${customerId}`)) || {};
    const savedCartTotal = localStorage.getItem(`cartTotal_${customerId}`) || 0;

    let currentPage = 1;
    const productsPerPage = 3;  // You can change this to any number

    function resetProductQuantities() {
    document.querySelectorAll('input[id^="quantity-"]').forEach((input) => {
        input.value = 0;
    });
}

function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

function fetchProducts(page = 1, query = '', category = '') {
    const searchQuery = query || new URLSearchParams(window.location.search).get('query') || ''; 
    const selectedCategory = category || '';

    fetch('API/searchProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            description: searchQuery, // Pass the search query to the backend
            page: page, 
            limit: productsPerPage,
            category: selectedCategory
        })
    })
    .then(response => response.json())
    .then(data => {
        const productsContainer = document.getElementById('products');
        productsContainer.innerHTML = '';

        if (Array.isArray(data.products) && data.products.length > 0) {
            data.products.forEach(product => {
                if (!product.product_id) {
                    console.error('Product ID is undefined for one of the products:', product);
                    return;
                }
                products[product.product_id] = product;

                const productCard = document.createElement('div');
                productCard.classList.add('product-card');
                productCard.innerHTML = `
<h1 class="product-description">
    ${product.description} 
    <i class="fas fa-tag"></i> <!-- Icon for product description -->
</h1>
${product.image ? `<img class="product-image" src="data:image/jpeg;base64,${product.image}" alt="${product.description}">` : ''}
<button onclick="openProductModal(${product.product_id})">
    <i class="fas fa-shopping-cart"></i> Order Now
</button>
<!-- Product Modal -->
<div id="productModal-${product.product_id}" class="modal product-modal">
    <div class="modal-content">
        <span class="close" onclick="closeProductModal(${product.product_id})">&times;</span>
        <div class="modal-body">
            <div class="product-image">
                ${product.image ? `<img src="data:image/jpeg;base64,${product.image}" alt="${product.description}" class="modal-product-image">` : ''}
            </div>
            <div class="product-info">
                <h1 class="product-title">
                    ${product.description} 
                    <i class="fas fa-box-open"></i> <!-- Icon for product title -->
                </h1>
                <p class="product-description">
                    Click Add to Cart to order ${product.description}.
                    <i class="fas fa-info-circle"></i> <!-- Icon for info -->
                </p>
                <div class="product-rating">
                    <span class="stars">★★★★☆</span> 
                    (${product.reviews}) 
                    <i class="fas fa-star"></i> <!-- Icon for rating -->
                </div>
                <div class="product-pricing">
                    <p>
                        <strong>for only ₱ ${product.price}</strong> 
                        <i class="fas fa-tag"></i> <!-- Icon for pricing -->
                    </p>
                </div>
                <div class="quantity-controls">
                    <p class="stock-info">
                        Only <span class="stock-count">${product.quantity}</span> Items Left 
                        <i class="fas fa-boxes"></i> <!-- Icon for stock info -->
                    </p>
                </div>
                <div class="quantity-controls">
                    <input type="text" id="quantity-${product.product_id}" value="${savedCart[product.product_id]?.quantity || 0}" readonly>
                </div>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQuantity(${product.product_id})">
                        <i class="fas fa-minus"></i> <!-- Icon for decrease -->
                    </button>
                    <button class="quantity-add" 
    onclick="increaseQuantity(${product.product_id})">
    <i class="fas fa-plus"></i> Add To Cart
</button>
                </div>
            </div>
        </div>
    </div>
</div>
`;

                productsContainer.appendChild(productCard);
            });

            resetProductQuantities();

            cartTotal = 0;
            for (const id in savedCart) {
                if (savedCart[id].quantity > 0 && products[id]) {
                    cartTotal += products[id].price * savedCart[id].quantity;
                }
            }

            document.querySelector('.cart-total').textContent = `TOTAL: ₱${cartTotal}`;
            updateCartDisplay();
            updatePaginationControls(data.total);
        } else {
            productsContainer.innerHTML = `<p>No products found.</p>`;
        }
    })
    .catch(error => {
        console.error('Error fetching products:', error);
        document.getElementById('products').innerHTML = `<p>Error fetching products: ${error.message}</p>`;
    });
}

// Add event listener to the search input for real-time search
const searchInput = document.getElementById('searchQuery');
const debouncedSearch = debounce(() => {
    const query = searchInput.value;
    fetchProducts(1, query); // Call fetchProducts on every input with the current query
}, 300); // Debounce with a 300ms delay to prevent excessive API calls

searchInput.addEventListener('input', debouncedSearch); // Trigger search on input

function increaseQuantity(productId) {
    const product = products[productId];  // Get the product details

    // Check if the product is out of stock
    if (product.quantity === 0) {
        showOutOfStockModal();  // Show the "Out of Stock" modal
        return;  // Stop further execution
    }

    const quantityInput = document.getElementById(`quantity-${productId}`);
    let quantity = parseInt(quantityInput.value);

    // Increase quantity on the frontend
    quantityInput.value = quantity + 1;

    // Update the `savedCart` object
    if (!savedCart[productId]) {
        savedCart[productId] = { quantity: 1, description: product.description, price: product.price, image: product.image };
    } else {
        savedCart[productId].quantity += 1;
    }

    // Update cart total and display without requiring a page reload
    updateCartDisplay();

    // Call the API to update the quantity in the backend
    fetch('API/updateProductQuantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 }) // Decrease stock by 1 in the backend
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const newQuantity = data.new_quantity;

            // Update product quantity in the products object
            products[productId].quantity = newQuantity;

            // Update the stock count display
            const stockCountElement = document.querySelector(`#productModal-${productId} .stock-count`);
            if (stockCountElement) {
                stockCountElement.textContent = newQuantity;
            }

            // Disable/enable the "Add to Cart" button based on new stock
            const addButton = document.querySelector(`#productModal-${productId} .quantity-add`);
            if (newQuantity === 0) {
                addButton.disabled = true;
                document.querySelector(`#productModal-${productId} .stock-info`).innerHTML = `Out of Stock <i class="fas fa-boxes"></i>`;
            } else {
                addButton.disabled = false;
                document.querySelector(`#productModal-${productId} .stock-info`).innerHTML = `Only ${newQuantity} Items Left <i class="fas fa-boxes"></i>`;
            }

            // Optionally save the cart to the backend
            saveCartToDatabase(savedCart);  // Save the updated cart to the server
        } else {
            alert(data.message || 'Failed to update product quantity');
        }
    })
    .catch(error => console.error('Error updating product quantity:', error));
}

function decreaseQuantity(productId) {
    const quantityInput = document.getElementById(`quantity-${productId}`);
    let quantity = parseInt(quantityInput.value);
    
    if (quantity > 0) {
        // Decrease quantity on the frontend
        quantityInput.value = quantity - 1;

        // Update the `savedCart` object
        if (savedCart[productId]) {
            savedCart[productId].quantity -= 1;
            
            // If the quantity becomes zero, remove the item from the cart
            if (savedCart[productId].quantity === 0) {
                delete savedCart[productId];
            }
        }

        // Update cart total and display without requiring a page reload
        updateCartDisplay();

        // Call the API to update the quantity in the backend
        fetch('API/updateProductQuantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: -1 }) // Increase stock by 1 in the backend
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const newQuantity = data.new_quantity;

                // Update product quantity in the products object
                products[productId].quantity = newQuantity;

                // Update the stock count display
                const stockCountElement = document.querySelector(`#productModal-${productId} .stock-count`);
                if (stockCountElement) {
                    stockCountElement.textContent = newQuantity;
                }

                // Disable/enable the "Add to Cart" button based on new stock
                const addButton = document.querySelector(`#productModal-${productId} .quantity-add`);
                if (newQuantity === 0) {
                    addButton.disabled = true;
                    document.querySelector(`#productModal-${productId} .stock-info`).innerHTML = `Out of Stock <i class="fas fa-boxes"></i>`;
                } else {
                    addButton.disabled = false;
                    document.querySelector(`#productModal-${productId} .stock-info`).innerHTML = `Only ${newQuantity} Items Left <i class="fas fa-boxes"></i>`;
                }

                // Optionally save the cart to the backend
                saveCartToDatabase(savedCart);  // Save the updated cart to the server
            } else {
                alert(data.message || 'Failed to update product quantity');
            }
        })
        .catch(error => console.error('Error updating product quantity:', error));
    }
}

function addToCart(productId, productDetails) {
    const newCartItem = { 
        ...productDetails, 
        quantity: 1,  // Every new entry is treated as a single item
        orderTime: new Date().toISOString()  // Add a unique timestamp to distinguish each order
    };
    
    // Generate a unique key for each product entry, even if it's the same product
    const uniqueKey = productId + '_' + new Date().getTime();

    // Add the new entry into the cart using the unique key
    savedCart[uniqueKey] = newCartItem;

    // Update the cart display or quantity (optional)
    updateCartDisplay();
    updateCartBadge(); 
}

function updateCartTotal(productId, change) {
    const product = products[productId];
    const currentQuantity = parseInt(document.getElementById(`quantity-${productId}`).value);
    savedCart[productId] = currentQuantity; // Update savedCart with the new quantity

    // Send updated cart data to the server
    saveCartToDatabase(savedCart);

    // Recalculate cart total
    cartTotal = 0;
    for (const id in savedCart) {
        if (savedCart[id] > 0 && products[id]) {
            cartTotal += products[id].price * savedCart[id];
        }
    }

    // Update the total price display in the UI
    document.querySelector('.cart-total').textContent = `TOTAL: ₱${cartTotal}`;

    // Update the cart items display to reflect the new item in the cart
    updateCartDisplay();
}

function saveCartToDatabase(cart) {
    showLoading(); // Show loading indicator before sending request

    // Remove null entries from the cart
    const filteredCart = Object.fromEntries(
        Object.entries(cart).filter(([_, value]) => value !== null)
    );

    fetch('API/saveCart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ cart: filteredCart })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log('Cart saved successfully');
        } else {
            console.error('Failed to save cart:', data.message);
        }
    })
    .catch(error => {
        console.error('Error saving cart:', error);
    })
    .finally(() => {
        hideLoading(); // Hide loading indicator after request is complete
    });
}

function loadCartFromDatabase() {
    showLoading(); // Show loading indicator

    fetch('API/loadCart.php')
        .then(response => response.json())
        .then(data => {
            console.log("Cart data loaded:", data);
            
            savedCart = data.cart || {}; // Load the cart data or set it to an empty object
            updateCartDisplay(); // Update the cart display after loading the cart
            
        })
        .catch(error => {
            console.error('Error loading cart:', error);
        })
        .finally(() => {
            hideLoading(); // Hide the loading indicator once the cart is loaded
        });
}

// Call this function on page load to get the cart data from the server
window.onload = function() {
    loadCartFromDatabase();
}

let currentCartPage = 1; // Initialize the current page to 1
const cartItemsPerPage = 2; // Number of items per page in the cart

function updateCartDisplay() {
    const cartItemsBody = document.getElementById('cartItemsBody');
    cartItemsBody.innerHTML = ''; // Clear existing cart items

    // Filter cart items that have a quantity greater than 0
    const cartItems = Object.entries(savedCart).filter(([productId, details]) => details.quantity > 0);

    // Calculate pagination
    const startIndex = (currentCartPage - 1) * cartItemsPerPage;
    const endIndex = startIndex + cartItemsPerPage;

    // Slice the items based on pagination
    const paginatedItems = cartItems.slice(startIndex, endIndex);

    let cartTotal = 0; // Recalculate total for displayed items
    let overallCartTotal = 0; // Recalculate total for all items

    // Calculate the overall cart total (all items in savedCart)
    for (const [productId, details] of cartItems) {
        overallCartTotal += details.price * details.quantity;
    }

    // Calculate total for displayed items and update the display
    for (const [productId, details] of paginatedItems) {
        const { quantity, description, price, image } = details; // Get product details from savedCart

        // Create a new row for the cart item
        const cartItemRow = document.createElement('tr');
        cartItemRow.innerHTML = `
            <td>
                ${image ? `<img src="data:image/jpeg;base64,${image}" alt="${description}" class="cart-product-image">` : ''}
                ${description}
            </td>
            <td>${quantity}</td>
            <td>₱${price}</td>
            <td>
                <button class="remove-btn" onclick="removeFromCart(${productId})"><i class="fas fa-minus"></i></button>
            </td>
        `;
        cartItemsBody.appendChild(cartItemRow);

        // Accumulate the total price for the displayed items
        cartTotal += price * quantity;
    }

    // Update the total price display for displayed items
    document.querySelector('.cart-total').textContent = `Your cart Total in this Page: ₱${cartTotal}`;

    // Update the overall total price display
    document.querySelector('.overall-cart-total').textContent = `Overall Total: ₱${overallCartTotal}`;

    // Update pagination controls based on total items in the cart
    updateCartPaginationControls(cartItems.length);
}

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts(currentPage);
    updateCartBadge(); 
});

function removeFromCart(productId) {
    // Get current quantity or 0 if not present
    const currentQuantity = savedCart[productId].quantity || 0;

    if (currentQuantity > 1) {
        // Decrease the quantity by 1
        savedCart[productId].quantity--;
    } else {
        // If the quantity is 1, remove the item from the cart
        delete savedCart[productId];
    }

    // Update the cart display immediately to reflect changes
    updateCartDisplay();

    // Recalculate cart total
    cartTotal = 0;
    for (const id in savedCart) {
        if (savedCart[id].quantity > 0 && products[id]) {
            cartTotal += products[id].price * savedCart[id].quantity;
        }
    }
    document.querySelector('.cart-total').textContent = `TOTAL: ₱${cartTotal}`;

    // Send the request to the backend to decrement the item or remove if it's the last one
    fetch('API/removeCartItem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            customer_id: customerId,
            product_id: productId,
            quantity: -1  // Decrement the quantity by 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Error updating product quantity in cart.');
        } else {
            console.log('Item removed successfully from cart');
        }
    })
    .catch(error => console.error('Error removing item from cart:', error));

    // Optionally save the cart to the backend
    saveCartToDatabase(savedCart);  // Save the updated cart to the server

    updateCartBadge(); 
}

function placeOrder() {
    if (Object.keys(savedCart).length === 0) {
        showPlaceOrderModal('Your cart is empty!');
        return;
    }

    const filteredCart = Object.fromEntries(
        Object.entries(savedCart).filter(([key, value]) => value !== null)
    );

    if (Object.keys(filteredCart).length === 0) {
        showPlaceOrderModal('Your cart is empty!');
        return;
    }

    // Show confirmation modal before proceeding with the order
    showPlaceOrderModal('Are you sure you want to place the order?', true, filteredCart);
}

// Function to show the specific modal for placeOrder with optional confirmation buttons
function showPlaceOrderModal(message, showConfirmation = false, cart = null) {
    const modal = document.getElementById('placeOrderModal');
    const modalMessage = document.getElementById('placeOrderModalMessage');
    const modalButtons = document.getElementById('placeOrderModalButtons');
    const closeButton = document.querySelector('.place-order-modal-close');
    const cancelBtn = document.getElementById('placeOrderCancelBtn');
    const confirmBtn = document.getElementById('placeOrderConfirmBtn');

    modalMessage.textContent = message;
    modal.style.display = 'block';

    // Show confirmation buttons if needed
    if (showConfirmation) {
        modalButtons.style.display = 'block';
    } else {
        modalButtons.style.display = 'none';
    }

    // Handle cancel button click
    cancelBtn.onclick = function() {
        modal.style.display = 'none';
    };

    // Handle confirm button click
    confirmBtn.onclick = function() {
        modal.style.display = 'none';
        proceedWithOrder(cart);  // Pass the filtered cart to proceedWithOrder
    };

    // Close the modal when the "X" button is clicked
    closeButton.onclick = function() {
        modal.style.display = 'none';
    };

    // Close the modal when the user clicks outside the modal content
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// Function to proceed with the order after confirmation
function proceedWithOrder(filteredCart) {
    console.log('Placing order:', {
        customer_id: customerId,
        cart: filteredCart
    });

    showLoading();

    fetch('API/placeOrder.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            customer_id: customerId,
            cart: filteredCart
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPlaceOrderModal('Order placed successfully!');
            savedCart = {};
            updateCartDisplay();
            document.querySelector('.cart-total').textContent = 'TOTAL: ₱0';

            // Call updateCartBadge to refresh the badge after the order is placed
            updateCartBadge();
        } else {
            showPlaceOrderModal('Failed to place order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error placing order:', error);
        showPlaceOrderModal('Error placing order. Please try again.');
    })
    .finally(() => {
        hideLoading();
    });
}


function openCartModal() {
    console.log('Customer ID:', customerId);

    // Show the modal
    document.getElementById('cartModal').style.display = 'block';

    fetch(`API/getSavedCart.php?customer_id=${customerId}`)
    .then(response => response.json())
    .then(data => {
        console.log('Cart data:', data); // Log the entire data
        const cartBadge = document.getElementById('cartBadge');  // Get the cart badge element

        if (data.success) {
            const cartItemsContainer = document.getElementById('cartModalItems');
            cartItemsContainer.innerHTML = '';  // Clear previous content

            let totalPrice = 0;  // Variable to keep track of the total price
            const itemsPerPage = 5; // Set the number of items per page
            let currentPage = 1;

            const cartItems = data.cartItems; // Store the cart items
            const totalPages = Math.ceil(cartItems.length / itemsPerPage); // Calculate total pages

            // Update the cart badge with the number of items
            cartBadge.textContent = cartItems.length;
            cartBadge.style.display = cartItems.length > 0 ? 'inline-block' : 'none';  // Show or hide badge

            if (cartItems.length > 0) {
                // Add the "Select All" checkbox outside the table
                const selectAllDiv = document.createElement('div');
                selectAllDiv.className = 'select-all-container';
                selectAllDiv.innerHTML = `<label><input type="checkbox" id="selectAll" /> Select All</label>`;
                cartItemsContainer.appendChild(selectAllDiv);

                // Create a table
                const table = document.createElement('table');
                table.className = 'cart-table';
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Product ID</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Time Order</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody"></tbody>
                `;
                cartItemsContainer.appendChild(table);

                // Create pagination controls
                const paginationControls = document.createElement('div');
                paginationControls.className = 'pagination-controls';
                cartItemsContainer.appendChild(paginationControls);

                // Function to render the table rows
                function renderTable(page) {
                    const tableBody = document.getElementById('cartTableBody');
                    tableBody.innerHTML = ''; // Clear previous rows
                    const startIndex = (page - 1) * itemsPerPage;
                    const endIndex = Math.min(startIndex + itemsPerPage, cartItems.length);

                    for (let i = startIndex; i < endIndex; i++) {
                        const item = cartItems[i];

                        const price = parseFloat(item.price);
                        const quantity = parseInt(item.quantity);
                        totalPrice += price * quantity; // Add to total price

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><input type="checkbox" class="selectItem" data-product-id="${item.product_id}" /></td>
                            <td>${item.product_id}</td>
                            <td>${item.description}</td>
                            <td>${quantity}</td>
                            <td>₱${price.toFixed(2)}</td>
                            <td>${item.created_at}</td>
                            <td><img src="data:image/jpeg;base64,${item.image}" alt="Product Image" style="max-width: 100px;"></td>
                        `;
                        tableBody.appendChild(row);
                    }

                    renderPaginationControls(); // Update pagination
                }

                // Function to render pagination controls
                function renderPaginationControls() {
                    paginationControls.innerHTML = ''; // Clear existing controls

                    if (totalPages > 1) {
                        if (currentPage > 1) {
                            const prevButton = document.createElement('button');
                            prevButton.innerText = 'Previous';
                            prevButton.onclick = () => {
                                currentPage--;
                                renderTable(currentPage);
                            };
                            paginationControls.appendChild(prevButton);
                        }

                        for (let i = 1; i <= totalPages; i++) {
                            const pageButton = document.createElement('button');
                            pageButton.innerText = i;
                            pageButton.disabled = i === currentPage;
                            pageButton.onclick = () => {
                                currentPage = i;
                                renderTable(currentPage);
                            };
                            paginationControls.appendChild(pageButton);
                        }

                        if (currentPage < totalPages) {
                            const nextButton = document.createElement('button');
                            nextButton.innerText = 'Next';
                            nextButton.onclick = () => {
                                currentPage++;
                                renderTable(currentPage);
                            };
                            paginationControls.appendChild(nextButton);
                        }
                    }
                }

                // Render the first page of the table
                renderTable(currentPage);

                // Display the total price at the bottom of the modal
                const totalElement = document.createElement('div');
                totalElement.className = 'cart-total';
                totalElement.innerHTML = `<p><strong>Total Price:</strong> ₱${totalPrice.toFixed(2)}</p>`;
                cartItemsContainer.appendChild(totalElement);

                // Add a wrapper div for the buttons
                const buttonContainer = document.createElement('div');
                buttonContainer.className = 'button-container';  // Add a class for styling

                // Create checkout button
                const checkoutButton = document.createElement('button');
                checkoutButton.innerText = 'Checkout';
                checkoutButton.className = 'checkout-button';
                checkoutButton.onclick = () => openPickupModal();  // Pass selected items
                buttonContainer.appendChild(checkoutButton);

                // Create cancel order button
                const cancelOrderButton = document.createElement('button');
                cancelOrderButton.innerText = 'Cancel Orders';
                cancelOrderButton.className = 'cancel-order-button';
                cancelOrderButton.onclick = () => {
                    // Collect all selected product IDs
                    const selectedProductIds = [];
                    
                    // Iterate over each cart item and check if it is selected
                    cartItems.forEach(item => {
                        const checkbox = document.querySelector(`.selectItem[data-product-id="${item.product_id}"]`);
                        if (checkbox && checkbox.checked) {
                            selectedProductIds.push(item.product_id);
                        }
                    });

                    // Check if any items were selected for cancellation
                    if (selectedProductIds.length > 0) {
                        // Show the confirmation modal
                        const confirmationMessage = `Are you sure you want to cancel the following items: ${selectedProductIds.join(', ')}?`;
                        showCancelOrderConfirmationModal(confirmationMessage, selectedProductIds, customerId);
                    } else {
                        // Show no selection modal instead of alert
                        showNoSelectionModal();
                    }
                };
                buttonContainer.appendChild(cancelOrderButton);

                // Append the button container to the cart items container
                cartItemsContainer.appendChild(buttonContainer);

                // Add event listener for "Select All" checkbox
                document.getElementById('selectAll').addEventListener('change', function() {
                    const selectItems = document.querySelectorAll('.selectItem');
                    selectItems.forEach(item => item.checked = this.checked);
                });
            } else {
                cartItemsContainer.innerHTML = '<p>Your cart is empty.</p>';
                cartBadge.textContent = '0';  // Show "0" if the cart is empty
            }
        } else {
            alert(data.message);
        }
    });
}


function closeCartModal() {
    document.getElementById('cartModal').style.display = 'none';
}


function updateCartBadge() {
    fetch(`API/getSavedCart.php?customer_id=${customerId}`)
    .then(response => response.json())
    .then(data => {
        const cartBadge = document.getElementById('cartBadge');
        if (data.success && data.cartItems.length > 0) {
            cartBadge.textContent = data.cartItems.length;
            cartBadge.style.display = 'inline-block';
        } else {
            cartBadge.textContent = '0';
            cartBadge.style.display = 'none';  // Hide badge if no items
        }
    })
    .catch(error => {
        console.error('Error updating cart badge:', error);
    });
}

function showNoSelectionModal() {
    const modal = document.getElementById('noSelectionModal');
    const closeButton = document.querySelector('.no-selection-modal-close');
    const closeBtn = document.getElementById('noSelectionCloseBtn');

    modal.style.display = 'block';

    // Handle close button click (just close the modal)
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    // Close the modal when the "X" button is clicked
    closeButton.onclick = function() {
        modal.style.display = 'none';
    };

    // Close the modal when the user clicks outside the modal content
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

function cancelOrder(customerId) {
    // Collect selected items
    const selectedItems = [];
    document.querySelectorAll('.selectItem:checked').forEach(item => {
        selectedItems.push(item.getAttribute('data-product-id'));
    });

    // If no items are selected, show a message and don't open the modal
    if (selectedItems.length === 0) {
        alert('Please select at least one item to cancel.');
        return;
    }

    // Show confirmation modal before proceeding with the cancellation
    showCancelOrderModal('Are you sure you want to cancel the selected orders?', true, selectedItems, customerId);
}

function showCancelOrderConfirmationModal(message, productIds, customerId) {
    const modal = document.getElementById('cancelOrderModal');  // Assuming you have a cancel order modal
    const modalMessage = document.getElementById('cancelOrderModalMessage');
    const modalButtons = document.getElementById('cancelOrderModalButtons');
    const closeButton = document.querySelector('.cancel-order-modal-close');
    const cancelBtn = document.getElementById('cancelOrderCancelBtn');
    const confirmBtn = document.getElementById('cancelOrderConfirmBtn');

    modalMessage.textContent = message;
    modal.style.display = 'block';
    modalButtons.style.display = 'block';  // Show confirmation buttons

    // Handle cancel button click (just close the modal)
    cancelBtn.onclick = function() {
        modal.style.display = 'none';
    };

    // Handle confirm button click (proceed with order cancellation)
    confirmBtn.onclick = function() {
        modal.style.display = 'none';
        proceedWithCancelOrder(productIds, customerId);  // Pass the array of selected product IDs
    };

    // Close the modal when the "X" button is clicked
    closeButton.onclick = function() {
        modal.style.display = 'none';
    };

    // Close the modal when the user clicks outside the modal content
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// Function to show the specific modal for cancelOrder with confirmation
function showCancelOrderModal(message, selectedProductIds = [], customerId) {  // Default empty array
    const modal = document.getElementById('cancelOrderModal');
    const modalMessage = document.getElementById('cancelOrderModalMessage');
    const modalButtons = document.getElementById('cancelOrderModalButtons');
    const closeButton = document.querySelector('.cancel-order-modal-close');
    const cancelBtn = document.getElementById('cancelOrderCancelBtn');
    const confirmBtn = document.getElementById('cancelOrderConfirmBtn');

    // Set the modal message
    modalMessage.textContent = message;
    modal.style.display = 'block';

    // Show confirmation buttons
    modalButtons.style.display = 'block';

    // Handle cancel button click (close the modal)
    cancelBtn.onclick = function () {
        modal.style.display = 'none';
    };

    // Handle confirm button click (proceed with order cancellation)
    confirmBtn.onclick = function () {
        modal.style.display = 'none'; // Close modal
        if (selectedProductIds.length > 0) { // Check if selectedProductIds is valid
            proceedWithCancelOrder(selectedProductIds, customerId); // Execute cancellation
        }
    };

    // Close the modal when the "X" button is clicked
    closeButton.onclick = function () {
        modal.style.display = 'none';
    };

    // Close the modal when the user clicks outside the modal content
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// Proceed with cancellation of orders
function proceedWithCancelOrder(selectedProductIds, customerId) {
    // Ensure selectedProductIds is a valid array
    if (!Array.isArray(selectedProductIds) || selectedProductIds.length === 0) {
        console.error('No product IDs selected for cancellation.');
        return;
    }

    const productIds = selectedProductIds.join(','); // Convert array to comma-separated string

    fetch(`API/cancelOrder.php?customer_id=${customerId}&product_ids=${productIds}`, {
        method: 'GET',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCancelOrderModal('Orders cancelled successfully.'); // Show success modal without product IDs
            openCartModal(); // Refresh the cart modal to update the cart items
        } else {
            // Show error messages
            data.messages.forEach(message => {
                showCancelOrderModal(`Failed to cancel the order: ${message}`);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCancelOrderModal('There was an error processing your request.');
    });
}


// Function to round time to the nearest 25 minutes (global scope)
function roundToNext25Minutes(date) {
    const minutes = date.getMinutes();
    const remainder = minutes % 25;
    const minutesToAdd = remainder === 0 ? 0 : 25 - remainder;
    return new Date(date.getTime() + minutesToAdd * 60000);
}

// Function to format time as HH:MM (global scope)
function formatTime(date) {
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

// Place modal functions here
function openSpecificModal(modalId, messageId, message) {
        const modal = document.getElementById(modalId);
        const messageElem = document.getElementById(messageId);

        if (modal && messageElem) {
            messageElem.textContent = message;
            modal.style.display = 'block';
        } else {
            console.error('Modal or message element not found');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        } else {
            console.error('Modal element not found');
        }
    }

    function validatePickupTime() {
    const pickupDateInput = document.getElementById('pickupDate');
    const pickupTimeInput = document.getElementById('pickupTime');

    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    const selectedDate = pickupDateInput.value;
    const selectedTime = pickupTimeInput.value;

    // Enforce valid time in 25-minute intervals
    const selectedDateTime = new Date(`${selectedDate}T${selectedTime}`);
    const roundedTime = roundToNext25Minutes(selectedDateTime);
    const roundedTimeStr = formatTime(roundedTime);

    if (selectedTime !== roundedTimeStr) {
        openSpecificModal('pickupTimeValidationModal', 'pickupTimeValidationMessage', `Pickup time must be in 25-minute intervals. Setting to ${roundedTimeStr}.`);
        pickupTimeInput.value = roundedTimeStr;
    }

    if (selectedDate === todayStr && selectedTime > '19:00') {
        openSpecificModal('latePickupModal', 'latePickupModalMessage', 'Pick-up times after 7 PM are only available for tomorrow. Setting the date to tomorrow.');
        
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];

        // Set the date to tomorrow and default the time to 6:00 AM
        pickupDateInput.value = tomorrowStr;
        pickupTimeInput.value = '06:00';
        pickupTimeInput.setAttribute('min', '06:00');
        pickupTimeInput.setAttribute('max', '19:00');
    }
}

// Function to open the pickup modal and pass selected items
function openPickupModal() {
    // Collect selected items
    const selectedItems = [];
    document.querySelectorAll('.selectItem:checked').forEach(item => {
        selectedItems.push(item.getAttribute('data-product-id'));
    });

    // If no items are selected, show a message and don't open the modal
    if (selectedItems.length === 0) {
        alert('Please select at least one item to checkout.');
        return;
    }

    // Open the modal
    document.getElementById('pickupModal').style.display = 'block';

    const today = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(today.getDate() + 1);

    const todayStr = today.toISOString().split('T')[0];
    const tomorrowStr = tomorrow.toISOString().split('T')[0];

    const pickupDateInput = document.getElementById('pickupDate');
    const pickupTimeInput = document.getElementById('pickupTime');

    // Function to add 25 minutes to the current time
    function addMinutesToTime(date, minutes) {
        return new Date(date.getTime() + minutes * 60000);
    }

    // Function to update the pickup time restrictions based on the selected date
    function updatePickupTimeRestrictions() {
        const selectedDate = pickupDateInput.value;
        if (selectedDate === todayStr) {
            let minPickupTime = addMinutesToTime(today, 25);
            minPickupTime = roundToNext25Minutes(minPickupTime);
            pickupTimeInput.setAttribute('min', formatTime(minPickupTime));
            pickupTimeInput.setAttribute('max', '19:00');  // Restrict today's pickup time to 7 PM max
            pickupTimeInput.value = formatTime(minPickupTime);
        } else if (selectedDate === tomorrowStr) {
            pickupTimeInput.setAttribute('min', '06:00');
            pickupTimeInput.setAttribute('max', '19:00');
            pickupTimeInput.value = '06:00';
        }
    }

    // Set min and max values for the pickup date
    pickupDateInput.setAttribute('min', todayStr);
    pickupDateInput.setAttribute('max', tomorrowStr);
    pickupDateInput.value = todayStr;

    // Update time restrictions
    updatePickupTimeRestrictions();

    // Listen to changes
    pickupDateInput.addEventListener('change', updatePickupTimeRestrictions);
    pickupTimeInput.addEventListener('change', validatePickupTime);

    // Handle form submission with selected items
    document.getElementById('pickupForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const pickupDate = pickupDateInput.value;
        const pickupTime = pickupTimeInput.value;
        const otpCode = document.getElementById('otpCode').value;

        // Validate time intervals again before submitting
        validatePickupTime();

        // Send selected items to the checkout API
        fetch('API/checkOut.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: customerId, // Assuming customerId is defined globally
                pickupDate,
                pickupTime,
                otpCode,
                selectedItems  // Pass the selected product IDs
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                openSpecificModal('pickupGeneralModal', 'pickupGeneralModalMessage', 'Pickup confirmed!');
                closePickupModal();
            } else {
                openSpecificModal('pickupGeneralModal', 'pickupGeneralModalMessage', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            openSpecificModal('pickupGeneralModal', 'pickupGeneralModalMessage', 'Error: Something went wrong.');
        });
    });
}

// Function to close the pickup modal
function closePickupModal() {
    document.getElementById('pickupModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {

// Function to format time to 12-hour format with AM/PM
function formatTimeTo12Hour(timeString) {
    const [hour, minute] = timeString.split(':');
    const hours = parseInt(hour, 10);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHour = hours % 12 || 12; // Convert to 12-hour format
    return `${formattedHour}:${minute} ${ampm}`;
}

function openOrdersModal() {
    const modal = document.getElementById("ordersModal");
    modal.style.display = "block";
}
function closeOrdersModal() {
    const modal = document.getElementById("ordersModal");
    modal.style.display = "none";
}

window.closeOrdersModal = function() {
    closeOrdersModal();
}

// Attach modal close event to window click (if user clicks outside the modal)
window.onclick = function(event) {
    const ordersModal = document.getElementById("ordersModal");
    if (event.target == ordersModal) {
        ordersModal.style.display = "none";
    }
}
});

// Function to load customer profile details
function loadProfileImageAndUsername() {
    fetch('API/getAccountInformation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error(data.error);
        } else {
            let profileImageSrc = 'default_profile.png';
            if (data.profile_image) {
                profileImageSrc = 'data:image/jpeg;base64,' + data.profile_image;
            }

            document.getElementById('profile-image').src = profileImageSrc;
            document.getElementById('profile-username').textContent = data.username;
        }
    })
    .catch(error => {
        console.error('Error fetching profile details:', error);
    });
}
// Load profile image and username on page load
document.addEventListener('DOMContentLoaded', loadProfileImageAndUsername);


function updatePaginationControls(totalProducts) {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = ''; // Clear existing pagination buttons

    const totalPages = Math.ceil(totalProducts / productsPerPage);
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.classList.add('pagination-button');
        if (i === currentPage) {
            pageButton.classList.add('active');
        }
        pageButton.onclick = () => {
            currentPage = i;
            fetchProducts(currentPage);
        };
        paginationContainer.appendChild(pageButton);
    }
}

function updateCartPaginationControls(totalCartItems) {
    const paginationContainer = document.getElementById('cartPagination');
    paginationContainer.innerHTML = ''; // Clear existing pagination buttons

    const totalPages = Math.ceil(totalCartItems / cartItemsPerPage);
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = i;
        pageButton.classList.add('cart-pagination-button');
        if (i === currentCartPage) {
            pageButton.classList.add('active');
        }
        pageButton.onclick = () => {
            currentCartPage = i;
            updateCartDisplay();
        };
        paginationContainer.appendChild(pageButton);
    }
}

function openProductModal(productId) {
    const modal = document.getElementById(`productModal-${productId}`);
    modal.style.display = "block";
}

function closeProductModal(productId) {
    // Close the modal
    const modal = document.getElementById(`productModal-${productId}`);
    if (modal) {
        modal.style.display = 'none';
    }

    // Reset the quantity input field to 0 when the modal is closed
    const quantityInput = document.getElementById(`quantity-${productId}`);
    if (quantityInput) {
        quantityInput.value = 0; // Reset quantity to 0
    }
}

function openCancelModal() {
        const modal = document.getElementById("cancelModal");
        modal.style.display = "block";
    }

function closeCancelModal() {
        const modal = document.getElementById("cancelModal");
        modal.style.display = "none";
    }
function showModal(message) {
        const modal = document.getElementById("myModal");
        const span = document.getElementsByClassName("close")[0];
        document.getElementById("modalMessage").textContent = message;
        modal.style.display = "block";
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }

function openLogoutModal() {
        const modal = document.getElementById("logoutModal");
        modal.style.display = "block";
    }

function closeLogoutModal() {
        const modal = document.getElementById("logoutModal");
        modal.style.display = "none";
    }

function confirmLogout() {
        window.location.href = 'logout.php';
    }
    function showOutOfStockModal() {
    const modal = document.getElementById('outOfStockModal');
    modal.style.display = 'block';
}

function closeOutOfStockModal() {
    const modal = document.getElementById('outOfStockModal');
    modal.style.display = 'none';
}

// Optional: Close the modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('outOfStockModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};

function showLoading() {
    document.getElementById('loadingIndicator').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingIndicator').style.display = 'none';
}
function confirmPickupTime() {
    console.log("Pickup time confirmed.");
    closeModal('pickupTimeValidationModal');
}

function confirmLatePickup() {
    console.log("Late pickup confirmed.");
    closeModal('latePickupModal');
}

function confirmGeneralPickup() {
    console.log("General pickup confirmed.");
    closeModal('pickupGeneralModal');
}


</script>
</body>
</html>
