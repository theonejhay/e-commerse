
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
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
        </div>
        <div class="content">
            <div class="dashboard-header">
                <h1>Orders</h1>
            </div>
            <div class="dashboard-orders">
            <table id="ordersTable">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Mobile no.</th>
            <th>Description</th>
            <th>Total Orders</th>
            <th>Base Total</th>
            <th>Addon Details</th>
            <th>Addon Total</th>
            <th>Grand Total</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
            <th>Confirmation Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
            <!-- Data will be dynamically inserted here -->
        </tbody>
    </table>
</div>

    <!-- Modal for Confirmation Status -->
    <div id="confirmationStatusModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeStatusModal">&times;</span>
            <h2>Update Confirmation Status</h2>
            <select id="confirmationStatusSelect">
                <option value="confirmed">Confirm</option>
                <option value="ready-to-claim">Ready to Claim</option>
                <option value="cooking">Cooking</option>
            </select>
            <button id="saveStatusButton">Save</button>
        </div>
    </div>

<!-- Modal for Delete Confirmation -->
<div id="deleteConfirmationModal" class="modal">
    <div class="modal-content">
        <h2>Confirm Deletion</h2>
        <p>Please select a reason for cancelling:</p>
        
        <!-- Radio options for cancellation reasons -->
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
            <!-- Hidden input for custom reason -->
            <textarea id="otherReasonInput" name="customReason" placeholder="Please specify your reason..." style="display: none; width: 100%; margin-top: 10px;"></textarea>
        </form>

        <button id="confirmDeleteButton">Cancel</button>
        <button id="cancelDeleteButton">Close</button>
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

    <!-- Modal for Delete All Orders Confirmation -->
    <div id="deleteAllModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('closeDeleteAllModal')">&times;</span>
            <p>By Pressing delete you will now clear all the records stored in the table</p>
            <button id="confirmDeleteAllButton">Delete</button>
            <div id="deleteAllMessage" class="modal-message"></div>
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

let dataTable;

function fetchOrders() {
    fetch(`API/getOrderMain.php`)
        .then(response => {
            if (!response.ok) {
                return Promise.reject('Failed to fetch data');
            }
            return response.json();
        })
        .then(data => {
            if (!data || !data.orders || data.orders.length === 0) {
                if ($.fn.dataTable.isDataTable('#ordersTable')) {
                    const dataTable = $('#ordersTable').DataTable();
                    dataTable.clear().draw(); // Clear table if no data
                }
                return;
            }

            const orders = data.orders;

            const formattedOrders = orders.map(order => {
                const orderTotal = parseFloat(order.total_amount);
                let addonsTotal = 0;
                let addonDetails = '';

                if (order.addons && order.addons.length > 0) {
                    addonDetails = order.addons.map(addon => {
                        const addonCost = parseFloat(addon.addon_price) * addon.addon_quantity;
                        addonsTotal += addonCost;
                        return `${addon.addon_name} (${addon.addon_type}) x${addon.addon_quantity} - ₱${addonCost.toFixed(2)}`;
                    }).join(', ');
                } else {
                    addonDetails = 'N/A';
                }

                const totalOrder = orderTotal + addonsTotal;
                const pickupTime = order.pickup_time ? convertTo12HourFormat(order.pickup_time) : 'N/A';

                return [
                    order.order_id,
                    order.customer_id,
                    `${order.firstname} ${order.lastname}`,
                    order.contact_no,
                    order.description,
                    order.total_orders,
                    `₱ ${orderTotal.toFixed(2)}`,
                    addonDetails,
                    `₱ ${addonsTotal.toFixed(2)}`,
                    `₱ ${totalOrder.toFixed(2)}`,
                    order.pickup_date || 'N/A',
                    pickupTime,
                    `${order.confirmation_status} 
                        <button class="update-status-button" data-id="${order.order_id}">Update</button>`,
                    `<button class="claim-button" data-id="${order.order_id}">Claim</button>
                        <button class="delete-button" data-id="${order.order_id}">Cancel</button>`
                ];
            });

            // Initialize or update the DataTable
            let dataTable;
            if ($.fn.dataTable.isDataTable('#ordersTable')) {
                dataTable = $('#ordersTable').DataTable();
                dataTable.clear(); // Clear existing data
                dataTable.rows.add(formattedOrders); // Add new rows
                dataTable.draw(); // Redraw the table
            } else {
                dataTable = $('#ordersTable').DataTable({
                    data: formattedOrders,
                    columns: [
                        { title: 'Order ID' },
                        { title: 'Customer ID' },
                        { title: 'Customer Name' },
                        { title: 'Contact No' },
                        { title: 'Description' },
                        { title: 'Total Orders' },
                        { title: 'Order Total' },
                        { title: 'Addon Details' },
                        { title: 'Addons Total' },
                        { title: 'Total Order' },
                        { title: 'Pickup Date' },
                        { title: 'Pickup Time' },
                        { title: 'Confirmation Status' },
                        { title: 'Actions' }
                    ],
                    paging: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50],
                    ordering: true,
                    searching: true,
                    info: true,
                    autoWidth: false,
                    responsive: true
                });
            }

            attachEventListeners(); // Reattach event listeners
        })
        .catch(error => {
            console.error('Error fetching order data:', error);
        });
}


// Call fetchOrders periodically
setInterval(fetchOrders, 5000); // Refresh every 5 seconds

// Function to convert time to 12-hour format
function convertTo12HourFormat(time24) {
    const [hours, minutes] = time24.split(':');
    let hours12 = (hours % 12) || 12;
    let amPm = hours < 12 ? 'AM' : 'PM';
    return `${hours12}:${minutes} ${amPm}`;
}

let currentPage = 1;
    function claimOrder(orderId) {
    fetch(`API/claimedOrders.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ order_id: orderId }),
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            alert(`Order ${orderId} successfully claimed!`);
            // Optionally, refresh the table or remove the claimed order from the current table
            fetchOrders(currentPage); // Assuming fetchOrders is the function to reload the order data
        } else {
            alert('Failed to claim the order.');
        }
    })
    .catch(error => console.error('Error claiming order:', error));
}

    fetchOrders();

        function attachEventListeners() {
            document.querySelectorAll('.update-status-button').forEach(button => {
                button.addEventListener('click', (event) => {
                    currentOrderId = event.target.getAttribute('data-id');
                    document.getElementById('confirmationStatusModal').style.display = 'block';
                });
            });

            document.querySelectorAll('.update-request-button').forEach(button => {
                button.addEventListener('click', (event) => {
                    currentOrderId = event.target.getAttribute('data-id');
                    document.getElementById('confirmRequestModal').style.display = 'block';
                });
            });

            // Add event listener for the claim buttons
            document.querySelectorAll('.claim-button').forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.dataset.id;
                    claimOrder(orderId);
                });
            });

            document.querySelectorAll('.delete-button, .delete-icon').forEach(element => {
    element.addEventListener('click', (event) => {
        currentOrderId = event.target.getAttribute('data-id');
        document.getElementById('deleteConfirmationModal').style.display = 'block';
    });
});

// Handle the delete confirmation
document.getElementById('confirmDeleteButton').addEventListener('click', () => {
    const selectedReason = document.querySelector('input[name="cancelReason"]:checked');
    if (selectedReason) {
        const cancelReason = selectedReason.value;
        // Add logic here to delete the order, passing the `currentOrderId` and `cancelReason` if needed
        document.getElementById('deleteConfirmationModal').style.display = 'none';
    } else {
        alert('Please select a reason for cancelling.');
    }
});

// Show/hide the "Other" input based on selection
document.querySelectorAll('input[name="cancelReason"]').forEach(radio => {
    radio.addEventListener('change', (event) => {
        const otherReasonInput = document.getElementById('otherReasonInput');
        if (event.target.value === 'Other') {
            otherReasonInput.style.display = 'block';
            otherReasonInput.setAttribute('required', 'true');
        } else {
            otherReasonInput.style.display = 'none';
            otherReasonInput.removeAttribute('required');
        }
    });
});


// Handle the cancel button
document.getElementById('cancelDeleteButton').addEventListener('click', () => {
    document.getElementById('deleteConfirmationModal').style.display = 'none';
});

        }


        // Handle confirmation of delete all orders
        document.getElementById('confirmDeleteAllButton').addEventListener('click', function() {
            fetch('functions/deleteAllOrders.php', {
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

        document.getElementById('saveStatusButton').addEventListener('click', () => {
            const status = document.getElementById('confirmationStatusSelect').value;
            fetch('API/updateConfirmation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: currentOrderId, confirmation_status: status })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('confirmationStatusModal').style.display = 'none';
                updateTableCell(currentOrderId, 'confirmation-status', status);
            })
            .catch(error => console.error('Error updating status:', error));
        });


document.getElementById('confirmDeleteButton').addEventListener('click', () => {
    const selectedReason = document.querySelector('input[name="cancelReason"]:checked');
    if (selectedReason) {
        let cancelReason = selectedReason.value;
        if (cancelReason === 'Other') {
            const otherReasonInput = document.getElementById('otherReasonInput');
            cancelReason = otherReasonInput.value.trim();
            if (!cancelReason) {
                alert('Please specify your reason for selecting "Other".');
                return;
            }
        }

        // Make API call with cancellation reason
        fetch(`API/cancelledOrder.php?order_id=${currentOrderId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ remarks: cancelReason }) // Pass the reason
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === "Order deleted successfully") {
                document.getElementById('deleteConfirmationModal').style.display = 'none';
                removeTableRow(currentOrderId);
                
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error deleting order:', error));
    } else {
        alert('Please select a reason for cancelling.');
    }
});


        document.getElementById('cancelDeleteButton').addEventListener('click', () => {
            document.getElementById('deleteConfirmationModal').style.display = 'none';
        });

        document.querySelectorAll('.close').forEach(closeBtn => {
            closeBtn.addEventListener('click', () => {
                closeBtn.parentElement.parentElement.style.display = 'none';
            });
        });

        function updateTableCell(order_id, className, newValue) {
            const row = document.querySelector(`tr[data-order_id="${order_id}"]`);
            if (row) {
                const cell = row.querySelector(`.${className}`);
                if (cell) {
                    cell.innerHTML = `${newValue} <button class="update-status-button" data-id="${order_id}">Update</button>`;
                    attachEventListeners();
                }
            }
        }

        function removeTableRow(order_id) {
            const row = document.querySelector(`tr[data-order_id="${order_id}"]`);
            if (row) {
                row.remove();
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

    </script>
</body>
</html>
