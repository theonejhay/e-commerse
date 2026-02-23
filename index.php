<?php
session_start();

// Check if the user is logged in by checking the session
$loggedIn = isset($_SESSION['customer_id']);

// Retrieve customer ID if logged in
if ($loggedIn) {
    $customer_id = $_SESSION['customer_id'];
}
?>
<script>
    var customerId = <?php echo isset($customer_id) ? json_encode($customer_id) : 'null'; ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/index.css">
    <title>Index</title>
</head>
<body>
<div class="hero-container container-fluid">
    <header class="hero-header d-flex justify-content-between align-items-center py-3">

        <!-- Profile Image and Username -->
        <div class="profile-container">
            <a class="nav-link" href="account.php">
                <img id="profile-image" src="default_profile.png" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 50%;">
            </a>
        </div>


        <!-- Regular Nav Links (Visible on all screen sizes, displayed horizontally) -->
        <nav class="nav-links d-flex flex-row">
            <?php if ($loggedIn): ?>
                <a href="#" class="nav-item nav-link" onclick="openCartModal()">
                    <i class="fas fa-shopping-cart">
                    <span id="cartBadge" class="badge bg-danger"></span>
                    
                    </i>
                </a>
                <a class="nav-item nav-link" href="#" onclick="openOrdersModal()">
                    <i class="fas fa-box">
                    
                    </i>
                </a>
                <a href="#" onclick="openLogoutModal()" class="nav-item nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span id="OrderBadge" class="badge bg-danger"></span>
                </a>
            <?php else: ?>
                <!-- Display Cart, Orders, and Sign In icons when not logged in -->
                <a href="#" class="nav-item nav-link" onclick="openCartModal()">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cartBadge" class="badge bg-danger"></span> <!-- Cart Icon -->
                </a>
                <a class="nav-item nav-link" href="#" onclick="openOrdersModal()">
                    <i class="fas fa-box"></i> <!-- Orders Icon -->
                </a>
                <a class="nav-item nav-link" href="admin_login.php">
                    <i class="fas fa-user"></i> <!-- Sign In Icon -->
                </a>
            <?php endif; ?>
        </nav>
    </header>

    <?php if ($loggedIn): ?>
<div id="orderReminder" class="reminder-section minimized">
    <p id="reminderTitle" class="reminder-title">🔔 Reminder! Click to view details</p>
    <div id="reminderDetails" class="reminder-details" style="display: none;">
        <!-- Reminder content will be dynamically inserted here -->
    </div>
</div>
<?php endif; ?>


<!-- Navigation Modal -->
<div id="navModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeNavModal()">&times;</span>
        <nav class="nav-links flex-column">
            <?php if ($loggedIn): ?>
                <a href="#" class="nav-item nav-link" onclick="openCartModal()">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
                <a class="nav-item nav-link" href="#" onclick="openOrdersModal()">
                    <i class="fas fa-box">
                        <span id="cartBadge" class="badge bg-danger"></span>
                    </i> My Orders
                </a>
                <a href="#" onclick="openLogoutModal()" class="nav-item nav-link">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            <?php else: ?>
                <!-- Display Cart, Orders, and Sign In options in modal when not logged in -->
                <a href="#" class="nav-item nav-link" onclick="openCartModal()">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
                <a class="nav-item nav-link" href="#" onclick="openOrdersModal()">
                    <i class="fas fa-box"></i> My Orders
                </a>
                <a class="nav-item nav-link" href="admin_login.php">
                    <i class="fas fa-user"></i> Sign In
                </a>
            <?php endif; ?>
        </nav>
    </div>
</div>


    <!-- Promo Section -->
    <div class="promo-section row align-items-center">
        <!-- Promo Text -->
        <div class="promo-image col-md-6 text-center">
            <img src="images/chicken.jpg" alt="Fried Chicken" class="img-fluid"> <!-- Replace with your image path -->
        
        <div class="promo-text col-md-6 text-center text-md-left">
            <h1 class="display-4">Maigis</h1>
            <h2 class="h4">FRIED CHICKEN</h2>
            <p>Crispy Outside, Tender and Savory Inside.</p>
            <div class="promo-buttons mt-4">
                <button class="buy-now" onclick="scrollToSection('main-product-container')">Buy Now</button>
                <button class="learn-more" onclick="scrollToSection('footer')">About Us</button>
            </div>
        </div>
        </div>
        
    </div>
</div>
            
<!-- Search Bar Below Navbar -->
<div class="search-bar-container">
  <div class="container">
    <form id="searchForm" class="d-flex" role="search" onsubmit="event.preventDefault();">
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-search"></i> <!-- Icon for search input -->
        </span>
        <input class="form-control me-2" type="search" placeholder="Search product here" aria-label="Search" name="query" id="searchQuery">
      </div>
    </form>
  </div>
</div>

<div id="main-product-container">
    <div class="icon-menu">
    </div>
</div>
</div>


        <div id="products" class="products-list">
            <!-- Products will be dynamically added here -->
        </div>
        <div id="pagination" class="pagination-controls"></div>
    </div>
    
    <div id="cartModal" class="modal">
    <div class="modal-content cart-modal-content">
        <div class="cart">
            <h2><i class="fas fa-shopping-cart"></i> Cart Items</h2>
            <table class="cart-items">
                <thead>
                    <tr>
                        <th></th> <!-- For select/check -->
                        <th>Product</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cartItemsBody">
                    <!-- Cart items and select-all checkbox will be dynamically added here -->
                </tbody>
            </table>
            <p class="cart-total">
                <i class="fas fa-calculator"></i> TOTAL: ₱<span id="totalPrice">0</span>
            </p>
            <button class="cart-addToCart" onclick="openCheckoutModal()">
                Checkout
            </button>
            <button class="cart-close" onclick="closeCartModal()">
                Close
            </button>
        </div>
    </div>
</div>

<div id="checkoutModal" class="modal">
    <div class="modal-content checkout-modal-content">
        <h2><i class="fas fa-calendar-alt"></i> Checkout</h2>
        <div id="selectedProductsContainer"></div>
        <p id="totalAmountDisplay"></p>

        <!-- Reminder box -->
        <div class="reminder-box">
            <p><i class="fas fa-info-circle"></i> Please select a pick-up time between 6 AM and 7 PM. The pick-up time must be at least 25 minutes from now.</p>
        </div>

        <form id="checkoutForm">
            <div class="input-group">
                <label for="pickupDate">
                    <i class="fas fa-calendar-day"></i> Pick-up Date:
                </label>
                <select id="pickupDate" name="pickupDate" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>

            <div class="input-group">
                <label for="pickupTime">
                    <i class="fas fa-clock"></i> Pick-up Time:
                </label>
                <select id="pickupTime" name="pickupTime" required>
                    <!-- Options will be populated dynamically -->
                </select>
            </div>

            <button type="submit" class="checkout-confirm">
                Place Order
            </button>
            <button type="button" class="checkout-close" onclick="closeCheckoutModal()">
                Cancel
            </button>
        </form>
    </div>
</div>

<div id="buynowModal" class="modal">
    <div class="modal-content checkout-modal-content">
        <h2><i class="fas fa-calendar-alt"></i> Checkout</h2>
        <div id="buyNowContainer"></div>
        <p id="totalAmountDisplay"></p>

        <!-- Reminder box -->
        <div class="buynow-reminder-box">
            <p><i class="fas fa-info-circle"></i> Please select a pick-up time between 6 AM and 7 PM. The pick-up time must be at least 25 minutes from now.</p>
        </div>

        <form id="buynowcheckoutForm">

        <div class="buynow-input-group">
                <label for="pickupTime">
                    <i class="fas fa-clock"></i> Pick-up Time:
                </label>
            <select  id="buynowpickupTime" name="pickupTime" required>
            </select>

        <div class="buynow-input-group">
                <label for="pickupDate">
                    <i class="fas fa-calendar-day"></i> Pick-up Date:
                </label>
            <select id="buynowpickupDate" name="pickupDate" required>
            </select>
            
            <button type="submit" class="checkout-confirm">
                Place Order
            </button>
            <button type="button" class="checkout-close" onclick="closeBuyNowCheckoutModal()">
                Cancel
            </button>
        </form>
    </div>
</div>
</div>
</div>

<!-- Orders Modal -->
<div id="ordersModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeOrdersModal()">&times;</span>
        <h2>Your Orders</h2>
        <div id="ordersContainer">
            <table id="ordersTable">
                <thead>
                    <tr>
                        <th>Product Description</th>
                        <th>Total Amount</th>
                        <th>Addons</th>
                        <th>Addons Total</th>
                        <th>Final Total</th>
                        <th>Pickup Date</th>
                        <th>Pickup Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Order rows will be populated here dynamically -->
                </tbody>
            </table>
        </div>
         <!-- Button to open account.php -->
         <div class="modal-footer">
            <button onclick="window.location.href='account.php'" class="account-btn">View Cancelled Orders</button>
        </div>
    </div>
</div>

<!-- Login Prompt Modal -->
<div id="loginPromptModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p>Please log in to perform this action.</p>
        <div class="modal-buttons">
            <button onclick="redirectToLogin()" class="login-btn">Log In</button>
            <button onclick="closeLoginPromptModal()" class="cancel-btn">Cancel</button>
        </div>
    </div>
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal logout-modal">
    <div class="modal-content logout-modal-content">
        <h2>Logout Confirmation</h2>
        <p>Are you sure you want to log out?</p>
        <div class="modal-buttons">
            <button class="logout-btn" onclick="confirmLogout()">Yes</button>
            <button class="cancel-btn" onclick="closeLogoutModal()">No</button>
        </div>
    </div>
</div>

<div id="cancelModal" class="modal">
    <div class="modal-content">
        <p id="modal-message"></p>

        <form id="cancelReasonForm">
            <label>
                <input type="radio" name="cancelReason" value="Mistaken Selection" required>
                Mistaken Selection
            </label><br>
            <label>
                <input type="radio" name="cancelReason" value="Change of Mind">
                Change of Mind
            </label><br>
            <label>
                <input type="radio" name="cancelReason" value="Other" id="otherReasonRadio">
                Other
            </label><br>
            <textarea id="otherReasonInput" name="customReason" placeholder="Please specify your reason..." style="display: none; width: 100%; margin-top: 10px;"></textarea>
        </form>

        <div id="modal-buttons">
            <button id="confirm-button">Cancel</button>
            <button id="cancel-button">Cancel</button>
            <button id="close-button">Close</button>
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

</div>
</div>
</div>
    <footer id="footer">
    <div class="footer-content">
        <div class="promo-text">
        <p class="special-text" style="color: whitesmoke; font-weight: bold; font-size: 24px;">Hot & Special</p>
        <h2 class="footer-title" style="color: whitesmoke; font-weight: bold; font-size: 36px;">MAIGIS FRIED CHICKEN</h2>
            <p class="footer-description">Crispy Outside, Tender and Savory Inside.</p>
            <p class="footer-description">Location:</p>
            <p class="footer-description">Purok 4, Labuyo, Tangub City, Misamis Occidental</p>
            <p class="footer-description">Contact us at: 09175011792</p>
        </div>
        <div class="footer-cta">
            <img src="images/homepagebg.png" alt="image">
            <div class="social-icons">
                <a href="https://www.facebook.com/search/top?q=maigis%20labuyo%20branch"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
    let cartTotal = 0;
    const products = {};  // To store product details
    let savedCart = JSON.parse(localStorage.getItem(`cart_${customerId}`)) || {};
    const savedCartTotal = localStorage.getItem(`cartTotal_${customerId}`) || 0;

    let currentPage = 1;
    const productsPerPage = 3; 
    let currentCategory = ''; // Declare globally
 // You can change this to any number

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


function createProductCard(product) {
    // Check the product's status based on its available quantity or explicitly set status
    const isQuantityAvailable = product.quantity > 0; // Check quantity
                const isStatusAvailable = product.status === 'available'; // Check if status is explicitly set to 'available'
                
                // If quantity is 0 or status is 'not available', display "Not Available"
                if (!isQuantityAvailable || !isStatusAvailable) {
                    product.status = 'not available';
                } else {
                    product.status = 'available'; // Otherwise, set status to 'available'
                }
                
                // Format price to two decimal places
                const formattedPrice = product.price.toFixed(2);

                // Generate star rating
                const averageRating = product.average_rating || 0;
                const fullStars = Math.floor(averageRating);
                const halfStar = averageRating % 1 >= 0.5 ? 1 : 0;
                const emptyStars = 5 - fullStars - halfStar;

                let starsHTML = '';
                for (let i = 0; i < fullStars; i++) {
                    starsHTML += '<span class="star full">&#9733;</span>';
                }
                if (halfStar) {
                    starsHTML += '<span class="star half">&#9733;</span>';
                }
                for (let i = 0; i < emptyStars; i++) {
                    starsHTML += '<span class="star empty">&#9733;</span>';
                }

                // Create product card
                const productCard = document.createElement('div');
                productCard.classList.add('product-card');
                productCard.innerHTML = `
                <div class="product-container">
                    <div class="product-image-container">
                        ${product.image ? `<img class="product-image" src="data:image/jpeg;base64,${product.image}" alt="${product.description}" onclick="openProductModal(${product.product_id})">` : ''}
                    </div>
                    <div class="product-details">
                        <div class="product-header">
                            <h1 class="product-card-product-description">${product.description}</h1>
                        </div>
                        <div class="product-rating">${starsHTML}</div>
                        <div class="product-footer">
                            <div class="product-price">₱ ${formattedPrice}</div>
                            <button class="orderbtn" onclick="openProductModal(${product.product_id})" ${product.status === 'not available' ? 'disabled' : ''}>
                                <i class="fas fa-shopping-cart"></i> Order Now
                            </button>
                            <p class="status-indicator ${product.status === 'available' ? 'available' : 'not-available'}">
                                ${product.status === 'available' ? 'Available' : 'Not Available'}
                            </p>
                        </div>
                    </div>
                </div>
                `;
    return productCard;
}

function createProductModal(product) {
    const formattedPrice = product.price.toFixed(2);

                // Create modal for the product and append to the body or a separate modal container
                const productModal = document.createElement('div');
                productModal.id = `productModal-${product.product_id}`;
                productModal.classList.add('modal', 'product-modal');
                productModal.innerHTML = `
                <div class="modal-content custom-product-modal">
                    <div class="product-info">
                        <p class="product-description">${product.description}</p>
                    </div>
                    <div class="modal-body">
                        <div class="product-image">
                            ${product.image ? `<img src="data:image/jpeg;base64,${product.image}" alt="${product.description}" class="modal-product-image" onclick="openFullImageModal('data:image/jpeg;base64,${product.image}')">` : ''}

                        </div>
                        <div class="product-info">
                            <div class="product-pricing">
                                <p><strong>₱ ${formattedPrice}</strong></p>
                            </div>
                            <div class="product-info">
                                <p class="product-description">
                                    <span class="stock-count">Available items: ${product.quantity}</span>
                                </p>
                            </div>
                            <!-- Quantity selection with clickable buttons -->
                            <div class="quantity-selection">
                                <p>Select Quantity:</p>
                                <div class="quantity-options">
                                    <button class="quantity-option" onclick="requireLogin(() => setQuantity(${product.product_id}, 1))">x1</button>
                                    <button class="quantity-option" onclick="requireLogin(() => setQuantity(${product.product_id}, 2))">x2</button>
                                    <button class="quantity-option" onclick="requireLogin(() => setQuantity(${product.product_id}, 5))">x5</button>
                                    <button class="quantity-option" onclick="requireLogin(() => setQuantity(${product.product_id}, 10))">x10</button>
                                    <button class="quantity-option" onclick="requireLogin(() => setQuantity(${product.product_id}, 15))">x15</button>
                                    <button class="quantity-option" onclick="requireLogin(() => setQuantity(${product.product_id}, 20))">x20</button>
                                </div>
                            </div>
                            <!-- Quantity controls with buttons aligned in a row -->
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="requireLogin(() => decreaseQuantity(${product.product_id}))">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input 
    type="text" 
    id="quantity-${product.product_id}" 
    value="${savedCart[product.product_id]?.quantity || 0}" 
    class="quantity-input" 
    oninput="validateQuantity(${product.product_id}, this)" 
    onchange="updateCartQuantity(${product.product_id}, this.value); saveCartToDatabase(savedCart)">

                                <button class="quantity-add" onclick="requireLogin(() => increaseQuantity(${product.product_id}))">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            </div>
                            </div>

                           <!-- Add-ons Section -->
<div class="add-ons">
    <h4>Add-ons</h4>
    <div class="addon-options">
        <label>
            <input type="checkbox" class="sauce-addon-${product.product_id}" value="10" data-name="Gravy" onchange="toggleAddon(${product.product_id}, 'Gravy', 10, 'sauce')" disabled>
            Gravy (+₱10)
            <select id="addon-quantity-Gravy-${product.product_id}" onchange="updateAddonQuantity(${product.product_id}, 'Gravy', 10, this)" disabled>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
            </select>
        </label>
        <label>
            <input type="checkbox" class="sauce-addon-${product.product_id}" value="35" data-name="Toyo" onchange="toggleAddon(${product.product_id}, 'Toyo', 10, 'sauce')" disabled>
            Toyo (+₱10)
            <select id="addon-quantity-Toyo-${product.product_id}" onchange="updateAddonQuantity(${product.product_id}, 'Toyo', 10, this)" disabled>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
            </select>
        </label>
        <label>
            <input type="checkbox" class="rice-addon-${product.product_id}" value="15" data-name="Rice" onchange="toggleAddon(${product.product_id}, 'Rice', 15, 'rice')" disabled>
            Rice (+₱15)
            <select id="addon-quantity-Rice-${product.product_id}" onchange="updateAddonQuantity(${product.product_id}, 'Rice', 15, this)" disabled>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
            </select>
        </label>
    </div>
</div>


                            <!-- Separated Add to Cart and Buy Now buttons -->
                            <div class="action-controls">
                            <p>₱ <span id="total-${product.product_id}">0</span></p>
                                <button class="action-btn" onclick="requireLogin(() => showSuccessModal(${product.product_id}))">Add To Cart</button>
                                <button class="action-btn" onclick="requireLogin(() => openBuyNowCheckoutModal(${product.product_id}))">Buy Now</button>
                            </div>
                            <div class="action-controls">
                                <button class="cancel-btn" onclick="clearSavedCart(${product.product_id})">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                `;
    return productModal;
}

let inputValues = {}; // Global object to store input values

function saveInputValues() {
    inputValues = {}; // Reset the object
    Object.keys(savedCart).forEach(productId => {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        if (quantityInput) {
            inputValues[productId] = parseInt(quantityInput.value, 10) || 0; // Save current input value
        }
    });
}

function restoreInputValues() {
    Object.keys(inputValues).forEach(productId => {
        const quantityInput = document.getElementById(`quantity-${productId}`);
        if (quantityInput) {
            quantityInput.value = inputValues[productId] || 0; // Restore saved value
        }
    });
}

function fetchProducts(page = 1, query = '', category = '') {
    saveInputValues(); // Save current input values

    const selectedCategory = category || currentCategory || '';
    const searchQuery = query || '';

    fetch('API/searchProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            description: searchQuery,
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

                    const productCard = createProductCard(product);
                    const productModal = createProductModal(product);

                    productsContainer.appendChild(productCard);
                    document.body.appendChild(productModal);
                });

                restoreInputValues(); // Restore input values after refreshing products
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

setInterval(() => {
    fetchProducts(); // Auto-refresh products
}, 5000); // Refresh every 60 seconds



function enableAddons(productId, isEnabled) {
    document.querySelectorAll(`.addon-options input[class*="${productId}"]`).forEach(checkbox => {
        checkbox.disabled = !isEnabled;
    });
    document.querySelectorAll(`.addon-options select[id^="addon-quantity-"]`).forEach(select => {
        select.disabled = !isEnabled;
    });
}

function updateCartQuantity(productId, newQuantity) {
    // Convert newQuantity to an integer
    const quantity = parseInt(newQuantity, 10);

    // Validate the quantity (ensure it's a positive integer)
    if (isNaN(quantity) || quantity <= 0) {
        alert("Please enter a valid quantity.");
        document.getElementById(`quantity-${productId}`).value = savedCart[productId]?.quantity || 1;
        return;
    }

    // Update the savedCart object
    if (!savedCart[productId]) {
        savedCart[productId] = { product_id: productId, quantity: 0 }; // Initialize if not present
    }
    savedCart[productId].quantity = quantity;

    // Update the total price dynamically
    updateTotalPrice(productId);
}

function updateTotalPrice(productId) {
    const quantity = savedCart[productId]?.quantity || 0;
    const productPrice = products[productId]?.price || 0;
    const total = quantity * productPrice;
    document.getElementById(`total-${productId}`).textContent = total.toFixed(2);
}

function validateQuantity(productId, inputElement) {
    // Get the entered value
    let quantity = inputElement.value;

    // Check if the value is a valid positive integer
    if (!Number.isInteger(parseFloat(quantity)) || quantity <= 0) {
        // If not, reset to 1 (or any valid default quantity)
        inputElement.value = 0;
    }

    // Update the total price dynamically based on the new quantity (optional)
    updateTotalPrice(productId);
}

function toggleAddon(productId, addonName, addonPrice, addonType) {
    const addonCheckbox = document.querySelector(`.addon-options input[data-name="${addonName}"][class*="${productId}"]`);
    const totalElement = document.getElementById(`total-${productId}`);
    let currentTotal = parseFloat(totalElement.textContent) || 0;

    // Retrieve existing add-ons from local storage or initialize an empty object
    let storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};

    if (!storedAddons[productId]) {
        storedAddons[productId] = {}; // Initialize the object for this product
    }

    // Get the dropdown element
    const dropdown = document.getElementById(`addon-quantity-${addonName.replace(" ", "")}-${productId}`);

    // Add or remove the add-on based on the checkbox state
    if (addonCheckbox.checked) {
        storedAddons[productId][addonName] = {
            name: addonName,
            price: addonPrice,
            quantity: 1, // Default quantity to 1 when initially added
            type: addonType
        };
        currentTotal += addonPrice;

        // Enable the dropdown and set quantity to 1, remove "0" option
        dropdown.value = "1";
        dropdown.disabled = false;
        removeOption(dropdown, "0");
    } else {
        if (storedAddons[productId][addonName]) {
            currentTotal -= storedAddons[productId][addonName].price * storedAddons[productId][addonName].quantity;
            delete storedAddons[productId][addonName];
        }

        // Reset dropdown to 0, disable it, and add "0" option back
        addOption(dropdown, "0", "0");
        dropdown.value = "0";
        dropdown.disabled = true;
    }

    totalElement.textContent = currentTotal.toFixed(2);

    // Save updated add-ons to local storage
    localStorage.setItem('selectedAddons', JSON.stringify(storedAddons));
}


// Helper function to remove an option by value
function removeOption(dropdown, value) {
    const option = dropdown.querySelector(`option[value="${value}"]`);
    if (option) {
        option.remove();
    }
}

// Helper function to add an option to the dropdown
function addOption(dropdown, value, text) {
    if (!dropdown.querySelector(`option[value="${value}"]`)) {
        const option = document.createElement("option");
        option.value = value;
        option.text = text;
        dropdown.prepend(option);  // Adds "0" option at the beginning
    }
}


function updateAddonQuantity(productId, addonName, addonPrice, selectElement) {
    const selectedQuantity = parseInt(selectElement.value);
    const totalElement = document.getElementById(`total-${productId}`);
    let currentTotal = parseFloat(totalElement.textContent) || 0;

    // Retrieve existing add-ons from local storage
    let storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    if (!storedAddons[productId]) {
        storedAddons[productId] = {}; // Initialize the object for this product
    }

    // Check if the add-on exists and update quantity only if it does
    const addon = storedAddons[productId][addonName];
    if (addon) {
        // Adjust total by removing the old quantity price
        currentTotal -= addon.price * addon.quantity;

        // Update the quantity of the add-on in the storedAddons
        storedAddons[productId][addonName].quantity = selectedQuantity;

        // Add the new price based on the updated quantity
        currentTotal += addonPrice * selectedQuantity;

        // Update the display total
        totalElement.textContent = currentTotal.toFixed(2);

        // Save updated add-ons to local storage
        localStorage.setItem('selectedAddons', JSON.stringify(storedAddons));

        // Log the updated add-ons to the console
        console.log('Updated Add-ons for Product ' + productId + ':', storedAddons[productId]);
    } else {
        console.warn(`Add-on "${addonName}" is not selected; cannot update quantity.`);
    }
}


function setQuantity(productId, quantity) {
    const product = products[productId];

    // Check if requested quantity is within available stock
    if (quantity > product.quantity) {
        alert(`Only ${product.quantity} items are available in stock.`);
        return;
    }

    // Enable the addon checkboxes and dropdowns once quantity is set
    const addonCheckboxes = document.querySelectorAll(`.addon-options input[class*="${productId}"]`);
    const addonDropdowns = document.querySelectorAll(`#productModal-${productId} .addon-options select`);

    addonCheckboxes.forEach(checkbox => checkbox.disabled = false);
    addonDropdowns.forEach(dropdown => dropdown.disabled = false);

    // Set quantity and total price display
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const totalElement = document.getElementById(`total-${productId}`);

    quantityInput.value = quantity;
    let totalAmount = quantity * product.price;  // Total based on product quantity

    // Retrieve add-ons from local storage and recalculate the total
    let storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    if (storedAddons[productId]) {
        Object.values(storedAddons[productId]).forEach(addon => {
            // Add-ons should be added based on their own quantity, not the product quantity
            totalAmount += addon.price * addon.quantity;
        });
    }

    totalElement.textContent = totalAmount.toFixed(2);

    // Update `savedCart` with the new quantity
    savedCart[productId] = {
        quantity: quantity,
        description: product.description,
        price: product.price,
        image: product.image
    };

    // Save updated cart data to the backend immediately
    saveCartToDatabase(savedCart);

    // Optionally update cart total display if required
    updateCartTotal(productId, quantity);
}


function increaseQuantity(productId) {
    const product = products[productId];
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const totalElement = document.getElementById(`total-${productId}`);

    let currentQuantity = parseInt(quantityInput.value, 10);
    let newQuantity = currentQuantity + 1;

    // Check if new quantity exceeds available stock
    if (newQuantity > product.quantity) {
        alert(`Only ${product.quantity} items are available in stock.`);
        return;
    }

    // Update quantity input field
    quantityInput.value = newQuantity;

    // Calculate the total price based on product quantity
    let totalAmount = newQuantity * product.price;

    // Retrieve add-ons from local storage and recalculate total
    let storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    if (storedAddons[productId]) {
        Object.values(storedAddons[productId]).forEach(addon => {
            // Add-on prices should be based on their quantity, not product quantity
            totalAmount += addon.price * addon.quantity;
        });
    }

    // Update total display
    totalElement.textContent = totalAmount.toFixed(2);

    // Update savedCart object with the new quantity
    savedCart[productId] = {
        quantity: newQuantity,
        description: product.description,
        price: product.price,
        image: product.image
    };

    // Save updated cart data to the backend
    saveCartToDatabase(savedCart);

    // Optionally update the cart total display if required
    updateCartTotal(productId, newQuantity);

    // Enable add-ons if quantity is greater than 0
    enableAddons(productId, newQuantity > 0);
}

function decreaseQuantity(productId) {
    const product = products[productId];
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const totalElement = document.getElementById(`total-${productId}`);

    let currentQuantity = parseInt(quantityInput.value, 10);
    let newQuantity = currentQuantity - 1;

    // Prevent negative quantity
    if (newQuantity < 0) {
        return;
    }

    // Update quantity input field
    quantityInput.value = newQuantity;

    // Calculate the total price based on product quantity
    let totalAmount = newQuantity * product.price;

    // Retrieve add-ons from local storage and recalculate total
    let storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    if (storedAddons[productId]) {
        Object.values(storedAddons[productId]).forEach(addon => {
            // Add-on prices should be based on their quantity, not product quantity
            totalAmount += addon.price * addon.quantity;
        });
    }

    // Update total display
    totalElement.textContent = totalAmount.toFixed(2);

    // If quantity is 0, remove the product from savedCart
    if (newQuantity === 0) {
        delete savedCart[productId];
    } else {
        savedCart[productId] = {
            quantity: newQuantity,
            description: product.description,
            price: product.price,
            image: product.image
        };
    }

    // Save updated cart data to the backend
    saveCartToDatabase(savedCart);

    // Optionally update the cart total display if required
    updateCartTotal(productId, newQuantity);

    // Enable add-ons if quantity is greater than 0
    enableAddons(productId, newQuantity > 0);
}


function updateCartTotal(productId, change) {
    const product = products[productId];
    const currentQuantity = parseInt(document.getElementById(`quantity-${productId}`).value);
    
    savedCart[productId] = {  // Use detailed structure here
        quantity: currentQuantity,
        description: product.description,
        price: product.price,
        image: product.image
    };

    // Send updated cart data to the server
    saveCartToDatabase(savedCart);

    // Recalculate cart total
    cartTotal = 0;
    for (const id in savedCart) {
        if (savedCart[id].quantity > 0 && products[id]) {
            cartTotal += products[id].price * savedCart[id].quantity;  // Ensure you're using quantity
        }
    }

    // Update the total price display in the UI
    document.querySelector('.cart-total').textContent = `TOTAL: ₱${cartTotal}`;
}

function showSuccessModal(productId) {
    const product = products[productId];

    if (!product || !product.description) {
        alert('Product not found or missing description');
        return;
    }

    const quantityInput = document.getElementById(`quantity-${productId}`);
    const quantity = parseInt(quantityInput.value, 10);
    if (isNaN(quantity) || quantity <= 0) {
        alert('Please select a valid quantity.');
        return;
    }

    // Retrieve the selected add-ons from local storage
    const storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    const selectedAddons = storedAddons[productId] || {};

    // Ensure selectedAddons is processed as an array
    const addonsArray = Array.isArray(selectedAddons) ? selectedAddons : Object.values(selectedAddons);

    // Initialize arrays for sauces and drinks
    const sauces = [];
    const drinks = [];

    // Group the add-ons into sauces and drinks
    addonsArray.forEach(addon => {
        const addonData = {
            name: addon.name || '',
            price: addon.price || 0.0,
            quantity: addon.quantity || 1, // Default to 1 if 'quantity' is missing
            type: addon.type || 'sauce' // Default to 'sauce' if type is missing
        };

        if (addonData.type === 'sauce') {
            sauces.push(addonData);
        } else if (addonData.type === 'drink') {
            drinks.push(addonData);
        }
    });

    // Construct the cart data including grouped add-ons
    const cartData = {
        customer_id: customerId,
        cart: {
            [productId]: {
                description: product.description,
                quantity: quantity,
                price: product.price,
                image: product.image,
                addons: {
                    sauces: sauces,
                    drinks: drinks
                }
            }
        }
    };

    // Create the success message modal with the product description
    const successModal = document.createElement('div');
    successModal.id = 'successModal';
    successModal.classList.add('modal', 'custom-success-modal');
    successModal.innerHTML = ` 
        <div class="custom-modal-content">
            <div class="check-icon">✔️</div>
            <p class="custom-modal-message">Successfully added to cart!</p>
            <div class="custom-modal-actions">
                <button class="custom-action-btn custom-confirm-btn" onclick="closeSuccessModal(${productId})">Okay</button>
            </div>
        </div>
    `;

    // Append the modal to the body and display it
    document.body.appendChild(successModal);
    successModal.style.display = 'block';

    // Send cart data to placeOrder.php and update quantities
    fetch('API/placeOrder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(cartData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // After a successful order, update product quantity
            fetch('API/updateProductQuantity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update local product quantity and display new stock count
                    products[productId].quantity = data.new_quantity;
                    const stockCountElement = document.querySelector(`#productModal-${productId} .stock-count`);
                    if (stockCountElement) {
                        stockCountElement.textContent = data.new_quantity;
                    }
                } else {
                    alert(data.message || 'Failed to update product quantity.');
                }
            })
            .catch(error => console.error('Error updating product quantity:', error));

            // Clear localStorage for selected add-ons
            localStorage.removeItem('selectedAddons');
        } else {
            alert(`Failed to add to cart: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        alert('An error occurred while adding to the cart.');
    });
}



function closeSuccessModal(productId) {
    // Hide and remove the success modal
    const successModal = document.getElementById('successModal');
    if (successModal) {
        successModal.style.display = 'none';
        document.body.removeChild(successModal);
    }

    // Clear the quantity input to 0
    const quantityInput = document.getElementById(`quantity-${productId}`);
    if (quantityInput) {
        quantityInput.value = 0;
    }

    // Reset the total display to 0
    const totalDisplay = document.getElementById(`total-${productId}`);
    if (totalDisplay) {
        totalDisplay.textContent = 0;
    }

    // Uncheck all add-on checkboxes for the specified product
    const addonCheckboxes = document.querySelectorAll(`.sauce-addon-${productId}, .drink-addon-${productId}`);
    addonCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    // Optionally, clear any saved data related to this product in the savedCart if needed
    if (savedCart[productId]) {
        delete savedCart[productId];
    }

    // Clear localStorage for selected add-ons for this product
    const storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    if (storedAddons[productId]) {
        delete storedAddons[productId];
        localStorage.setItem('selectedAddons', JSON.stringify(storedAddons));
    }
}



// Add event listener to the search input for real-time search
const searchInput = document.getElementById('searchQuery');
const debouncedSearch = debounce(() => {
    const query = searchInput.value;
    fetchProducts(1, query); // Call fetchProducts on every input with the current query
}, 300); // Debounce with a 300ms delay to prevent excessive API calls

searchInput.addEventListener('input', debouncedSearch); // Trigger search on input

function requireLogin(action) {
    if (!customerId) {
        closeAllProductModals(); // Close any open product modals
        openLoginPromptModal(); // Show the login prompt modal
    } else {
        action(); // Execute the action if the user is logged in
    }
}

// Function to close all open product modals
function closeAllProductModals() {
    const productModals = document.querySelectorAll('.product-modal');
    productModals.forEach(modal => {
        modal.style.display = 'none';
    });
}

// Function to open the login prompt modal
function openLoginPromptModal() {
    const loginPromptModal = document.getElementById('loginPromptModal');
    loginPromptModal.style.display = 'flex';
}

// Function to close the login prompt modal
function closeLoginPromptModal() {
    const loginPromptModal = document.getElementById('loginPromptModal');
    loginPromptModal.style.display = 'none';
}

// Function to redirect to the login page
function redirectToLogin() {
    window.location.href = 'admin_login.php';
}





function clearSavedCart(productId) {
        // Clear savedCart data
        savedCart = {};

        // Call the function to save the empty cart to the database
        saveCartToDatabase(savedCart);

        // Close the modal
        closeProductModal(productId); // Assuming this function exists to close the modal


}

function clearCartOnRefresh() {
    // Clear the savedCart object
    savedCart = {}; 

    // Clear the localStorage for selected add-ons as well
    localStorage.removeItem('selectedAddons');

    // Save the empty cart to the database
    saveCartToDatabase(savedCart);
}


// Add event listener for page unload
window.addEventListener('beforeunload', clearCartOnRefresh);

function saveCartToDatabase(cart) {
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
    .then(response => response.text())  // Use .text() instead of .json() initially
    .then(text => {
        try {
            const data = JSON.parse(text);  // Parse JSON if valid
            if (data.status === 'success') {
                console.log('Cart saved successfully');
            } else {
                console.error('Failed to save cart:', data.message);
            }
        } catch (error) {
            console.error('Server response is not valid JSON:', text);  // Log the raw response
        }
    })
    .catch(error => {
        console.error('Error saving cart:', error);
    });
}

function updateCartBadge() {
    const customerId = getCustomerId();

    fetch(`API/getCartCount.php?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.getElementById('cartBadge');

            if (data.success && data.totalOrders > 0) {
                cartBadge.textContent = data.totalOrders; // Show the total orders
                cartBadge.style.display = 'inline-block'; // Make the badge visible
            } else {
                cartBadge.style.display = 'none'; // Hide the badge if no orders
            }
        })
        .catch(error => console.error('Error fetching order count:', error));
}

// Call the function once when the page loads
document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();

    // Set up auto-refresh every 5 seconds
    setInterval(updateCartBadge, 5000);
});

function updateOrderBadge() {
    const customerId = getCustomerId();

    fetch(`API/getOrderBadge.php?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            const OrderBadge = document.getElementById('OrderBadge');

            if (data.success && data.totalOrders > 0) {
                OrderBadge.textContent = data.totalOrders; // Show the total orders
                OrderBadge.style.display = 'inline-block'; // Make the badge visible
            } else {
                OrderBadge.style.display = 'none'; // Hide the badge if no orders
            }
        })
        .catch(error => console.error('Error fetching order count:', error));
}

// Call the function once when the page loads
document.addEventListener('DOMContentLoaded', () => {
    updateOrderBadge();

    // Set up auto-refresh every 5 seconds
    setInterval(updateOrderBadge, 5000);
});



function openCartModal() {
    const customerId = getCustomerId();

    fetch(`API/getSavedCart.php?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartItemsBody = document.getElementById('cartItemsBody');
                cartItemsBody.innerHTML = '';

                // Select All Row
                const selectAllRow = document.createElement('tr');
                selectAllRow.innerHTML = `
                    <td colspan="5">
                        <label>
                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)"> Select All
                        </label>
                    </td>
                `;
                cartItemsBody.appendChild(selectAllRow);

                let totalPrice = 0;

                data.cartItems.forEach(item => {
                    const { id, description, quantity, price, image, addons } = item;
                    const numericPrice = parseFloat(price);

                    const cartItemRow = document.createElement('tr');
                    cartItemRow.innerHTML = `
                        <td><input type="checkbox" class="productCheckbox" data-id="${id}" onclick="updateTotalPriceBasedOnSelection()"></td>
                        <td>${image ? `<img src="data:image/jpeg;base64,${image}" alt="${description}" class="cart-product-image">` : ''}</td>
                        <td>${description}</td>
                        <td>${quantity}</td>
                        <td>₱${numericPrice.toFixed(2)}</td>
                        <td>
                            <div class="cart-button-container">
                                <button onclick="deleteCartItem(${id}, ${customerId})" class="cart-delete-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button onclick="deleteCart(${id}, ${customerId})" class="cart-delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="increaseCartItem(${id}, ${customerId})" class="cart-delete-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                    `;

                    cartItemsBody.appendChild(cartItemRow);

                    if (addons && addons.length > 0) {
                        addons.forEach(addon => {
                            const { addon_type, addon_name, addon_price, addon_quantity, addon_id } = addon;
                            const numericAddonPrice = parseFloat(addon_price);

                            const addonRow = document.createElement('tr');
                            addonRow.classList.add('addon-row');
                            addonRow.setAttribute('data-product-id', item.id);
                            addonRow.innerHTML = `
                            <td></td>
                            <td colspan="2" class="addon-detail">+ ${addon_type}: ${addon_name}</td>
                            <td>${addon_quantity}</td>
                            <td>₱${numericAddonPrice.toFixed(2)}</td>
                            <td>
                                <div class="cart-button-container"> 
                                    <button onclick="deleteAddon(${addon_id})" class="cart-delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                            cartItemsBody.appendChild(addonRow);
                        });
                    }
                });

                updateTotalPriceBasedOnSelection();
            } else {
                console.warn("Failed to fetch cart items:", data.message);
            }
        })
        .catch(error => console.error('Error fetching updated cart data:', error));

    const cartModal = document.getElementById('cartModal');
    cartModal.style.display = 'block';
}

// Function to toggle all checkboxes and update the total based on selection
function toggleSelectAll(selectAllCheckbox) {
    const productCheckboxes = document.querySelectorAll('.productCheckbox');
    productCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    updateTotalPriceBasedOnSelection();
}

function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const allProductCheckboxes = document.querySelectorAll('.productCheckbox');
    const allChecked = Array.from(allProductCheckboxes).every(checkbox => checkbox.checked);
    selectAllCheckbox.checked = allChecked;
    updateTotalPriceBasedOnSelection();
}

function updateTotalPriceBasedOnSelection() {
    let totalPrice = 0;

    // Loop through each checked product
    document.querySelectorAll('.productCheckbox:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const quantity = parseInt(row.querySelector('td:nth-child(4)').textContent);  // Product quantity
        const price = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace('₱', '').trim());  // Product price

        // Validate product price and quantity
        if (isNaN(quantity) || isNaN(price)) {
            console.warn('Invalid quantity or price for product');
            return; // Skip if either value is invalid
        }

        // Add the product price times the quantity
        totalPrice += price * quantity;

        // Automatically include add-ons associated with the selected product
        const productId = checkbox.getAttribute('data-id');

        // Loop through each add-on row for the selected product
        const addonRows = document.querySelectorAll(`.addon-row[data-product-id="${productId}"]`);
        addonRows.forEach(addonRow => {

            // Get the quantity and price for the add-on (corrected columns)
            const addonQuantityText = addonRow.querySelector('td:nth-child(3)').textContent.trim();  // Add-on quantity (now from column 3)
            const addonPriceText = addonRow.querySelector('td:nth-child(4)').textContent.replace('₱', '').trim();  // Add-on price (now from column 4)


            // Convert to numbers
            const addonQuantity = parseInt(addonQuantityText);
            const addonPrice = parseFloat(addonPriceText);


            // Validate add-on price and quantity
            if (isNaN(addonQuantity) || isNaN(addonPrice)) {
                console.warn('Invalid quantity or price for add-on');
                return; // Skip if either value is invalid
            }

            // Add the addon price times its quantity to the total price
            totalPrice += addonPrice * addonQuantity;
        });
    });

    // Update the total price displayed on the page
    const cartTotalElement = document.querySelector('.cart-total');
    if (cartTotalElement) {
        cartTotalElement.textContent = `TOTAL: ₱${totalPrice.toFixed(2)}`;
    }
}


function deleteCart(orderId) {
    fetch(`API/deleteCart.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message); // Success message
            openCartModal();
            // Optionally refresh the cart display or remove the item from the UI
        } else {
            console.error(data.message); // Error message
        }
    })
    .catch(error => {
        console.error('Error fetching cart data:', error);
    });
}

function deleteAddon(addonId) {
    fetch(`API/deleteAddon.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${addonId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message); // Success message
            openCartModal();
            
        } else {
            console.error(data.message); // Error message
        }
    })
    .catch(error => {
        console.error('Error fetching addon data:', error);
    });
}


// Attach `updateCartDisplay` to delete and increase functions
function deleteCartItem(cartItemId, customerId) {
    fetch('API/deleteCartItem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ cart_item_id: cartItemId, customer_id: customerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === "Item quantity reduced by 1" || data.message === "Item deleted successfully") {
            openCartModal(); // Refresh display after deletion
        } else {
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function increaseCartItem(cartItemId, customerId) {
    fetch('API/addCartItem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ cart_item_id: cartItemId, customer_id: customerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === "Item quantity increased by 1") {
            openCartModal(); // Refresh display after increment
        } else {
            console.error(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Function to open the checkout modal with only checked items
function openCheckoutModal() {
    const checkoutModal = document.getElementById('checkoutModal');
    checkoutModal.style.display = 'block'; // Show the modal

    // Gather checked items in the cart modal
    const checkedProductIds = Array.from(document.querySelectorAll('.productCheckbox:checked'))
        .map(checkbox => parseInt(checkbox.getAttribute('data-id')));
    const checkedAddonIds = Array.from(document.querySelectorAll('.addonCheckbox:checked'))
        .map(checkbox => parseInt(checkbox.getAttribute('data-id')));

    // Fetch cart items from the server
    fetch('API/fetch_cart.php')
        .then(response => response.json())
        .then(cartItems => {
            if (cartItems.length === 0) {
                alert("No items in the cart.");
                closeCheckoutModal(); // Close modal if cart is empty
                return;
            }

            // Filter items to include only selected products and addons
            const selectedCartItems = cartItems.filter(item =>
                checkedProductIds.includes(item.id) ||
                (item.addons && item.addons.some(addon => checkedAddonIds.includes(addon.addon_id)))
            );

            // Pass the filtered items to populateCheckoutModal
            populateCheckoutModal(selectedCartItems);
        })
        .catch(error => {
            console.error('Error fetching cart items:', error);
            alert("Error loading cart items.");
        });
}

// Function to close the checkout modal
function closeCheckoutModal() {
    document.getElementById("checkoutModal").style.display = "none";
}

// Event listener for Place Order button in the cart modal
document.querySelector(".cart-addToCart").addEventListener("click", openCheckoutModal);

function populateCheckoutModal(cartItems) {
    const selectedProductsContainer = document.getElementById('selectedProductsContainer');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');

    // Clear previous content
    selectedProductsContainer.innerHTML = '';
    totalAmountDisplay.textContent = '';

    let totalAmount = 0;

    // Iterate over fetched and filtered cart items
    cartItems.forEach(item => {
        // Ensure item.price and item.quantity are valid numbers
        const productPrice = parseFloat(item.price);
        const productQuantity = parseInt(item.quantity, 10);

        // If product price or quantity is invalid, skip this item
        if (isNaN(productPrice) || isNaN(productQuantity)) {
            console.warn('Invalid price or quantity for item:', item);
            return;
        }

        const productTotal = productPrice * productQuantity;
        totalAmount += productTotal;

        // Create a container for each product
        const productEntry = document.createElement('div');

        // Add image if available
        if (item.image) {
            const productImage = document.createElement('img');
            productImage.src = `data:image/jpeg;base64,${item.image}`;
            productImage.alt = item.description || 'Product image';
            productImage.style.width = '50px';
            productImage.style.height = '50px';
            productEntry.appendChild(productImage);
        } else {
            console.warn('No image data found for item:', item);
        }

        // Add product description, price, and quantity
        const productText = document.createElement('p');
        productText.textContent = `${item.description} (₱${productPrice.toFixed(2)} each, x${productQuantity}): ₱${productTotal.toFixed(2)}`;
        productEntry.appendChild(productText);

        // Check if the item has add-ons
        if (item.addons && item.addons.length > 0) {
            const addonsList = document.createElement('ul'); // Create a list for addons
            let addonsTotal = 0;

            // Iterate over each add-on and calculate its total with quantity
            item.addons.forEach(addon => {
                const addonPrice = parseFloat(addon.addon_price);
                const addonQuantity = addon.addon_quantity ? parseInt(addon.addon_quantity, 10) : 1; // Use addon.addon_quantity

                // If the addon price and quantity are valid, calculate total and display
                if (!isNaN(addonPrice) && !isNaN(addonQuantity)) {
                    const addonItem = document.createElement('li');
                    addonItem.textContent = `${addon.addon_name} (${addon.addon_type}): ₱${addonPrice.toFixed(2)} x ${addonQuantity}`;
                    addonsList.appendChild(addonItem);

                    addonsTotal += addonPrice * addonQuantity;
                } else {
                    console.warn('Invalid addon price or quantity for:', addon);
                }
            });

            // Append the addons list to the product entry
            productEntry.appendChild(addonsList);

            // Add the total of addons to the overall total
            totalAmount += addonsTotal;
        }

        // Append the product entry to the container
        selectedProductsContainer.appendChild(productEntry);
    });

    // Display the total amount
    totalAmountDisplay.textContent = `Total Amount: ₱${totalAmount.toFixed(2)}`;
}



function openBuyNowCheckoutModal() {
    // Close any open Buy Now and Product modals
    const buyNowModals = document.querySelectorAll('.buy-now-modal');
    buyNowModals.forEach(modal => {
        modal.style.display = 'none';
    });

    const productModals = document.querySelectorAll('.product-modal'); // Adjust class name as necessary
    productModals.forEach(modal => {
        modal.style.display = 'none';
    });

    const checkoutModal = document.getElementById('buynowModal'); // Adjust the ID if necessary
    if (checkoutModal) {
        checkoutModal.style.display = 'block'; // Show the checkout modal
    } else {
        console.error("Checkout modal not found.");
    }

    // Reset quantity and total in the modal when opening
    const quantityInput = document.getElementById('buyNowQuantity');
    if (quantityInput) {
        quantityInput.value = 0; // Reset quantity to 0
    }

    const totalDisplay = document.getElementById('buyNowTotal');
    if (totalDisplay) {
        totalDisplay.textContent = '₱ 0'; // Reset total to 0
    }

    // Fetch cart items from the server
    fetch('API/fetch_cart_table.php')
        .then(response => response.json())
        .then(cartItems => {
            if (cartItems.length === 0) {
                alert("No items in the cart.");
                closeBuyNowCheckoutModal(); // Close the modal if no items
                return;
            }

            // Populate modal with cart items
            populateBuyNowModal(cartItems);
        })
        .catch(error => {
            console.error('Error fetching cart items:', error);
            alert("Error loading cart items.");
        });
}

function closeBuyNowCheckoutModal() {
    const checkoutModal = document.getElementById("buynowModal");
    if (checkoutModal) {
        checkoutModal.style.display = "none"; // Hide the checkout modal
    }

    // Reset all quantity input fields and total price displays
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.value = 0; // Reset each quantity input to 0
    });

    const totalDisplays = document.querySelectorAll('[id^="total-"]');
    totalDisplays.forEach(total => {
        total.textContent = '0'; // Reset each total display to 0
    });

    // Clear savedCart data
    savedCart = {};

    // Save the empty cart to the database
    saveCartToDatabase(savedCart);

    // Clear the displayed data in the checkout modal
    const buyNowContainer = document.getElementById('buyNowContainer');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');
    const pickupDate = document.getElementById('buynowpickupDate');
    const pickupTime = document.getElementById('buynowpickupTime');

    if (buyNowContainer) {
        buyNowContainer.innerHTML = ''; // Clear previous product entries
    }

    if (totalAmountDisplay) {
        totalAmountDisplay.textContent = ''; // Clear the total amount display
    }

    // Clear pickup date and time selections
    if (pickupDate) {
        pickupDate.selectedIndex = 0; // Reset to the first option, if applicable
    }

    if (pickupTime) {
        pickupTime.selectedIndex = 0; // Reset to the first option, if applicable
    }

    // Reset all add-on checkboxes
    const addonCheckboxes = document.querySelectorAll('.addon-options input[type="checkbox"]');
    addonCheckboxes.forEach(checkbox => {
        checkbox.checked = false; // Uncheck all add-on checkboxes
    });

    // Reset all add-on dropdowns
    const addonDropdowns = document.querySelectorAll('.addon-options select');
    addonDropdowns.forEach(dropdown => {
        dropdown.value = "0"; // Reset dropdown value to 0
        dropdown.disabled = true; // Disable the dropdown
    });

     // Clear add-ons from local storage
    localStorage.removeItem('selectedAddons')
}


function populateBuyNowModal(cartItems) {
    const buyNowContainer = document.getElementById('buyNowContainer');
    const totalAmountDisplay = document.getElementById('totalAmountDisplay');

    // Clear previous content
    buyNowContainer.innerHTML = '';
    totalAmountDisplay.textContent = '';

    let totalAmount = 0;

    // Retrieve stored add-ons
    let storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};

    console.log("Stored Addons:", storedAddons); // Debugging: check the stored add-ons in localStorage

    // Iterate over fetched cart items
    cartItems.forEach(item => {
        const productEntry = document.createElement('div');
        const itemPrice = parseFloat(item.price); // Base price of the product

        // Validate and calculate base item total
        const itemTotal = item.quantity * (isNaN(itemPrice) ? 0 : itemPrice);
        let addonsTotal = 0;

        // Retrieve the stored add-ons for the current product
        const addonsForProduct = storedAddons[item.product_id] ? Object.values(storedAddons[item.product_id]) : [];

        console.log(`Add-ons for Product ${item.product_id}:`, addonsForProduct); // Debugging: check if add-ons exist for this product

        // Calculate total price of selected add-ons
        addonsForProduct.forEach(addon => {
            addonsTotal += parseFloat(addon.price) * addon.quantity; // Now multiply by quantity of addon
        });

        // Calculate the total price for the product including add-ons
        const totalForProduct = itemTotal + addonsTotal; // Add add-ons total without multiplying by quantity

        // Add the product total to the overall total
        totalAmount += totalForProduct;

        // Display item description, price, quantity, total, and add-ons
        productEntry.innerHTML = `
            <div class="product-entry">
                <p>Product: ${item.description}</p>
                <p>Price: ₱${itemPrice.toFixed(2)}</p> <!-- Display base product price -->
                <p>Quantity: ${item.quantity}</p>
                <p>Base Total: ₱${itemTotal.toFixed(2)}</p>
                <p>Add-ons Total: ₱${addonsTotal.toFixed(2)}</p>
                <p>Total Amount: ₱${totalForProduct.toFixed(2)}</p>
                ${item.image ? `<img src="data:image/jpeg;base64,${item.image}" alt="${item.description}" style="width: 100px; height: auto;">` : ''}
                <div class="addons">
                    <strong>Add-ons:</strong>
                    <ul>
                        ${addonsForProduct.length > 0 ? addonsForProduct.map(addon => `<li>${addon.name} (+₱${addon.price} x ${addon.quantity})</li>`).join('') : '<li>No add-ons selected</li>'}
                    </ul>
                </div>
            </div>
        `;
        buyNowContainer.appendChild(productEntry);
    });

    // Display the overall total
    totalAmountDisplay.textContent = 'Total Amount: ₱' + totalAmount.toFixed(2);
}

document.getElementById("buynowcheckoutForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    // Retrieve pickup date and time from the form
    const pickupDate = document.getElementById("buynowpickupDate").value;
    const pickupTime = document.getElementById("buynowpickupTime").value;

    // Retrieve addons from localStorage
    const storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};

    // First, update the quantity of each product in the database
    fetch('API/fetch_cart_table.php')
        .then(response => response.json())
        .then(cartItems => {
            if (cartItems.length === 0) {
                alert("No items in the cart.");
                closeBuyNowCheckoutModal(); // Close the modal if no items
                return;
            }

            // Create promises for updating quantities
            const updatePromises = cartItems.map(item =>
                fetch('API/updateProductQuantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ product_id: item.product_id, quantity: item.quantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const newQuantity = data.new_quantity;

                        // Update the stock count display if applicable
                        const stockCountElement = document.querySelector(`#productModal-${item.product_id} .stock-count`);
                        if (stockCountElement) {
                            stockCountElement.textContent = newQuantity;
                        }
                    } else {
                        console.error(data.message || 'Failed to update product quantity.');
                    }
                })
                .catch(error => {
                    console.error('Error updating product quantity:', error);
                })
            );

            // Wait for all quantity updates to complete before proceeding to checkout
            Promise.all(updatePromises)
                .then(() => {
                    // Construct the data to send to buyNowCheckOut.php after quantity updates
                    const checkoutData = {
                        customer_id: customerId,
                        pickupDate: pickupDate,
                        pickupTime: pickupTime,
                        addons: storedAddons // Use storedAddons here
                    };

                    // Send data to PHP script for order processing
                    fetch('API/buyNowCheckOut.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(checkoutData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showOrderFeedbackModal("Order placed successfully!", true);
                            closeBuyNowCheckoutModal();

                            // Clear localStorage for selected addons after successful checkout
                            localStorage.removeItem('selectedAddons');
                        } else {
                            showOrderFeedbackModal("Error: " + data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error during order confirmation:', error);
                        showOrderFeedbackModal("An error occurred while confirming your order.", false);
                    });
                });
        })
        .catch(error => {
            console.error('Error fetching cart items:', error);
            alert("Error loading cart items.");
        });
});

document.getElementById("checkoutForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    // Retrieve the pickup date and time
    const pickupDate = document.getElementById("pickupDate").value;
    const pickupTimeFormatted = document.getElementById("pickupTime").value;
    const pickupTime24 = convertTo24HourFormat(pickupTimeFormatted);

    // Get only checked items and addons
    const selectedProductIds = Array.from(document.querySelectorAll('.productCheckbox:checked'))
                                     .map(checkbox => checkbox.dataset.id);
    const selectedAddonIds = Array.from(document.querySelectorAll('.addonCheckbox:checked'))
                                  .map(checkbox => checkbox.dataset.id);

    const checkoutData = {
        customer_id: customerId,
        pickupDate: pickupDate,
        pickupTime: pickupTime24,
        selectedProductIds: selectedProductIds,
        selectedAddonIds: selectedAddonIds
    };

    // Send the data using Fetch API
    fetch('API/checkOut.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(checkoutData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Order placed successfully!");
            closeCheckoutModal();
            closeCartModal();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

// Set the minimum and maximum dates for the pickup date input to today and tomorrow
// Set the minimum and maximum dates for the pickup date input to today and tomorrow
document.getElementById("pickupDate").min = new Date().toISOString().split('T')[0];
const tomorrowDate = new Date();
tomorrowDate.setDate(tomorrowDate.getDate() + 1);
document.getElementById("pickupDate").max = tomorrowDate.toISOString().split('T')[0];

// Function to populate date dropdown with "Today" and "Tomorrow" options
function populateDateOptions() {
    const pickupDateElement = document.getElementById("pickupDate");

    const now = new Date();
    const today = now.toISOString().split('T')[0];
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowDate = tomorrow.toISOString().split('T')[0];

    pickupDateElement.innerHTML = `
        <option value="${today}">Today (${today})</option>
        <option value="${tomorrowDate}">Tomorrow (${tomorrowDate})</option>
    `;
}

// Function to populate time options based on selected date
function populateTimeOptions() {
    const timeSelect = document.getElementById("pickupTime");
    const dateInput = document.getElementById("pickupDate"); // Assumes there’s an input for the date
    const currentDate = new Date(); // Current date and time
    const selectedDate = new Date(dateInput.value); // The selected date

    timeSelect.innerHTML = ""; // Clear any existing options

    const startHour = 6; // Start at 6 AM
    const endHour = 19; // End at 7 PM (24-hour format)

    // Loop through hours and minutes to create time options
    for (let hour = startHour; hour <= endHour; hour++) {
        for (let minute = 0; minute < 60; minute += 25) {
            const optionTime = new Date();
            optionTime.setHours(hour, minute, 0, 0); // Set the option time

            // Check if the selected date is today
            const isToday = currentDate.toDateString() === selectedDate.toDateString();

            // If today, only allow times later than the current time
            if (!isToday || optionTime > currentDate) {
                // Format time in 12-hour format with AM/PM
                let displayHour = hour % 12 || 12; // Convert to 12-hour format
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const minutes = minute.toString().padStart(2, '0');
                const formattedTime = `${displayHour}:${minutes} ${ampm}`;

                // Create option element
                const option = document.createElement("option");
                option.value = `${hour.toString().padStart(2, '0')}:${minutes}`; // Set value as HH:MM in 24-hour format
                option.textContent = formattedTime; // Display 12-hour format with AM/PM
                timeSelect.appendChild(option);
            }
        }
    }
}

// Populate date options and set up event listener for date changes
populateDateOptions();
document.getElementById("pickupDate").addEventListener("change", populateTimeOptions);

// Initial population of time options based on the default date selection
populateTimeOptions();



// Function to convert 12-hour format to 24-hour format
function convertTo24HourFormat(time12h) {
    const [time, modifier] = time12h.split(" ");
    let [hours, minutes] = time.split(":");

    if (modifier === "PM" && hours !== "12") {
        hours = parseInt(hours, 10) + 12;
    }
    if (modifier === "AM" && hours === "12") {
        hours = "00";
    }

    return `${hours}:${minutes}:00`; // Append seconds
}

// Populate date and time options on page load
populateDateOptions();
populateTimeOptions();



// Set the minimum and maximum dates for the buy now pickup date input to today and tomorrow
document.getElementById("buynowpickupDate").min = new Date().toISOString().split('T')[0];
tomorrowDate.setDate(tomorrowDate.getDate() + 1);
document.getElementById("buynowpickupDate").max = tomorrowDate.toISOString().split('T')[0];

// Function to populate date dropdown with "Today" and "Tomorrow" options
function populateBuyNowDateOptions() {
    const pickupDateElement = document.getElementById("buynowpickupDate");

    const now = new Date();
    const today = now.toISOString().split('T')[0];
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowDate = tomorrow.toISOString().split('T')[0];

    pickupDateElement.innerHTML = `
        <option value="${today}">Today (${today})</option>
        <option value="${tomorrowDate}">Tomorrow (${tomorrowDate})</option>
    `;
}

// Function to populate time options based on selected date
function populateBuyNowTimeOptions() {
    const timeSelect = document.getElementById("buynowpickupTime");
    const dateInput = document.getElementById("buynowpickupDate");
    const currentDate = new Date();
    const selectedDate = new Date(dateInput.value);

    timeSelect.innerHTML = ""; // Clear any existing options

    const startHour = 6;
    const endHour = 19;

    // Loop through hours and minutes to create time options
    for (let hour = startHour; hour <= endHour; hour++) {
        for (let minute = 0; minute < 60; minute += 25) {
            const optionTime = new Date();
            optionTime.setHours(hour, minute, 0, 0); // Set the option time

            // Check if the selected date is today
            const isToday = currentDate.toDateString() === selectedDate.toDateString();

            // If today, only allow times later than the current time
            if (!isToday || optionTime > currentDate) {
                // Format time in 12-hour format with AM/PM
                let displayHour = hour % 12 || 12; // Convert to 12-hour format
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const minutes = minute.toString().padStart(2, '0');
                const formattedTime = `${displayHour}:${minutes} ${ampm}`;

                // Create option element
                const option = document.createElement("option");
                option.value = `${hour.toString().padStart(2, '0')}:${minutes}`; // Set value as HH:MM in 24-hour format
                option.textContent = formattedTime; // Display 12-hour format with AM/PM
                timeSelect.appendChild(option);
            }
        }
    }
}

// Populate date options and set up event listener for date changes
populateBuyNowDateOptions();
document.getElementById("buynowpickupDate").addEventListener("change", populateBuyNowTimeOptions);

// Initial population of time options based on the default date selection
populateBuyNowTimeOptions();


function openOrdersModal() {
    document.getElementById("ordersModal").style.display = "block";
    fetchOrders(currentPage);
}

function closeOrdersModal() {
    document.getElementById("ordersModal").style.display = "none";
}

function convertTo24Hour(timeStr) {
    if (!timeStr) return ''; // Handle null or undefined time values

// Split the time string into hours and minutes (ignore seconds if present)
let [hours, minutes] = timeStr.split(':').map(Number);

// Determine AM/PM
const modifier = hours >= 12 ? 'pm' : 'am';

// Convert hours from 24-hour to 12-hour format
hours = hours % 12 || 12; // Convert 0 to 12 for midnight

// Return formatted time as 12-hour format with AM/PM
return `${hours}:${String(minutes).padStart(2, '0')} ${modifier}`;
}

function fetchOrders() {
    fetch('API/getCustomerOrder.php')  // Fetch all orders without pagination
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById("ordersContainer").innerHTML = `<p>${data.error}</p>`;
                return;
            }

            // Populate orders in table
            const tbody = document.querySelector("#ordersTable tbody");
            tbody.innerHTML = ""; // Clear previous rows

            data.orders.forEach(order => {
                // Convert pickup time to 24-hour format
                const pickupTime24 = convertTo24Hour(order.pickup_time);

                // Prepare addons information
                let addonsHTML = "";
                let addonsTotal = 0;
                if (order.addons.length > 0) {
                    order.addons.forEach(addon => {
                        const addonPrice = parseFloat(addon.addon_price) || 0;
                        const addonTotalPrice = addonPrice * addon.addon_quantity;
                        addonsTotal += addonTotalPrice;
                        addonsHTML += `
                            <div>${addon.addon_name} x ${addon.addon_quantity} (₱${addonPrice.toFixed(2)} each)</div>
                        `;
                    });
                } else {
                    addonsHTML = "<div>No addons</div>";
                }

                // Add the order row with addons and final total
                const row = `
                    <tr>
                        <td>${order.product_description}</td>
                        <td>₱${order.total_amount.toFixed(2)}</td>
                        <td>${addonsHTML}</td>
                        <td>₱${addonsTotal.toFixed(2)}</td>
                        <td>₱${order.final_total.toFixed(2)}</td>
                        <td>${order.pickup_date}</td>
                        <td>${pickupTime24}</td>
                        <td>${order.confirmation_status}</td>
                        <td><button class="cancel-btn" onclick="orderCancel(${order.order_id}, '${order.confirmation_status}')">Cancel</button></td>
                    </tr>
                `;
                // Insert each row at the top to ensure newest orders are displayed first
                tbody.insertAdjacentHTML("afterbegin", row);
            });
        })
        .catch(error => {
            console.error("Error fetching orders:", error);
            document.getElementById("ordersContainer").innerHTML = `<p>Error fetching orders</p>`;
        });
}

function fetchPendingOrdersReminder() {
    fetch('API/remiderOrder.php') // Ensure this URL points to the updated PHP script for fetching all pending orders
        .then(response => response.json())
        .then(data => {
            const reminderDetails = document.getElementById("reminderDetails");
            const reminderSection = document.getElementById("orderReminder");

            // Check for errors or no orders found
            if (data.error || data.message) {
                reminderDetails.innerHTML = `<p>${data.error || data.message}</p>`;
                
                // Hide the reminder section if no orders are found
                reminderSection.style.display = 'none';
                return;
            }

            // If there are pending orders, display each order
            if (Array.isArray(data) && data.length > 0) {
                let ordersHTML = `<h3 class="details-title">Your Orders</h3>`;
                data.forEach(order => {
                    // Format pickup time (convert to 24-hour format if needed)
                    const pickupTime24 = convertTo24Hour(order.pickup_time);

                    // Start building the order details
                    ordersHTML += `
                        <div class="order-item">
                            <strong>Order:</strong> "${order.product_description}" 
                            is <span class="text-warning">${order.confirmation_status}</span>.
                            <br> Pickup on <strong>${order.pickup_date}</strong> at <strong>${pickupTime24}</strong>.
                    `;

                    // Display add-ons if they exist
                    if (order.addons && order.addons.length > 0) {
                        ordersHTML += `<br><strong>Add-ons:</strong><ul>`;
                        order.addons.forEach(addon => {
                            ordersHTML += `
                                <li>${addon.addon_name} (${addon.addon_quantity} x $${addon.addon_price}) - Total: $${addon.total_addon_price}</li>
                            `;
                        });
                        ordersHTML += `</ul>`;
                    }

                    // Display total addon price
                    if (order.total_addon_price > 0) {
                        ordersHTML += `
                            <br><strong>Total Add-ons Price:</strong> $${order.total_addon_price}
                        `;
                    }

                    // Close the order details
                    ordersHTML += `
                        <hr>
                    `;
                });

                reminderDetails.innerHTML = ordersHTML;

                // Show the reminder section if there are orders
                reminderSection.style.display = 'block';
            } else {
                reminderDetails.innerHTML = `<p>No pending orders found.</p>`;
                
                // Hide the reminder section if there are no orders
                reminderSection.style.display = 'none';
            }
        })
        .catch(error => {
            console.error("Error fetching pending orders:", error);
            document.getElementById("reminderDetails").innerHTML = `<p>Error retrieving data.</p>`;
            
            // Hide the reminder section if there's an error
            const reminderSection = document.getElementById("orderReminder");
            reminderSection.style.display = 'none';
        });
}

// Add click functionality to toggle the reminder details
document.addEventListener("DOMContentLoaded", () => {
    const reminderTitle = document.getElementById("reminderTitle");
    const reminderSection = document.getElementById("orderReminder");
    const reminderDetails = document.getElementById("reminderDetails");

    // Fetch reminders initially
    fetchPendingOrdersReminder();

    // Refresh reminders every 5 seconds
    setInterval(fetchPendingOrdersReminder, 5000);

    // Toggle visibility of the details when the title is clicked
    reminderTitle.addEventListener("click", () => {
        const isHidden = reminderDetails.style.display === "none";
        reminderDetails.style.display = isHidden ? "block" : "none";
        reminderSection.classList.toggle("minimized", !isHidden); // Toggle minimized class
        reminderTitle.textContent = isHidden
            ? "🔔 Hide reminders"
            : "🔔 Reminders! Click to view details";
    });
});

function showCancelModal(message, options = {}) {
    const modal = document.getElementById("cancelModal");
    if (!modal) {
        console.error("Modal not found!");
        return; // Exit function if modal is not found
    }

    const modalMessage = document.getElementById("modal-message");
    const confirmButton = document.getElementById("confirm-button");
    const cancelButton = document.getElementById("cancel-button");
    const closeButton = document.getElementById("close-button");
    const cancelReasonForm = document.getElementById("cancelReasonForm");
    const otherReasonRadio = document.getElementById("otherReasonRadio");
    const otherReasonInput = document.getElementById("otherReasonInput");

    if (!modalMessage || !confirmButton || !cancelButton || !closeButton || !cancelReasonForm) {
        console.error("One or more modal elements are missing!");
        return; // Exit function if any critical element is missing
    }

    modalMessage.textContent = message;

    // Show or hide cancellation reason form
    if (options.showReasons) {
        cancelReasonForm.style.display = "block";
        cancelReasonForm.reset();
        otherReasonInput.style.display = "none";
    } else {
        cancelReasonForm.style.display = "none";
    }

    // Confirm button
    confirmButton.style.display = options.showConfirm ? "inline-block" : "none";
    if (options.showConfirm) {
        confirmButton.onclick = () => {
            if (options.showReasons) {
                const selectedReason = document.querySelector('input[name="cancelReason"]:checked');
                let reason = selectedReason ? selectedReason.value : null;

                if (reason === "Other") {
                    reason = otherReasonInput.value.trim();
                }

                if (!reason) {
                    alert("Please select or specify a reason.");
                    return;
                }

                options.onConfirm(reason);
            } else {
                options.onConfirm();
            }
        };
    }

    // Cancel button
    cancelButton.style.display = options.showCancel ? "inline-block" : "none";
    if (options.showCancel) {
        cancelButton.onclick = options.onCancel || (() => (modal.style.display = "none"));
    }

    // Close button
    closeButton.style.display = options.showClose ? "inline-block" : "none";
    if (options.showClose) {
        closeButton.onclick = () => (modal.style.display = "none");
    }

    modal.style.display = "flex";
}


document.addEventListener("DOMContentLoaded", () => {
    const otherReasonRadio = document.getElementById("otherReasonRadio");
    const otherReasonInput = document.getElementById("otherReasonInput");

    if (otherReasonRadio && otherReasonInput) {
        // Add a change event listener to all radio buttons
        const cancelReasonRadios = document.querySelectorAll('input[name="cancelReason"]');
        cancelReasonRadios.forEach(radio => {
            radio.addEventListener("change", () => {
                if (otherReasonRadio.checked) {
                    otherReasonInput.style.display = "block";
                } else {
                    otherReasonInput.style.display = "none";
                    otherReasonInput.value = ""; // Clear the input if "Other" is not selected
                }
            });
        });
    }
});

function orderCancel(orderId, confirmationStatus) {
    if (confirmationStatus.toLowerCase() === "confirmed") {
        showCancelModal("Cannot cancel confirmed orders", { showClose: true });
        return;
    }

    showCancelModal("Are you sure you want to cancel this order?", {
        showReasons: true,
        showConfirm: true,
        showCancel: true,
        onConfirm: (reason) => {
            document.getElementById("cancelModal").style.display = "none";
            fetch(`API/cancelOrder.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    confirmation_status: confirmationStatus, // Pass the current confirmationStatus here
                    remarks: reason
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCancelModal("Order canceled successfully", { showClose: true });
                    fetchOrders(currentPage); // Refresh orders
                } else {
                    showCancelModal(data.message || "Error canceling order", { showClose: true });
                }
            })
            .catch(error => {
                console.error("Error canceling order:", error);
                showCancelModal("An error occurred while canceling the order. Please try again later.", { showClose: true });
            });
        }
    });
}


// Automatically refresh orders when the orders modal is open
setInterval(() => {
    if (document.getElementById("ordersModal").style.display === "block") {
        fetchOrders(currentPage);
    }
}, 2000);

function showOrderFeedbackModal(message, isSuccess = true) {
    // Remove any existing feedback modal
    const existingModal = document.getElementById('feedbackModal');
    if (existingModal) {
        existingModal.remove();
    }

    const feedbackModal = document.createElement('div');
feedbackModal.id = 'feedbackModal';
feedbackModal.classList.add('custom-success-modal'); // Use the class from the CSS


// Populate the modal with the content
feedbackModal.innerHTML = `
    <div class="custom-modal-content">
        <!-- Check icon, will show ✔️ for success, ⚠️ for error -->
        <div class="check-icon">${isSuccess ? '✔️' : '⚠️'}</div>

        <!-- Modal message -->
        <p class="custom-modal-message">${message}</p>

        <!-- Modal actions (buttons) -->
        <div class="custom-modal-actions">
            <button class="custom-action-btn custom-confirm-btn" onclick="closeOrderFeedbackModal()">Close</button>
        </div>
        
        <!-- Close button (optional) -->
        <span class="custom-close" onclick="closeOrderFeedbackModal()">×</span>
    </div>
`;

// Append the modal to the body (or another container in your app)
document.body.appendChild(feedbackModal);

    // Display the modal
    feedbackModal.style.display = 'block';
}

// Function to close the feedback modal
function closeOrderFeedbackModal() {
    const feedbackModal = document.getElementById('feedbackModal');
    if (feedbackModal) {
        feedbackModal.style.display = 'none';
        feedbackModal.remove();
    }
}

function cancelOrder(productId, initialQuantity) {
    // Close the modal
    closeConfirmModal(productId);
    
}

function closeCartModal() {
    const cartModal = document.getElementById('cartModal');
    cartModal.style.display = 'none';
}

function getCustomerId() {
    return customerId;
}

function closeConfirmModal(productId) {
    const modal = document.getElementById(`confirmModal-${productId}`);
    if (modal) {
        modal.style.display = 'none';
        document.body.removeChild(modal);  // Clean up modal from DOM
    }
}

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts(currentPage); 
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

    // Reset the quantity input field to 0
    const quantityInput = document.getElementById(`quantity-${productId}`);
    if (quantityInput) {
        quantityInput.value = 0; // Reset quantity to 0
    }

    // Clear the total price display
    const totalDisplay = document.getElementById(`total-${productId}`);
    if (totalDisplay) {
        totalDisplay.textContent = '0'; // Reset total to 0
    }

    // Uncheck all add-on checkboxes related to the product
    const addonCheckboxes = document.querySelectorAll(`.sauce-addon-${productId}, .drink-addon-${productId}`);
    addonCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });

    // Clear localStorage for selected add-ons for this product
    const storedAddons = JSON.parse(localStorage.getItem('selectedAddons')) || {};
    if (storedAddons[productId]) {
        delete storedAddons[productId];
        localStorage.setItem('selectedAddons', JSON.stringify(storedAddons));
    }

    // Reset the addon quantity dropdowns
    const addonDropdowns = document.querySelectorAll(`.addon-options select[id^="addon-quantity-"]`);
    addonDropdowns.forEach(dropdown => {
        dropdown.value = "0"; // Reset quantity to 0
        dropdown.disabled = true; // Disable the dropdown
        addOption(dropdown, "0", "0"); // Ensure the 0 option is added back
    });

     // Clear add-ons from local storage
     localStorage.removeItem('selectedAddons');
}

function loadCategories() {
    fetch('functions/getCategory.php')
        .then(response => response.json())
        .then(categories => {
            const menu = document.querySelector('.icon-menu');
            menu.innerHTML = '';

            menu.innerHTML += `
                <button class="icon-btn" onclick="selectCategory('')" title="All Products">
                    <i class="fas fa-th"></i><br>All
                </button>
            `;

            categories.forEach(category => {
                const safeCategory = encodeURIComponent(category.toLowerCase());
                const formattedCategory = category.charAt(0).toUpperCase() + category.slice(1);

                menu.innerHTML += `
                    <button class="icon-btn" onclick="selectCategory('${safeCategory}')" title="${formattedCategory}">
                        <i class="fas fa-box"></i><br>${formattedCategory}
                    </button>
                `;
            });
        })
        .catch(error => console.error('Error fetching categories:', error));
}

function selectCategory(category) {
    currentCategory = category;
    fetchProducts(1, '', category);
}


// Call the loadCategories function when the page loads
window.onload = loadCategories;

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



function openNavModal() {
    const navModal = document.getElementById('navModal');
    if (navModal) {
        navModal.style.display = 'block';
    }
}

function closeNavModal() {
    const navModal = document.getElementById('navModal');
    if (navModal) {
        navModal.style.display = 'none';
    }
}

function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    } else {
        console.error(`Section with ID '${sectionId}' not found.`);
    }
}
function openFullImageModal(imageSrc) {
    // Create the full-size image modal
    const fullImageModal = document.createElement('div');
    fullImageModal.classList.add('modal', 'full-image-modal');
    fullImageModal.innerHTML = `
        <div class="modal-content full-image-content">
            <span class="close" onclick="closeFullImageModal()">&times;</span>
            <img src="${imageSrc}" alt="Full Size Image" class="full-size-image">
        </div>
    `;
    
    // Append the full-size image modal to the body
    document.body.appendChild(fullImageModal);

    // Display the full-size image modal
    fullImageModal.style.display = 'block';
}

// Function to close the full-size image modal
function closeFullImageModal() {
    const fullImageModal = document.querySelector('.full-image-modal');
    if (fullImageModal) {
        fullImageModal.remove();
    }
}

</script>
</body>
</html>
