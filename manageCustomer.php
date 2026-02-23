
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
    <link rel="stylesheet" href="css/manage_customer.css">
</head>
<body>
<div class="header">
        <div class="logo">
            <img src="images/logo.jpg" alt="Logo">
        </div>
        <div>
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
            <a href="add_products.php"><i class="fas fa-box"></i> Products</a>
            <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
            <a href="salesReport.php"><i class="fas fa-file-alt"></i> Sales Report</a>
        </div>
       
        <div class="content">
            <div class="dashboard-header">
                <h1>Customer List</h1>
            </div>
   
            <div class="table-container">
    <table id="customersTable" class="display nowrap">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Contact No</th>
                <th>Username</th>
                <th>Password</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="customers-table-body">
            <!-- Rows will be populated by JavaScript -->
        </tbody>
    </table>
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

    <!-- Modal for Delete Confirmation -->
<div id="deleteConfirmationModal" class="modal">
    <div class="modal-content" id="modalContent">
        <h2 id="modalTitle">Confirm Deletion</h2>
        <p id="modalMessage">Are you sure you want to delete this customer?</p>
        <button id="confirmDeleteButton">Delete</button>
        <button id="cancelDeleteButton">Cancel</button>
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
        let currentCustomerId;
const limit = 10;
let dataTable;

function fetchCustomers(page = 1) {
   

    fetch('API/getCustomer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },

    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('customers-table-body');
        tbody.innerHTML = ''; // Clear existing rows

        data.customers.forEach(customer => {
            const row = document.createElement('tr');
            row.id = `customer-${customer.customer_id}`;
            row.innerHTML = `
                <td>${customer.customer_id}</td>
                <td>${customer.firstname}</td>
                <td>${customer.lastname}</td>
                <td>${customer.contact_no}</td>
                <td>${customer.username}</td>
                <td>${customer.password}</td>
                <td>
                    <button class="delete-button" data-id="${customer.customer_id}">
                        Delete <i class="fas fa-trash delete-icon" data-id="${customer.customer_id}"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Reinitialize DataTable
        if (dataTable) {
            dataTable.destroy(); // Destroy any existing DataTable instance
        }
        dataTable = $('#customersTable').DataTable({
            paging: true,         // Enable pagination
            pageLength: 10,       // Rows per page
            lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
            ordering: true,       // Enable sorting
            searching: true,      // Enable search
            info: true,           // Show table info
            autoWidth: false,     // Disable auto column width
            responsive: true      // Make table responsive
        });

        attachEventListeners(); // Reattach event listeners
    })
    .catch(error => console.error('Error fetching customers:', error));
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

        function attachEventListeners() {
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', (event) => {
                    currentCustomerId = event.target.getAttribute('data-id');
                    document.getElementById('deleteConfirmationModal').style.display = 'block';
                });
            });

            document.querySelectorAll('.delete-icon').forEach(icon => {
                icon.addEventListener('click', (event) => {
                    currentCustomerId = event.target.getAttribute('data-id');
                    document.getElementById('deleteConfirmationModal').style.display = 'block';
                });
            });
        }

        document.getElementById('confirmDeleteButton').addEventListener('click', () => {
    fetch('API/deleteCustomer.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ customer_id: currentCustomerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message === 'Customer deleted successfully') {
            // Remove the customer element from the UI
            document.getElementById(`customer-${currentCustomerId}`).remove();
            
            // Update modal content to show success message
            document.getElementById('modalTitle').innerText = 'Success';
            document.getElementById('modalMessage').innerText = 'Customer deleted successfully.';
            document.getElementById('confirmDeleteButton').style.display = 'none';
            document.getElementById('cancelDeleteButton').innerText = 'Close';

            // Automatically close the modal after 3 seconds
            setTimeout(() => {
                document.getElementById('deleteConfirmationModal').style.display = 'none';

                // Reset modal content
                document.getElementById('modalTitle').innerText = 'Confirm Deletion';
                document.getElementById('modalMessage').innerText = 'Are you sure you want to delete this customer?';
                document.getElementById('confirmDeleteButton').style.display = 'inline-block';
                document.getElementById('cancelDeleteButton').innerText = 'Cancel';
            }, 3000); // 3 seconds
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error deleting customer:', error);
        alert('Error deleting customer');
        document.getElementById('deleteConfirmationModal').style.display = 'none';
    });
});

document.getElementById('cancelDeleteButton').addEventListener('click', () => {
    document.getElementById('deleteConfirmationModal').style.display = 'none';
});

// Optional: function to open the modal
function openDeleteConfirmationModal() {
    document.getElementById('deleteConfirmationModal').style.display = 'block';
}

// Fetch customers on page load
fetchCustomers();
    </script>
</body>
</html>