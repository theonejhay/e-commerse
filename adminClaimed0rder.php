
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                <h1>Claimed Orders</h1>
            </div>
            <button id="deleteAllButton" style="background-color: red; color: white; border: none; border-radius: 5px; padding: 10px; cursor: pointer; margin-bottom: 10px;">Delete All Claimed Orders</button>
            <div class="filter">
    <label for="startDate">Start Date:</label>
    <input type="text" id="startDate" placeholder="Select Start Date">

    <label for="endDate">End Date:</label>
    <input type="text" id="endDate" placeholder="Select End Date">

    <button id="applyFilter">Apply Filter</button>
    <button id="clearFilter">Clear Filter</button> <!-- Clear Button -->
</div>

<div class="dashboard-orders">
    <table id="ordersTable">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Username</th>
                <th>Contact No</th>
                <th>Description</th>
                <th>Total Orders</th>
                <th>Product Total</th>
                <th>Add-Ons</th>
                <th>Add-Ons Total</th>
                <th>Order Total</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
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
    let ordersDataTable; // Declare ordersDataTable globally

async function fetchOrders(search = '', startDate = '', endDate = '') {
    try {
        const response = await fetch(
            `API/getClaimedOrder.php?search=${encodeURIComponent(search)}&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`
        );
        const data = await response.json();

        // Handle API errors
        if (data.error) {
            console.error(data.error);
            return;
        }

        // Clear existing table rows
        const tableBody = document.querySelector('.dashboard-orders table tbody');
        tableBody.innerHTML = '';

        // Check if data is available
        if (data.orders && data.orders.length > 0) {
            data.orders.forEach(order => {
                // Create a new table row
                const row = document.createElement('tr');

                // Function to convert time to 12-hour format
                const formatTimeTo12Hour = (time) => {
                    if (!time) return '-';
                    const [hour, minute] = time.split(':');
                    const hour12 = (hour % 12) || 12;
                    const ampm = hour >= 12 ? 'PM' : 'AM';
                    return `${hour12}:${minute} ${ampm}`;
                };

                // Calculate the total price for addons
                const addons = order.addons || [];
                const addonTotal = addons.reduce((sum, addon) => {
                    const price = parseFloat(addon.addon_price) || 0;
                    const quantity = parseInt(addon.quantity) || 0;
                    return sum + (price * quantity);
                }, 0);

                // Calculate the total order amount
                const baseAmount = parseFloat(order.total_amount) || 0;
                const totalOrderAmount = baseAmount + addonTotal;

                // Fill in the table row
                row.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.customer_id || '-'}</td>
                    <td>${order.username || '-'}</td>
                    <td>${order.contact_no || '-'}</td>
                    <td>${order.description || '-'}</td>
                    <td>${order.total_orders || 0}</td>
                    <td>${baseAmount.toFixed(2)}</td>
                    <td>${addons.map(addon => `${addon.addon_name} (x${addon.quantity})`).join(', ') || '-'}</td>
                    <td>${addonTotal.toFixed(2)}</td>
                    <td>${totalOrderAmount.toFixed(2)}</td>
                    <td>${order.pickup_date || '-'}</td>
                    <td>${formatTimeTo12Hour(order.pickup_time)}</td>
                    <td>${order.status || '-'}</td>
                `;

                // Append the row to the table body
                tableBody.appendChild(row);
            });

            // Reinitialize DataTable if it exists
            if (ordersDataTable) {
                ordersDataTable.destroy(); // Destroy existing instance
            }

            // Initialize or reinitialize the DataTable
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
            tableBody.innerHTML = '<tr><td colspan="12">No orders found.</td></tr>';
        }
    } catch (error) {
        console.error('Error fetching orders:', error);
    }
}



    // Function to update pagination (if needed)
    function updatePagination(total) {
        paginationDiv.innerHTML = `Total Records: ${total}`;
    }

    // Fetch initial data
    fetchOrders();

    // Add search functionality (if you have a search bar)
    const searchInput = document.getElementById('searchInput'); // Assuming you have an input with id 'searchInput'
    searchInput?.addEventListener('input', () => fetchOrders(searchInput.value));

    // Add apply filter functionality (when Apply Filter button is clicked)
    applyFilterButton.addEventListener('click', () => {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        // Fetch orders with the selected date range
        fetchOrders('', startDate, endDate);
    });

    // Add clear filter functionality (when Clear Filter button is clicked)
    clearFilterButton.addEventListener('click', () => {
        // Clear the date inputs
        startDateInput.value = '';
        endDateInput.value = '';

        // Fetch orders without any date filter
        fetchOrders();
    });
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
            fetch('functions/deleteAllClaimedOrders.php', {
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
