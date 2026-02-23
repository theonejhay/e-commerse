<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: homepage.php");
    exit();
}
$customer_id = $_SESSION['customer_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="css/account.css">
    <title>Account Details</title>
</head>
<body>
<div class="account-container">
    <div class="account-sidebar">
    
        <img src="default_profile.png" alt="Profile Picture">
        <h2>Username Here</h2>
    </div>
    </div>
    </div>
    <button id="update-profile-btn" class="update-profile-btn">Update Account Information</button>
    <div class="account-content">
        <h1>Your Account Information</h1>
        <div id="customer-info">
            <!-- Customer details will be dynamically inserted here -->
        </div>
        </div>

        <h2>Your Orders</h2>
<div id="current-order">
    <!-- Table container with horizontal scroll -->
    <div class="table-wrapper">
    <table id="current-order-table" class="display">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Description</th>
                    <th>Product Total</th>
                    <th>Add-Ons</th>
                    <th>Add-Ons Total</th>
                    <th>Total Amount</th>
                    <th>Pickup Date</th>
                    <th>Pickup Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="current-order-table-body">
                <!-- Orders will be dynamically inserted here -->
            </tbody>
        </table>
    </div>


        <!-- Order History Section -->
<h2>Claimed Orders</h2>
<div id="order-history">
    <!-- Table container with horizontal scroll -->
    <div class="table-wrapper">
    <table id="order-history-table" class="display">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Description</th>
                    <th>Product Total</th>
                    <th>Add-Ons</th>
                    <th>Add-Ons Total</th>
                    <th>Total Amount</th>
                    <th>Pickup Date</th>
                    <th>Pickup Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="order-history-table-body">
                <!-- Orders will be dynamically inserted here -->
            </tbody>
        </table>
    </div>



<h2>Cancelled Orders</h2>
<div id="admin-cancelled-orders">
    <div class="table-wrapper">
    <table id="cancelled-orders-table" class="display">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Username</th>
                    <th>Contact No</th>
                    <th>Description</th>
                    <th>Total Amount</th>
                    <th>Total Orders</th>
                    <th>Confirmation Status</th>
                    <th>Pickup Date</th>
                    <th>Pickup Time</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>ReOrder</th>
                </tr>
            </thead>
            <tbody id="cancelled-orders-table-body">
                <!-- Cancelled orders will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

<div id="rating-modal" style="display: none;">
    <div class="modal-content">
        <h3>Rate Your Order</h3>
        <form id="rating-form" data-order-id="">
            <div>
                <label for="rating-1">
                    <input type="radio" id="rating-1" name="rating" value="1"> Worst
                </label>
                <label for="rating-2">
                    <input type="radio" id="rating-2" name="rating" value="2"> Bad
                </label>
                <label for="rating-3">
                    <input type="radio" id="rating-3" name="rating" value="3"> Good
                </label>
                <label for="rating-4">
                    <input type="radio" id="rating-4" name="rating" value="4"> Better
                </label>
                <label for="rating-5">
                    <input type="radio" id="rating-5" name="rating" value="5"> Excellent
                </label><br>
                <label for="rating-comments">Comments:</label><br>
                <textarea id="rating-comments" placeholder="Enter your comments here..." rows="4" cols="30"></textarea>
                <label for="rating-recommendation">Recommendation:</label><br>
                <textarea  id="rating-recommendation" placeholder="Would you recommend this?"rows="4" cols="30"></textarea>
            </div>
            <button type="button" onclick="submitRating()">Submit Rating</button>
            <button type="button" onclick="closeRatingModal()">Close</button>
        </form>
    </div>
</div>

<!-- Re-order Modal -->
<div id="reorder-modal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5>Re-order Cancelled Order</h5>
            <span class="close-button" id="modal-close">&times;</span>
        </div>
        <div class="custom-modal-body">
            <form id="reorder-form">
                <input type="hidden" id="reorder-order-id" name="order_id">
                <p><strong>Order Details:</strong></p>
                <ul>
                    <li><strong>Description:</strong> <span id="modal-order-description">N/A</span></li>
                    <li><strong>Total Amount:</strong> <span id="modal-order-amount">N/A</span></li>
                    <li><strong>Total Orders:</strong> <span id="modal-order-total">N/A</span></li>
                </ul>
                <p><strong>Pickup Details:</strong></p>
                <label for="modal-pickup-date">Pickup Date:</label>
<input type="date" id="modal-pickup-date" name="pickup_date" readonly required>

<label for="modal-pickup-time">Pickup Time:</label>
<select id="modal-pickup-time" name="pickup_time" required></select>

                <p>Are you sure you want to re-order this item?</p>
            </form>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn-secondary" id="cancel-button">Cancel</button>
            <button type="button" class="btn-primary" id="reorder-confirm-button">Confirm Re-order</button>
        </div>
    </div>
</div>


<!-- Update Profile Modal -->
<div id="update-profile-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update Your Profile</h2>
        <form id="update-profile-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" >
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" >
            </div>

            <div class="form-group">
                <label for="contact_no">Contact Number:</label>
                <input type="text" id="contact_no" name="contact_no" >
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image:</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                <div class="image-preview" id="image-preview">
                    <img id="preview-img" src="" alt="Profile Preview" style="display: none;">
                </div>
            </div>

            <button type="submit" class="update-profile-btn">Update Profile</button>
        </form>
        <div id="update-response">
            <!-- Update response will be shown here -->
        </div>
    </div>
</div>

<script>
// Function to load customer details
function loadCustomerDetails() {
    fetch('API/getAccountInformation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.getElementById('customer-info').innerHTML = `<p>${data.error}</p>`;
        } else {
            let profileImageSrc = 'default_profile.png';
            if (data.profile_image) {
                profileImageSrc = 'data:image/jpeg;base64,' + data.profile_image;
            }
            document.querySelector('.account-sidebar img').src = profileImageSrc;
            document.querySelector('.account-sidebar h2').textContent = data.username;
            
            // Check if <p> element for contact info exists before setting its content
            const contactPara = document.querySelector('.account-sidebar p');
            if (contactPara) {
                contactPara.textContent = data.contact_no;
            }
            
            document.getElementById('customer-info').innerHTML = `
                <div class="account-info-card">
                    <div>
                        <h3>Username</h3>
                        <p>${data.username}</p>
                    </div>
                </div>
                <div class="account-info-card">
                    <div>
                        <h3>Contact No.</h3>
                        <p>${data.contact_no}</p>
                    </div>
                </div>
                <div class="account-info-card">
                    <div>
                        <h3>Password</h3>
                        <p>******</p>
                    </div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('customer-info').innerHTML = `<p>Error loading account information.</p>`;
    });
}

// Modal functionality
var modal = document.getElementById("update-profile-modal");
var btn = document.getElementById("update-profile-btn");
var span = document.getElementsByClassName("close")[0];

// Open the modal when the button is clicked
btn.onclick = function() {
    fetch('API/getAccountInformation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error fetching account information.');
        } else {
            // Pre-fill the form fields with existing user data
            document.getElementById('username').value = data.username || '';
            document.getElementById('password').value = ''; // Leave password empty for security
            document.getElementById('contact_no').value = data.contact_no || '';
        }
        modal.style.display = "block"; // Show the modal after fetching user data
    })
    .catch(error => {
        console.error('Error fetching account details:', error);
        modal.style.display = "block"; // Open the modal even if there's an error
    });
};

// Close the modal when the close button (×) is clicked
span.onclick = function() {
    modal.style.display = "none";
}

// Close the modal when the user clicks anywhere outside of the modal content
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}


function resizeImage(imageFile, maxWidth, maxHeight, callback) {
    const img = new Image();
    const reader = new FileReader();

    reader.onload = function (e) {
        img.onload = function () {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // Set canvas dimensions
            const width = img.width > maxWidth ? maxWidth : img.width;
            const height = img.height > maxHeight ? maxHeight : img.height;

            canvas.width = width;
            canvas.height = height;

            // Draw image on canvas and convert it to base64
            ctx.drawImage(img, 0, 0, width, height);
            const resizedDataUrl = canvas.toDataURL(imageFile.type);
            callback(resizedDataUrl);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(imageFile);
}

// When the user clicks the "Update Profile" button
document.getElementById('update-profile-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const customerId = <?php echo $customer_id; ?>;
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const contactNo = document.getElementById('contact_no').value.trim();
    const profileImage = document.getElementById('profile_image').files[0];

    // Prepare the data object, including only the fields that have been changed
    const data = { customer_id: customerId };

    // Only include updated fields
    if (username) {
        data.username = username;
    }
    if (password) {
        data.password = password;  // This is optional, only included if changed
    }
    if (contactNo) {
        data.contact_no = contactNo;  // This is optional, only included if changed
    }

    // Check if an image was uploaded
    if (profileImage) {
        // Resize the image before sending
        resizeImage(profileImage, 800, 800, function (resizedImage) {
            data.profile_image = {
                name: profileImage.name,
                data: resizedImage.split(',')[1]  // Only send the base64-encoded data
            };
            sendUpdateRequest(data);
        });
    } else {
        // If no image was uploaded, send the rest of the data
        sendUpdateRequest(data);
    }
});

function sendUpdateRequest(data) {
    fetch('API/updateProfile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showNotification('Profile updated successfully!', 3000);
                loadCustomerDetails();  // Reload updated details
            } else {
                showNotification(data.error, 3000);
            }
        } catch (err) {
            console.error('Error parsing JSON:', text);
            showNotification('Error processing the response.', 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating profile.', 3000);
    });
}

// Function to show vanilla notification
function showNotification(message, duration = 3000) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'vanilla-notification';
    notification.innerText = message;

    // Append notification to the body
    document.body.appendChild(notification);

    // Show the notification with animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10); // Slight delay for transition

    // Hide and remove the notification after a specific duration
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 500); // Wait for the hide animation
    }, duration);
}



// Image preview functionality
document.getElementById('profile_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewImg = document.getElementById('preview-img');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block'; // Show the preview image
        };
        reader.readAsDataURL(file); // Convert image to base64 string
    } else {
        previewImg.style.display = 'none'; // Hide the preview image if no file is selected
    }
});

let dataTable = null; // Declare a global DataTable variable

function loadCurrentOrders(page = 1, limit = 10) {
    fetch(`API/currentOrders.php?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }

            const orders = data.orders.map(order => {
                let orderTotal = parseFloat(order.total_amount) || 0;
                let addonsTotal = 0;
                let addonDetails = '';

                if (order.addons && order.addons.length > 0) {
                    addonDetails = order.addons.map(addon => {
                        const addonCost = parseFloat(addon.addon_price) * addon.addon_quantity;
                        addonsTotal += addonCost;
                        return `${addon.addon_name} (${addon.addon_type}) x${addon.addon_quantity} - ₱${addonCost.toFixed(2)}`;
                    }).join('<br>');
                } else {
                    addonDetails = 'N/A';
                }

                const overallTotal = orderTotal + addonsTotal;
                const pickupTime = order.pickup_time ? formatTimeToAMPM(order.pickup_time) : 'N/A';

                return [
                    order.order_id,
                    order.description,
                    `₱ ${orderTotal.toFixed(2)}`,
                    addonDetails,
                    `₱ ${addonsTotal.toFixed(2)}`,
                    `₱ ${overallTotal.toFixed(2)}`,
                    order.pickup_date || 'N/A',
                    pickupTime,
                    `<span class="confirmation-status">${order.confirmation_status}</span>`,
                    `<button 
                        class="rate-button" 
                        data-id="${order.order_id}" 
                        ${order.isRated ? 'disabled' : ''}
                    >
                        ${order.isRated ? 'Rated' : 'Rate'}
                    </button>`
                ];
            });

            // Initialize or reinitialize DataTable
            const table = $('#current-order-table'); // Use jQuery for DataTables
            if (!dataTable) {
                dataTable = table.DataTable({
                    data: orders,
                    columns: [
                        { title: "Order ID" },
                        { title: "Description" },
                        { title: "Order Total" },
                        { title: "Add-ons" },
                        { title: "Add-ons Total" },
                        { title: "Overall Total" },
                        { title: "Pickup Date" },
                        { title: "Pickup Time" },
                        { title: "Status" },
                        { title: "Action" }
                    ],
                    paging: true,
                    pageLength: limit,
                    lengthMenu: [5, 10, 25, 50],
                    ordering: true,
                    searching: true,
                    info: true,
                    autoWidth: false,
                    responsive: true
                });
            } else {
                dataTable.clear().rows.add(orders).draw();
            }

            attachEventListeners(); // Reattach event listeners
        })
        .catch(error => {
            console.error('Error loading current orders:', error);
        });
}


// Helper Function: Format Time to AM/PM
function formatTimeToAMPM(time) {
    const [hour, minute] = time.split(':').map(Number);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const formattedHour = hour % 12 || 12;
    return `${formattedHour}:${minute.toString().padStart(2, '0')} ${ampm}`;
}

// Attach Event Listeners to Buttons
function attachEventListeners() {
    document.querySelectorAll('.rate-button').forEach(button => {
        button.addEventListener('click', event => {
            const orderId = event.target.dataset.id;
            openRatingModal(orderId);
        });
    });
}


let orderHistoryDataTable = null; // Declare a global variable for the DataTable instance

function loadOrderHistory(page = 1, limit = 10) {
    fetch(`API/orderHistory.php?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#order-history-table tbody');
            tbody.innerHTML = '';

            if (data.error) {
                tbody.innerHTML = `<tr><td colspan="10">${data.error}</td></tr>`;
            } else {
                const orders = data.orders;

                orders.forEach(order => {
                    let orderTotal = parseFloat(order.total_amount) || 0;
                    let addonsTotal = 0;
                    let addonDetails = 'N/A';

                    if (order.addons && order.addons.length > 0) {
                        addonDetails = order.addons.map(addon => {
                            const addonCost = parseFloat(addon.total_addon_price);
                            addonsTotal += addonCost;
                            return `${addon.addon_name} (${addon.addon_type}) x${addon.addon_quantity} - ₱${addonCost.toFixed(2)}`;
                        }).join('<br>');
                    }

                    const overallTotal = orderTotal + addonsTotal;
                    const formattedTime = formatTimeToAMPM(order.pickup_time);

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${order.order_id}</td>
                        <td>${order.description}</td>
                        <td>₱${orderTotal.toFixed(2)}</td>
                        <td>${addonDetails}</td>
                        <td>₱${addonsTotal.toFixed(2)}</td>
                        <td>₱${overallTotal.toFixed(2)}</td>
                        <td>${order.pickup_date}</td>
                        <td>${formattedTime}</td>
                        <td>${order.status}</td>
                        <td>
                            <button 
                                class="rate-button" 
                                data-id="${order.order_id}" 
                                ${order.isRated ? 'disabled' : ''}
                            >
                                ${order.isRated ? 'Rated' : 'Rate'}
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Initialize or reinitialize DataTable
                const table = $('#order-history-table'); // Use jQuery for DataTables
                if (orderHistoryDataTable) {
                    orderHistoryDataTable.destroy(); // Destroy the previous DataTable instance
                }
                orderHistoryDataTable = table.DataTable({
                    paging: true,         // Enable pagination
                    pageLength: limit,    // Rows per page
                    lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
                    ordering: true,       // Enable sorting
                    searching: true,      // Enable search
                    info: true,           // Show table info
                    autoWidth: false,     // Disable auto column width
                    responsive: true      // Make table responsive
                });

                attachEventListeners(); // Reattach event listeners for dynamic buttons
            }
        })
        .catch(error => {
            console.error('Error loading order history:', error);
        });
}

// Helper Function: Format Time to AM/PM
function formatTimeToAMPM(time) {
    const [hour, minute] = time.split(':').map(Number);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const formattedHour = hour % 12 || 12;
    return `${formattedHour}:${minute.toString().padStart(2, '0')} ${ampm}`;
}

// Attach Event Listeners to Buttons
function attachEventListeners() {
    document.querySelectorAll('.rate-button').forEach(button => {
        button.addEventListener('click', event => {
            const orderId = event.target.dataset.id;
            openRatingModal(orderId);
        });
    });
}

let cancelledOrdersDataTable = null; // Declare a global variable for the DataTable instance

function loadAdminCancelledOrders(page = 1, limit = 10) {
    fetch(`API/adminCancelledOrders.php?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#cancelled-orders-table tbody');
            tbody.innerHTML = ''; // Clear the table body

            if (data.error) {
                tbody.innerHTML = `<tr><td colspan="12">${data.error}</td></tr>`;
            } else {
                const orders = data.orders;

                // Append rows dynamically
                orders.forEach(order => {
                    const formattedTime = formatTimeToAMPM(order.pickup_time);

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${order.order_id}</td>
                        <td>${order.username}</td>
                        <td>${order.contact_no}</td>
                        <td>${order.description}</td>
                        <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
                        <td>${order.total_orders}</td>
                        <td>${order.confirmation_status}</td>
                        <td>${order.pickup_date}</td>
                        <td>${formattedTime}</td>
                        <td>${order.remarks || 'N/A'}</td>
                        <td>${order.status}</td>
                        <td><button class="btn btn-primary reorder-button" data-order-id="${order.order_id}">Re-order</button></td>
                    `;
                    tbody.appendChild(tr);
                });

                // Initialize or reinitialize DataTable
                const table = $('#cancelled-orders-table');
                if (cancelledOrdersDataTable) {
                    cancelledOrdersDataTable.destroy();
                }
                cancelledOrdersDataTable = table.DataTable({
                    paging: true,
                    pageLength: limit,
                    lengthMenu: [5, 10, 25, 50],
                    ordering: true,
                    searching: true,
                    info: true,
                    autoWidth: false,
                    responsive: true
                });

                // Attach event listeners for reorder buttons
                document.querySelectorAll('.reorder-button').forEach(button => {
                    button.addEventListener('click', function () {
                        const orderId = this.getAttribute('data-order-id');
                        openReorderModal(orderId);
                    });
                });
            }
        })
        .catch(error => console.error('Error loading admin cancelled orders:', error));
}

function openReorderModal(orderId) {
    // Fetch the details of the cancelled order
    fetch(`API/getAdminCancelledOrders.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(order => {
            if (order.error) {
                alert(`Error: ${order.error}`);
            } else {
                // Populate modal fields with the order details
                document.getElementById('reorder-order-id').value = order.order_id;
                document.getElementById('modal-order-description').textContent = order.description || 'N/A';
                document.getElementById('modal-order-amount').textContent = `₱${parseFloat(order.total_amount).toFixed(2)}`;
                document.getElementById('modal-order-total').textContent = order.total_orders || 'N/A';
            }

            // Show the modal
            const modal = document.getElementById('reorder-modal');
            modal.style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            alert('An unexpected error occurred while fetching order details.');
        });
}


function closeReorderModal() {
    const modal = document.getElementById('reorder-modal');
    modal.style.display = 'none'; // Hide the modal
}

// Attach event listeners
document.getElementById('modal-close').addEventListener('click', closeReorderModal);
document.getElementById('cancel-button').addEventListener('click', closeReorderModal);
document.getElementById('reorder-confirm-button').addEventListener('click', function () {
    const orderId = document.getElementById('reorder-order-id').value;
    const pickupDate = document.getElementById('modal-pickup-date').value;
    const pickupTime = document.getElementById('modal-pickup-time').value;

    if (!pickupDate || !pickupTime) {
        alert('Please select a valid pickup date and time.');
        return;
    }

    fetch('API/reOrder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            order_id: orderId,
            pickup_date: pickupDate,
            pickup_time: pickupTime
        })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Order successfully re-ordered!');
                closeReorderModal();
                loadAdminCancelledOrders(); // Reload the table
            } else {
                alert('Failed to re-order: ' + result.error);
            }
        })
        .catch(error => console.error('Error re-ordering:', error));
});

document.querySelectorAll('.reorder-button').forEach(button => {
    button.addEventListener('click', function () {
        const orderId = this.getAttribute('data-order-id');
        openReorderModal(orderId);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('modal-pickup-date');
    const timeSelect = document.getElementById('modal-pickup-time');

    // Set today's date as the only option for the date input
    const today = new Date();
    const todayDateString = today.toISOString().split('T')[0];
    dateInput.value = todayDateString;
    dateInput.min = todayDateString;
    dateInput.max = todayDateString;

    // Populate time options dynamically
    populateTimeOptions();

    function populateTimeOptions() {
        timeSelect.innerHTML = ''; // Clear existing options

        const startHour = 6; // 6 AM
        const endHour = 19; // 7 PM (24-hour format)
        const intervalMinutes = 25; // 25-minute intervals

        for (let hour = startHour; hour <= endHour; hour++) {
            for (let minute = 0; minute < 60; minute += intervalMinutes) {
                const optionTime = new Date();
                optionTime.setHours(hour, minute, 0, 0);

                // If the time is within the allowed hours, create the option
                const displayHour = hour % 12 || 12; // Convert to 12-hour format
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const formattedTime = `${displayHour}:${minute.toString().padStart(2, '0')} ${ampm}`;

                const option = document.createElement('option');
                option.value = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                option.textContent = formattedTime;

                timeSelect.appendChild(option);
            }
        }
    }

    // Disable manual changes to the date field
    dateInput.addEventListener('input', () => {
        dateInput.value = todayDateString; // Reset to today's date if changed
    });
});


// Helper Function: Format Time to AM/PM
function formatTimeToAMPM(time) {
    const [hour, minute] = time.split(':').map(Number);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const formattedHour = hour % 12 || 12;
    return `${formattedHour}:${minute.toString().padStart(2, '0')} ${ampm}`;
}

function submitRating() {
    const orderId = document.getElementById('rating-form').getAttribute('data-order-id');
    
    if (!orderId || isNaN(orderId)) {
        alert('Invalid order ID.');
        return;
    }

    const ratingInput = document.querySelector('input[name="rating"]:checked');
    if (!ratingInput) {
        alert('Please select a rating before submitting.');
        return;
    }

    const ratingValue = ratingInput.value;
    const comments = document.getElementById('rating-comments').value.trim();
    const recommendation = document.getElementById('rating-recommendation').value.trim();

    const data = {
        order_id: orderId,
        rating_value: ratingValue,
        comments: comments,
        recommendation: recommendation
    };

    fetch('API/submitRating.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rating submitted successfully, thank you for your feedback.');

            // Update the corresponding button in the DOM
            const button = document.querySelector(`.rate-button[data-id="${orderId}"]`);
            if (button) {
                button.textContent = 'Rated'; // Change button text
                button.disabled = true;      // Disable the button
            }

            closeRatingModal();
        } else if (data.error === 'Order already rated.') {
            alert('This order has already been rated.');
        } else {
            alert('Error submitting rating: ' + (data.error || 'Unknown error.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the rating.');
    });
}


function openRatingModal(orderId) {
    // First, check if the order has already been rated
    fetch(`API/checkRatedOrder.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.error === 'Order already rated.') {
                alert('This order has already been rated.');
            } else {
                // If not rated, open the modal to submit a rating
                document.getElementById('rating-modal').style.display = 'block';
                document.getElementById('rating-form').setAttribute('data-order-id', orderId);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while checking if the order has been rated.');
        });
}

function closeRatingModal() {
    document.getElementById('rating-modal').style.display = 'none';
}

// Load customer details and order history on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerDetails();
    loadOrderHistory();
    loadCurrentOrders();
    loadAdminCancelledOrders();
});

// Refresh interval in milliseconds
const refreshInterval = 5000; // 10 seconds

// Set intervals to refresh each table
setInterval(() => {
    loadCurrentOrders();
}, refreshInterval);

setInterval(() => {
    loadOrderHistory();
}, refreshInterval);

setInterval(() => {
    loadAdminCancelledOrders();
}, refreshInterval);

</script>
</body>
</html>
