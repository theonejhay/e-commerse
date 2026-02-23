
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="css/all_order.css">
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
        <a href="products.php"><i class="fas fa-box"></i> Products</a>
        
            <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
        </div>
        <div class="content">
            <div class="dashboard-header">
                <h1>Cancelled Orders</h1>
            </div>
            <button id="deleteAllButton" style="background-color: red; color: white; border: none; border-radius: 5px; padding: 10px; cursor: pointer; margin-bottom: 10px;">Delete All Cancelled Orders</button>

            <div class="filter">
    <label for="startDate">Start Date:</label>
    <input type="text" id="startDate" placeholder="Select Start Date">

    <label for="endDate">End Date:</label>
    <input type="text" id="endDate" placeholder="Select End Date">

    <button id="applyFilter">Apply Filter</button>
    <button id="clearFilter">Clear</button>
</div>

<div class="dashboard-orders">
    <table class="display">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Username</th>
                <th>Contact.no</th>
                <th>Description</th>
                <th>Total Orders</th>
                <th>Product Total</th>
                <th>Add-Ons</th> 
                <th>Add-Ons Total</th>
                <th>Orders Total</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
                <th>Remarks</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically inserted here -->
        </tbody>
    </table>
</div>

<!-- Modal for Delete All Orders Confirmation -->
<div id="deleteAllModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('closeDeleteAllModal')">&times;</span>
            <p>By Pressing delete you will now clear all the records stored in the table</p>
            <button id="confirmDeleteAllButton">Delete</button>
            <div id="deleteAllMessage" class="modal-message"></div>
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

document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('table tbody'); // Reference to the table body
    const paginationDiv = document.getElementById('pagination'); // Reference to pagination div
    const startDateInput = document.getElementById('startDate'); // Start date input
    const endDateInput = document.getElementById('endDate'); // End date input
    const applyFilterButton = document.getElementById('applyFilter'); // Apply filter button
    const clearFilterButton = document.getElementById('clearFilter'); // Clear filter button

    // Initialize Flatpickr on the date inputs
    flatpickr(startDateInput, {
        dateFormat: 'Y-m-d', // Format of the date (ISO format)
        allowInput: true, // Allow input alongside the calendar
    });

    flatpickr(endDateInput, {
        dateFormat: 'Y-m-d', // Format of the date (ISO format)
        allowInput: true, // Allow input alongside the calendar
    });

    let ordersDataTable; // Variable to hold the DataTable instance

    async function fetchOrders(search = '', startDate = '', endDate = '') {
    try {
        const response = await fetch(
            `API/getAdminCancelledOrders.php?search=${encodeURIComponent(search)}&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`
        );
        const data = await response.json();

        const tbody = document.querySelector('.dashboard-orders table tbody');
        if (!tbody) {
            throw new Error("Element with the expected structure is not found.");
        }

        tbody.innerHTML = ''; // Clear the table body

        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(order => {
                let orderTotal = parseFloat(order.total_amount || 0);
                let addonsTotal = 0;
                let addonDetails = '';

                if (order.addons && order.addons.length > 0) {
                    addonDetails = order.addons.map(addon => {
                        const addonPrice = parseFloat(addon.addon_price || 0);
                        const addonQuantity = parseInt(addon.quantity || 0, 10);
                        const addonCost = addonPrice * addonQuantity;
                        addonsTotal += addonCost;
                        return `${addon.addon_name} (${addon.addon_type || 'N/A'}) x${addonQuantity} - ₱${addonCost.toFixed(2)}`;
                    }).join('<br>'); // Line break for readability
                } else {
                    addonDetails = 'N/A';
                }

                const totalOrder = orderTotal + addonsTotal;
                const pickupTime = order.pickup_time ? convertTo12HourFormat(order.pickup_time) : 'N/A';

                const tr = document.createElement('tr');
                tr.dataset.order_id = order.order_id; // Store order ID for operations
                tr.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.customer_id || 'N/A'}</td>
                    <td>${order.username || 'N/A'}</td>
                    <td>${order.contact_no || 'N/A'}</td>
                    <td>${order.description || 'N/A'}</td>
                    <td>${order.total_orders || 0}</td>
                    <td>₱ ${orderTotal.toFixed(2)}</td>
                    <td>${addonDetails}</td>
                    <td>₱ ${addonsTotal.toFixed(2)}</td>
                    <td>₱ ${totalOrder.toFixed(2)}</td>
                    <td>${order.pickup_date || 'N/A'}</td>
                    <td>${pickupTime}</td>
                    <td>${order.remarks || 'N/A'}</td>
                    <td>${order.status || 'N/A'}</td>
                `;
                tbody.appendChild(tr);
            });

            // Reinitialize DataTable
            if (ordersDataTable) {
                ordersDataTable.destroy(); // Destroy existing instance
            }
            ordersDataTable = $('.dashboard-orders table').DataTable({
                paging: true,         // Enable pagination
                pageLength: 10,       // Rows per page
                lengthMenu: [5, 10, 25, 50], // Options for rows per page
                ordering: true,       // Enable sorting
                searching: true,      // Enable search
                info: true,           // Show table info
                autoWidth: false,     // Disable auto column width
                responsive: true      // Make the table responsive
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="14">No orders found.</td></tr>';
        }
    } catch (error) {
        console.error('Error fetching orders:', error);
    }
}

// Helper function to convert time to 12-hour format
function convertTo12HourFormat(time) {
    if (!time) return 'N/A';
    const [hour, minute] = time.split(':');
    const hour12 = (hour % 12) || 12;
    const ampm = hour >= 12 ? 'PM' : 'AM';
    return `${hour12}:${minute} ${ampm}`;
}


    // Function to update pagination (if required)
    function updatePagination(totalOrders) {
        const itemsPerPage = 10; // Example number of items per page
        const totalPages = Math.ceil(totalOrders / itemsPerPage);
        paginationDiv.innerHTML = ''; // Clear existing pagination buttons

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.innerText = i;
            pageButton.addEventListener('click', () => {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                fetchOrders('', startDate, endDate, i); // Call fetchOrders with page number
            });
            paginationDiv.appendChild(pageButton);
        }
    }

    // Event listener for apply filter button
    applyFilterButton.addEventListener('click', () => {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        // Fetch orders with date filters
        fetchOrders('', startDate, endDate);
    });

    // Event listener for clear filter button
    clearFilterButton.addEventListener('click', () => {
        // Clear the date inputs
        startDateInput.value = '';
        endDateInput.value = '';

        // Fetch orders without date filters
        fetchOrders();
    });

    // Initial fetch (optional: add a default search or date filter here)
    fetchOrders();
});

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



        // Handle Delete All Orders button click to show confirmation modal
        document.getElementById('deleteAllButton').addEventListener('click', function() {
            document.getElementById('deleteAllModal').style.display = 'block';
        });

        // Handle modal close button click
        document.querySelectorAll('.close').forEach(element => {
            element.addEventListener('click', function() {
                document.getElementById('deleteAllModal').style.display = 'none';
            });
        });

        // Handle confirmation of delete all orders
        document.getElementById('confirmDeleteAllButton').addEventListener('click', function() {
            fetch('functions/deleteAllCancelledOrders.php', {
                    method: 'POST'
                })
            .then(response => response.text())
            .then(data => {
                document.getElementById('deleteAllMessage').style.display = 'block';
                document.getElementById('deleteAllMessage').innerText = data;
                setTimeout(() => {
                    document.getElementById('deleteAllModal').style.display = 'none';
                    document.getElementById('deleteAllMessage').style.display = 'none';
                }, 3000);
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
