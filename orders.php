<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/orders.css">
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: admin_login.php");
    exit();
}
$customer_id = $_SESSION['customer_id'];
?>
<script>
    const customerId = '<?php echo $customer_id; ?>';
</script>
<div class="header">
    <div class="logo">
        <img src="images/njsb.png" alt="Logo">
    </div>
    <div class="nav">
        <a href="index.php">Home</a>
        <a href="#" onclick="openLogoutModal()">Log Out</a>
    </div>
</div>
<div class="restaurant-header">
    <div class="details">
        <h1>We Print for Good</h1>
    </div>
</div>

<!-- Card Section (Updated with Customer Info) -->
<div class="container">
    <div class="card">
        <h1>Welcome!</h1>
        <p>Customer ID: <span id="cardCustomerId"></span></p>
        <p>Pickup Date: <span id="cardPickupDate"></span></p>
        <p>Pickup Time: <span id="cardPickupTime"></span></p>
        <p>Remaining Time: <span id="cardRemainingTime"></span></p>
        <p>Click the button below to view your orders.</p>
        <button onclick="openOrdersModal()">View Orders</button>
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

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLogoutModal()">&times;</span>
        <p>Are you sure you want to log out?</p>
        <button onclick="confirmLogout()">Yes, Log Out</button>
        <button onclick="closeLogoutModal()">Cancel</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Function to format time to 12-hour format with AM/PM
    function formatTimeTo12Hour(timeString) {
        const [hour, minute] = timeString.split(':');
        const hours = parseInt(hour, 10);
        const ampm = hours >= 12 ? 'PM' : 'AM';
        const formattedHour = hours % 12 || 12; // Convert to 12-hour format
        return `${formattedHour}:${minute} ${ampm}`;
    }

    // Fetch order data and display in the card
    function fetchOrdersAndDisplayInCard() {
        fetch(`API/getCustomerOrder.php?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                const orders = data.orders;
                
                // Display customer ID, pickup date, and pickup time in the card
                document.getElementById('cardCustomerId').textContent = customerId;
                const pickupDate = orders[0].pickup_date;
                const pickupTime = orders[0].pickup_time;
                document.getElementById('cardPickupDate').textContent = pickupDate;

                // Format pickup time and display
                const formattedPickupTime = formatTimeTo12Hour(pickupTime);
                document.getElementById('cardPickupTime').textContent = formattedPickupTime;

                // Combine pickup date and time into a Date object
                const pickupDateTime = new Date(`${pickupDate}T${pickupTime}`);

                // Calculate remaining time and display it in the card
                calculateRemainingTimeInCard(pickupDateTime);
            })
            .catch(error => console.error('Error fetching order data:', error));
    }

    function calculateRemainingTimeInCard(pickupDateTime) {
        const interval = setInterval(() => {
            const currentTime = new Date();
            const timeDiff = pickupDateTime - currentTime;

            if (timeDiff <= 0) {
                document.getElementById('cardRemainingTime').textContent = 'Pickup time has passed';
                clearInterval(interval);  // Stop the timer when the time has passed
            } else {
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                document.getElementById('cardRemainingTime').textContent = 
                    `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
            }
        }, 1000);  // Update every second
    }

    function fetchOrdersAndShowModal() {
        fetch(`API/getCustomerOrder.php?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                const orders = data.orders;
                
                // Display customer id, pickup date, and pickup time in the modal
                document.getElementById('modalCustomerId').textContent = customerId;
                const pickupDate = orders[0].pickup_date;
                const pickupTime = orders[0].pickup_time;
                document.getElementById('modalPickupDate').textContent = pickupDate;

                // Format pickup time and display in the modal
                const formattedPickupTime = formatTimeTo12Hour(pickupTime);
                document.getElementById('modalPickupTime').textContent = formattedPickupTime;

                // Combine pickup date and time into a Date object
                const pickupDateTime = new Date(`${pickupDate}T${pickupTime}`);

                // Calculate remaining time and display it in the modal
                calculateRemainingTimeInModal(pickupDateTime);

                // Populate table with order details
                const tbody = document.getElementById('modalOrderTableBody');
                tbody.innerHTML = '';

                orders.forEach(order => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `        
                        <td>${order.description}</td>
                        <td>₱ ${order.total_amount}</td>
                        <td>${order.total_orders}</td>
                        <td>${order.confirmation_status}</td>
                    `;
                    tbody.appendChild(tr);
                });

                openOrdersModal();
            })
            .catch(error => console.error('Error fetching order data:', error));
    }

    function calculateRemainingTimeInModal(pickupDateTime) {
        const interval = setInterval(() => {
            const currentTime = new Date();
            const timeDiff = pickupDateTime - currentTime;

            if (timeDiff <= 0) {
                document.getElementById('remainingTime').textContent = 'Pickup time has passed';
                clearInterval(interval);  // Stop the timer when the time has passed
            } else {
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

                document.getElementById('remainingTime').textContent = 
                    `${days}d ${hours}h ${minutes}m ${seconds}s remaining`;
            }
        }, 1000);  // Update every second
    }

    function openOrdersModal() {
        const modal = document.getElementById("ordersModal");
        modal.style.display = "block";
    }

    function closeOrdersModal() {
        const modal = document.getElementById("ordersModal");
        modal.style.display = "none";
    }

    // Button event to trigger the modal
    window.openOrdersModal = function() {
        fetchOrdersAndShowModal();
    }

    window.closeOrdersModal = function() {
        closeOrdersModal();
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

    window.onclick = function(event) {
        const logoutModal = document.getElementById("logoutModal");
        const ordersModal = document.getElementById("ordersModal");
        if (event.target == logoutModal) {
            logoutModal.style.display = "none";
        }
        if (event.target == ordersModal) {
            ordersModal.style.display = "none";
        }
    }

    // Fetch orders and display them in the card when the page loads
    fetchOrdersAndDisplayInCard();
});
</script>
</body>
</html>
