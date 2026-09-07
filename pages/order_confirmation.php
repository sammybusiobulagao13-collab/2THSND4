<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php');
    exit();
}

// Get order details from session
$order = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : null;

// If no order, redirect to shop
if (!$order) {
    header('Location: shop.php');
    exit();
}

// Clear the order from session para di na ma-access balik
unset($_SESSION['last_order']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - 2THSND4</title>
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
                <!-- SEARCH WRAPPER -->
                <div class="search-wrapper">
                    <a href="#" class="search-icon" id="searchToggle">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="search-dropdown" id="searchDropdown">
                        <input type="text" placeholder="Search products..." id="searchInput">
                        <button type="button"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
                
                <!-- MENU TOGGLE -->
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- DROPDOWN MENU -->
            <div class="dropdown-menu" id="dropdownMenu">
                <ul>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li><a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['user']['name']; ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<section class="checkout-page">
    <div class="container">
        <div class="order-success">
            <i class="fas fa-check-circle"></i>
            <h2>Order Confirmed!</h2>
            <p class="sub-text">
                Thank you for your order, <strong><?php echo $_SESSION['user']['name']; ?></strong>!
            </p>
            <p class="info-text">
                Your order has been placed successfully.
            </p>
            
            <div class="order-box">
                <p class="label">Order #</p>
                <p class="order-number">
                    #2TH-<?php echo date('Ymd') . '-' . rand(1000, 9999); ?>
                </p>
                <p class="order-date">
                    <?php echo date('F d, Y - h:i A'); ?>
                </p>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h4>Order Summary</h4>
                <?php foreach ($order['items'] as $item): ?>
                    <div class="item-row">
                        <span class="item-name"><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                        <span class="item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="total-row">
                    <span class="total-label">Total</span>
                    <span class="total-amount">₱<?php echo number_format($order['total'], 2); ?></span>
                </div>
            </div>
            
            <div class="order-actions">
                <a href="shop.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
                <a href="history.php" class="btn btn-secondary">
                    <i class="fas fa-history"></i> View Orders
                </a>
            </div>
        </div>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>