<?php
// Start session and include database connection
session_start();
require_once 'db_connection/db_con.php'; 

// Handle form submissions for adding/updating/deleting staff
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['addStaff'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $role = "staff"; // Only allow Staff role

        $sql = "INSERT INTO admin_access (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $password, $role);
        $stmt->execute();
    }

    if (isset($_POST['updateStaff'])) {
        $customer_id = $_POST['access_id'];
        $username = $_POST['username'];
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $_POST['current_password'];
        $role = "staff"; // Role remains Staff

        $sql = "UPDATE admin_access SET username = ?, password = ? WHERE access_id = ? AND role = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssis', $username, $password, $customer_id, $role);
        $stmt->execute();
    }

    if (isset($_POST['deleteStaff'])) {
        $customer_id = $_POST['access_id'];

        $sql = "DELETE FROM admin_access WHERE access_id = ? AND role = 'Staff'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
    }

    // Redirect to refresh the page and prevent form resubmission
    header("Location: manageStaff.php");
    exit;
}

// Fetch all staff members
$sql = "SELECT * FROM admin_access WHERE role = 'staff'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/manageStaff.css">
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
        <a href="products.php"><i class="fas fa-box"></i> Products</a>
        <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
        <a href="salesReport.php"><i class="fas fa-file-alt"></i> Sales Report</a>
</div>

<div class="content">
    <div class="dashboard-header">
        <h1>Manage Staff</h1>
    </div>
    <div class="add-container">
    <form method="POST" action="">
        <h3>Add New Staff</h3>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="addStaff">Add Staff</button>
    </form>

    <!-- Display Staff Table -->
    <table>
        <thead>
        <tr>
            <th>Access ID</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['access_id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td>
                    <button onclick="editStaff(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="access_id" value="<?php echo $row['access_id']; ?>">
                        <button type="submit" name="deleteStaff">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Edit Staff Modal -->
<div id="editStaffModal" class="modal">
    <div class="modal-content">
        <div class="form-container">
            <form id="editStaffForm" method="POST" action="">
                <input type="hidden" name="access_id" id="editCustomerId">
                
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" name="username" id="editUsername" required>
                </div>
                
                <div class="form-group">
                    <label for="editPassword">Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="editPassword">
                </div>
                
                <input type="hidden" name="current_password" id="currentPassword">
                
                <div class="form-actions">
                    <button type="submit" name="updateStaff" class="save-btn">Update Staff</button>
                    <button type="button" class="cancel-btn" onclick="closeModal('editStaffModal')">Cancel</button>
                </div>
            </form>
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

function editStaff(staff) {
    document.getElementById('editCustomerId').value = staff.access_id; // Correct field name
    document.getElementById('editUsername').value = staff.username;
    document.getElementById('currentPassword').value = staff.password; // Hidden current password
    document.getElementById('editStaffModal').style.display = 'block';
}


function closeModal() {
    document.getElementById('editStaffModal').style.display = 'none';
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