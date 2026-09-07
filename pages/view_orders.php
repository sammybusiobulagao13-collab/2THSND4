<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php?redirect=view_orders');
    exit();
}

$user = $_SESSION['user'];

// Get orders from session
$orders = isset($_SESSION['orders']) ? $_SESSION['orders'] : [];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - My Orders</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <div class="nav-logo">
            <a href="index.php">
                <img src='../images/headerlogo.png' alt="2THSND4 Logo">
            </a>
        </div>
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="contact.php">CONTACT</a></li>
            </ul>
            
            <div class="nav-icons">
                <div class="search-wrapper">
                    <a href="#" class="search-icon" id="searchToggle">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="search-dropdown" id="searchDropdown">
                        <input type="text" placeholder="Search products..." id="searchInput">
                        <button type="button"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="dropdown-menu" id="dropdownMenu">
                <ul>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['user']['name']; ?></a></li>
                    <?php else: ?>
                        <li><a href="../login/login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
                    <?php endif; ?>
                    
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="view-orders.php"><i class="fas fa-box"></i> My Orders</a></li>
                        <li><a href="history.php"><i class="fas fa-history"></i> History</a></li>
                        <li><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

<section class="history-page">
    <div class="container">
        <div class="history-header">
            <h1> MY ORDERS</h1>
            <p>View all your orders and track their status</p>
        </div>

        <?php if (count($orders) > 0): ?>
            <div class="orders-container">
                <?php foreach (array_reverse($orders) as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-info">
                                <span class="order-number">Order #<?php echo $order['id']; ?></span>
                                <span class="order-date">
                                    <i class="far fa-calendar-alt"></i> 
                                    <?php echo date('F d, Y - h:i A', strtotime($order['date'])); ?>
                                </span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item">
                                    <span class="item-name"><?php echo $item['name']; ?></span>
                                    <span class="item-qty">x<?php echo $item['quantity']; ?></span>
                                    <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer">
                            <div class="order-total">
                                <span>Total:</span>
                                <strong>₱<?php echo number_format($order['total'], 2); ?></strong>
                            </div>
                           
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-history">
                <i class="fas fa-box fa-4x"></i>
                <h2>No Orders Yet</h2>
                <p>You haven't placed any orders yet. Start shopping now!</p>
                
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- LOGOUT MODAL -->
<div class="logout-modal-overlay" id="logoutModal" style="display: none;">
    <div class="logout-modal">
        <div class="logout-modal-content">
            <p>Are you sure you want to log out?</p>
            <div class="logout-modal-actions">
                <button class="btn btn-secondary" onclick="closeLogoutModal()">Cancel</button>
                <a href="../login/logout.php" class="btn btn-primary">Yes</a>
            </div>
        </div>
    </div>
</div>

<script src="../script.js"></script>
<script>
function viewOrderDetails(orderId) {
    alert('Order #' + orderId + '\n\nOrder details will be shown here.\n(This feature coming soon!)');
}
</script>

</body>
</html>