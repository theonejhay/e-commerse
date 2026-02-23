<?php
require_once 'db_connection/db_con.php'; 

$currentUserQuery = "SELECT * FROM customer WHERE customer_id = ?";
$currentUserStmt = $conn->prepare($currentUserQuery);
$currentUserStmt->bind_param("i", $customer_id);
$currentUserStmt->execute();
$currentUserResult = $currentUserStmt->get_result();
$currentUser = $currentUserResult->fetch_assoc();

// Fetch total number of customers
$totalCustomersQuery = "SELECT COUNT(*) AS total_customers FROM customer";
$totalCustomersResult = $conn->query($totalCustomersQuery);
if (!$totalCustomersResult) {
    die("Query failed: " . $conn->error);
}
$totalCustomers = $totalCustomersResult->fetch_assoc()['total_customers'];

// Fetch total number of products
$totalProductsQuery = "SELECT COUNT(*) AS total_products FROM product_maintenance";
$totalProductsResult = $conn->query($totalProductsQuery);
if (!$totalProductsResult) {
    die("Query failed: " . $conn->error);
}
$totalProducts = $totalProductsResult->fetch_assoc()['total_products'];

// Fetch total number of orders
$totalOrdersQuery = "SELECT COUNT(*) AS total_orders FROM order_main";
$totalOrdersResult = $conn->query($totalOrdersQuery);
if (!$totalOrdersResult) {
    die("Query failed: " . $conn->error);
}
$totalOrders = $totalOrdersResult->fetch_assoc()['total_orders'];

// Fetch total pending and confirmed statuses
$statusQuery = "SELECT 
    (SELECT COUNT(*) FROM order_main WHERE confirmation_status = 'pending') AS total_pending,
    (SELECT COUNT(*) FROM order_main WHERE confirmation_status = 'confirmed') AS total_confirm";
$statusResult = $conn->query($statusQuery);
if (!$statusResult) {
    die("Query failed: " . $conn->error);
}
$statusCounts = $statusResult->fetch_assoc();
$totalPending = $statusCounts['total_pending'];
$totalConfirm = $statusCounts['total_confirm'];

// Fetch product data with total sale quantity, total sales amount, and total profit
$productQuery = "
SELECT 
    p.product_id, 
    p.description, 
    c.category_name, -- Join and fetch the actual category name
    p.price, 
    p.quantity AS available_quantity, -- Include quantity from product_maintenance
    SUM(
        CASE 
            WHEN co.description LIKE CONCAT('%', p.description, '%') 
            AND co.description REGEXP '\\(x([0-9]+)\\)' THEN 
                CAST(SUBSTRING(
                    co.description, 
                    LOCATE('(x', co.description) + 2, 
                    LOCATE(')', co.description) - LOCATE('(x', co.description) - 2
                ) AS UNSIGNED)
            ELSE 0
        END
    ) AS total_sale_quantity, -- Extract and sum quantity from description
    SUM(co.total_amount) AS total_sales, -- Total sales amount from claimed_order
    SUM(
        CASE 
            WHEN co.description LIKE CONCAT('%', p.description, '%') 
            AND co.description REGEXP '\\(x([0-9]+)\\)' THEN 
                CAST(SUBSTRING(
                    co.description, 
                    LOCATE('(x', co.description) + 2, 
                    LOCATE(')', co.description) - LOCATE('(x', co.description) - 2
                ) AS UNSIGNED) * p.price
            ELSE 0
        END
    ) AS total_profit -- Total profit based on sale quantity * price
FROM 
    product_maintenance p
LEFT JOIN 
    claimed_order co 
ON 
    co.description LIKE CONCAT('%', p.description, '%') -- Match description
LEFT JOIN 
    categories c 
ON 
    p.category = c.category_id -- Join categories to fetch category_name
GROUP BY 
    p.product_id, c.category_name, p.quantity;";


$productResult = $conn->query($productQuery);

if (!$productResult) {
    die("Query failed: " . $conn->error);
}

// Update the initial_total_profit in product_maintenance using the total from product_total
$updateInitialProfitQuery = "
    UPDATE product_maintenance pm
    INNER JOIN product_total pt ON pm.product_id = pt.product_id
    SET pm.initial_total_profit = pt.total
    WHERE pm.initial_total_profit = 0 OR pm.initial_total_profit IS NULL
";
$conn->query($updateInitialProfitQuery);

// Retrieve product data along with initial_total_profit from product_total
$initialProductQuery = "
    SELECT 
        p.product_id, 
        p.description, 
        c.category_name, 
        p.price, 
        p.quantity AS available_quantity, 
        pt.total AS initial_total_profit 
    FROM 
        product_maintenance p
    LEFT JOIN 
        product_total pt 
    ON 
        p.product_id = pt.product_id
    LEFT JOIN 
        categories c 
    ON 
        p.category = c.category_id
";
$initialProductResult = $conn->query($initialProductQuery);

if (!$initialProductResult) {
    die("Query failed: " . $conn->error);
}



// Fetch data from admincancelled_orders table
$adminCancelledOrdersQuery = "SELECT * FROM admincancelled_orders";
$adminCancelledOrdersResult = $conn->query($adminCancelledOrdersQuery);

if (!$adminCancelledOrdersResult) {
    die("Query failed: " . $conn->error);
}

$claimedOrdersQuery = "SELECT * FROM claimed_order";
$claimedOrdersResult = $conn->query($claimedOrdersQuery);

if (!$claimedOrdersResult) {
    die("Query failed: " . $conn->error);
}

// Fetch total earnings from claimed_orders table
$totalEarningsQuery = "SELECT SUM(total_amount) AS total_earnings FROM claimed_order";
$totalEarningsResult = $conn->query($totalEarningsQuery);

if (!$totalEarningsResult) {
    die("Query failed: " . $conn->error);
}

$totalEarningsRow = $totalEarningsResult->fetch_assoc();
$totalEarnings = $totalEarningsRow['total_earnings'] ?: 0; // Default to 0 if NULL


// Fetch total loss from admincancelled_orders table
$totalLossQuery = "SELECT SUM(total_amount) AS total_loss FROM admincancelled_orders";
$totalLossResult = $conn->query($totalLossQuery);

if (!$totalLossResult) {
    die("Query failed: " . $conn->error);
}

$totalLossRow = $totalLossResult->fetch_assoc();
$totalLoss = $totalLossRow['total_loss'] ?: 0; // Default to 0 if NULL

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
        <a href="manageStaff.php"><i class="fa-solid fa-user"></i> Manage Staff</a>
        <a href="manageCustomer.php"><i class="fas fa-users"></i> Customer</a>
        <a href="products.php"><i class="fas fa-box"></i> Products</a>
        <a href="all_order.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="ratings.php"><i class="fas fa-check"></i> Ratings</a>
        <a href="salesReport.php"><i class="fas fa-file-alt"></i> Sales Report</a>
    </div>
    <div class="content">
        <h2>Reports</h2>
        <h3>Real-Time Product Quantities (Updated)</h3>
            <table id="realTimeProductsTable" border="1" class="display nowrap" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Sale Quantity</th>
                        <th>Total Sales</th>
                        <th>Total Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($productRow = $productResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($productRow['product_id']); ?></td>
                        <td><?php echo htmlspecialchars($productRow['description']); ?></td>
                        <td><?php echo htmlspecialchars($productRow['category_name']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($productRow['price'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($productRow['available_quantity']); ?></td>
                        <td><?php echo htmlspecialchars($productRow['total_sale_quantity']); ?></td> <!-- Total Sale Quantity -->
                        <td><?php echo htmlspecialchars(number_format($productRow['total_sales'], 2)); ?></td> <!-- Total Sales Amount -->
                        <td><?php echo htmlspecialchars(number_format($productRow['total_profit'], 2)); ?></td> <!-- Total Profit -->
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h3>Initial Product Quantities (Fixed)</h3>
<table id="initialProductsTable" border="1" class="display nowrap" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Description</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total of Product (per quantity)</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($initialProductRow = $initialProductResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($initialProductRow['product_id']); ?></td>
            <td><?php echo htmlspecialchars($initialProductRow['description']); ?></td>
            <td><?php echo htmlspecialchars($initialProductRow['category_name']); ?></td>
            <td><?php echo htmlspecialchars(number_format($initialProductRow['price'], 2)); ?></td>
            <td><?php echo htmlspecialchars($initialProductRow['available_quantity']); ?></td>
            <td><?php echo htmlspecialchars(number_format($initialProductRow['initial_total_profit'], 2)); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Available Products</h3>
<table id="availableProductsTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Description</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $availableProductsQuery = "SELECT * FROM product_maintenance WHERE status = 'available'";
        $availableProductsResult = $conn->query($availableProductsQuery);

        if (!$availableProductsResult) {
            die("Query failed: " . $conn->error);
        }

        while ($productRow = $availableProductsResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($productRow['product_id']); ?></td>
            <td><?php echo htmlspecialchars($productRow['description']); ?></td>
            <td><?php echo htmlspecialchars($productRow['category']); ?></td>
            <td><?php echo htmlspecialchars(number_format($productRow['price'], 2)); ?></td>
            <td><?php echo htmlspecialchars($productRow['quantity']); ?></td>
            <td><?php echo htmlspecialchars($productRow['status']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Pending Orders</h3>
<table id="pendingOrdersTable" border="1" class="display nowrap" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Description</th>
            <th>Total Amount</th>
            <th>Total Orders</th>
            <th>Confirmation Status</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $pendingOrdersQuery = "SELECT * FROM order_main WHERE confirmation_status = 'pending'";
        $pendingOrdersResult = $conn->query($pendingOrdersQuery);

        if (!$pendingOrdersResult) {
            die("Query failed: " . $conn->error);
        }

        while ($pendingOrderRow = $pendingOrdersResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($pendingOrderRow['order_id']); ?></td>
            <td><?php echo htmlspecialchars($pendingOrderRow['customer_id']); ?></td>
            <td><?php echo htmlspecialchars($pendingOrderRow['description']); ?></td>
            <td><?php echo htmlspecialchars(number_format($pendingOrderRow['total_amount'], 2)); ?></td>
            <td><?php echo htmlspecialchars($pendingOrderRow['total_orders']); ?></td>
            <td><?php echo htmlspecialchars($pendingOrderRow['confirmation_status']); ?></td>
            <td><?php echo htmlspecialchars($pendingOrderRow['pickup_date']); ?></td>
            <td><?php echo htmlspecialchars($pendingOrderRow['pickup_time']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>


<h3>Confirmed Orders</h3>
<table id="confirmedOrdersTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Description</th>
            <th>Total Amount</th>
            <th>Total Orders</th>
            <th>Confirmation Status</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // SQL query to fetch confirmed orders
        $confirmedOrdersQuery = "SELECT * FROM order_main WHERE confirmation_status = 'confirmed'";
        $confirmedOrdersResult = $conn->query($confirmedOrdersQuery);
        
        if (!$confirmedOrdersResult) {
            die("Query failed: " . $conn->error);
        }

        // Loop through the results and display in the table
        while ($confirmedOrderRow = $confirmedOrdersResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($confirmedOrderRow['order_id']); ?></td>
            <td><?php echo htmlspecialchars($confirmedOrderRow['customer_id']); ?></td>
            <td><?php echo htmlspecialchars($confirmedOrderRow['description']); ?></td>
            <td><?php echo htmlspecialchars(number_format($confirmedOrderRow['total_amount'], 2)); ?></td>
            <td><?php echo htmlspecialchars($confirmedOrderRow['total_orders']); ?></td>
            <td><?php echo htmlspecialchars($confirmedOrderRow['confirmation_status']); ?></td>
            <td><?php echo htmlspecialchars($confirmedOrderRow['pickup_date']); ?></td>
            <td><?php echo htmlspecialchars($confirmedOrderRow['pickup_time']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Ready to claim Orders</h3>
<table id="readyToClaimTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Description</th>
            <th>Total Amount</th>
            <th>Total Orders</th>
            <th>Confirmation Status</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // SQL query to fetch confirmed orders
        $readyToClaimOrdersQuery = "SELECT * FROM order_main WHERE confirmation_status = 'ready-to-claim'";
        $readyToClaimOrdersResult = $conn->query($readyToClaimOrdersQuery);
        
        if (!$readyToClaimOrdersResult) {
            die("Query failed: " . $conn->error);
        }

        // Loop through the results and display in the table
        while ($readyToClaimOrderRow = $readyToClaimOrdersResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['order_id']); ?></td>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['customer_id']); ?></td>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['description']); ?></td>
            <td><?php echo htmlspecialchars(number_format($readyToClaimOrderRow['total_amount'], 2)); ?></td>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['total_orders']); ?></td>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['confirmation_status']); ?></td>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['pickup_date']); ?></td>
            <td><?php echo htmlspecialchars($readyToClaimOrderRow['pickup_time']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Claimed Orders</h3>
<table id="claimedOrdersTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Description</th>
            <th>Total Amount</th>
            <th>Total Orders</th>
            <th>Pickup Date</th>
            <th>Pickup Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($claimedOrderRow = $claimedOrdersResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($claimedOrderRow['order_id']); ?></td>
            <td><?php echo htmlspecialchars($claimedOrderRow['customer_id']); ?></td>
            <td><?php echo htmlspecialchars($claimedOrderRow['description']); ?></td>
            <td><?php echo htmlspecialchars(number_format($claimedOrderRow['total_amount'], 2)); ?></td>
            <td><?php echo htmlspecialchars($claimedOrderRow['total_orders']); ?></td>
            <td><?php echo htmlspecialchars($claimedOrderRow['pickup_date']); ?></td>
            <td><?php echo htmlspecialchars($claimedOrderRow['pickup_time']); ?></td>
            <td><?php echo htmlspecialchars($claimedOrderRow['status']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Claimed Add-ons</h3>
<table id="claimedAddonsTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Order ID</th>
            <th>Addon Name</th>
            <th>Addon Type</th>
            <th>Addon Price</th>
            <th>Quantity</th>
            <th>Pickup Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // SQL query to fetch claimed addons
        $claimedAddonsQuery = "SELECT * FROM claimed_addons";
        $claimedAddonsResult = $conn->query($claimedAddonsQuery);
        
        if (!$claimedAddonsResult) {
            die("Query failed: " . $conn->error);
        }

        // Loop through the results and display them in the table
        while ($addonRow = $claimedAddonsResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($addonRow['id']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['order_id']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['addon_name']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['addon_type']); ?></td>
            <td><?php echo htmlspecialchars(number_format($addonRow['addon_price'], 2)); ?></td>
            <td><?php echo htmlspecialchars($addonRow['quantity']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['created_at']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<h3>Cancelled Orders</h3>
    <table id="cancelledOrdersTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>Username</th>
                <th>Contact No</th>
                <th>Description</th>
                <th>Total Amount</th>
                <th>Total Orders</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
                <th>Remarks</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $adminCancelledOrdersResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars(number_format($row['total_amount'], 2)); ?></td>
                <td><?php echo htmlspecialchars($row['total_orders']); ?></td>
                <td><?php echo htmlspecialchars($row['pickup_date']); ?></td>
                <td><?php echo htmlspecialchars($row['pickup_time']); ?></td>
                <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3>Cancelled Add-ons</h3>
<table id="cancelledAddonsTable" class="display nowrap" border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Order ID</th>
            <th>Addon Name</th>
            <th>Addon Type</th>
            <th>Addon Price</th>
            <th>Quantity</th>
            <th>Pickup Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // SQL query to fetch claimed addons
        $claimedAddonsQuery = "SELECT * FROM cancelled_addons";
        $claimedAddonsResult = $conn->query($claimedAddonsQuery);
        
        if (!$claimedAddonsResult) {
            die("Query failed: " . $conn->error);
        }

        // Loop through the results and display them in the table
        while ($addonRow = $claimedAddonsResult->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($addonRow['id']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['order_id']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['addon_name']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['addon_type']); ?></td>
            <td><?php echo htmlspecialchars(number_format($addonRow['addon_price'], 2)); ?></td>
            <td><?php echo htmlspecialchars($addonRow['quantity']); ?></td>
            <td><?php echo htmlspecialchars($addonRow['created_at']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
    <div class="content">
        <div class="dashboard-header">
            <div class="card" onclick="location.href='all_order.php'">
                <i class="fas fa-clock"></i>
                <h3>Total Pending</h3>
                <p><?php echo htmlspecialchars($totalPending); ?></p>
            </div>
            <div class="card" onclick="location.href='all_order.php'">
                <i class="fas fa-check-circle"></i>
                <h3>Total Confirm</h3>
                <p><?php echo htmlspecialchars($totalConfirm); ?></p>
            </div>
            <div class="card" onclick="location.href='all_order.php'">
                <i class="fas fa-shopping-cart"></i>
                <h3>Total Orders</h3>
                <p><?php echo htmlspecialchars($totalOrders); ?></p>
            </div>
            <div class="card" onclick="location.href='manageCustomer.php'">
                <i class="fas fa-users"></i>
                <h3>Total Customers</h3>
                <p><?php echo htmlspecialchars($totalCustomers); ?></p>
            </div>
            <div class="card" onclick="location.href='add_products.php'">
                <i class="fas fa-box"></i>
                <h3>Total Products</h3>
                <p><?php echo htmlspecialchars($totalProducts); ?></p>
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

$(document).ready(function () {
    $('#pendingOrdersTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable automatic column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#initialProductsTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#realTimeProductsTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#claimedOrdersTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#cancelledOrdersTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#readyToClaimTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#confirmedOrdersTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#availableProductsTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#claimedAddonsTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
        responsive: true      // Make the table responsive
    });

    $('#cancelledAddonsTable').DataTable({
        paging: true,         // Enable pagination
        pageLength: 10,       // Default number of rows per page
        lengthMenu: [5, 10, 25, 50], // Dropdown options for rows per page
        ordering: true,       // Enable column sorting
        searching: true,      // Enable search functionality
        info: true,           // Show table information
        autoWidth: false,     // Disable auto column width adjustment
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
