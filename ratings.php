<?php
require_once 'db_connection/db_con.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <title>Admin Dashboard</title>
</head>
<body>
<div class="header">
    <div class="logo">
        <img src="images/logo.jpg" alt="Logo">
    </div>
    <div class="nav">
        <i id="toggleSidebarBtn"></i>
        <a id="logoutBtn">Log Out</a>
    </div>
</div>

<div class="main">
    <div id="sidebar" class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manageStaff.php"><i class="fas fa-tachometer-alt"></i> Manage Staff</a>
        <a href="manageCustomer.php"><i class="fas fa-users"></i> Customer</a>
        <a href="products.php"><i class="fas fa-box"></i> Products</a>
        <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
    </div>
    <div class="content">
        <h2>Product Ratings</h2>
            
        <h3>Ratings</h3>
<table id="ratingsTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Product</th>
            <th>Comments</th>
            <th>Recommendation</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $ratingsQuery = "SELECT * FROM ratings";
        $ratingsResult = $conn->query($ratingsQuery);

        if (!$ratingsResult) {
            die("Query failed: " . $conn->error);
        }

        while ($row = $ratingsResult->fetch_assoc()) { ?>
        <tr>

            <td><?php echo htmlspecialchars($row['rating_description']); ?></td>
            <td><?php echo htmlspecialchars($row['comments']); ?></td>
            <td><?php echo htmlspecialchars($row['recommendation']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>


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

$(document).ready(function () {
    $('#ratingsTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable automatic column width adjustment
        responsive: true      // Make the table responsive
    });

});


// Get the modal
var modal = document.getElementById('logoutModal');

// Get the <span> element that closes the modal
var cancelBtn = document.getElementById('cancelLogout');

// Get the logout link
var logoutBtn = document.getElementById('logoutBtn');

// When the user clicks on Log Out link, open the modal
logoutBtn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on Cancel, close the modal
cancelBtn.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// When the user clicks on Logout button in modal, perform logout action
var confirmBtn = document.getElementById('confirmLogout');
confirmBtn.onclick = function() {
    // Perform logout action here, e.g., redirect to logout.php or clear session
    window.location.href = "adminLogout.php";
}

// Get the toggle button/icon
const toggleSidebarBtn = document.getElementById('toggleSidebarBtn');

// Get the sidebar content
const sidebar = document.getElementById('sidebar');

// Add click event listener to toggle the sidebar content
toggleSidebarBtn.addEventListener('click', function() {
if (sidebar.style.display === 'none' || sidebar.style.display === '') {
sidebar.style.display = 'block'; // Show the sidebar
} else {
sidebar.style.display = 'none'; // Hide the sidebar
}
});
</script>
</body>
</html>
