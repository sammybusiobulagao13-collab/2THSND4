<?php
session_start();
require_once '../database/config.php';  // ← IDUGANG NI

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php');
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $orderId = '2TH-' . date('Ymd') . '-' . rand(1000, 9999);
    
    //SAVE TO DATABASE
    $user_id = $_SESSION['user']['id'];
    $shipping_address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip_code = $_POST['zip'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $payment_method = 'Cash on Delivery';
    
    try {
        $pdo->beginTransaction();
        
        // Insert into orders table
$stmt = $pdo->prepare("
    INSERT INTO orders (user_id, order_number, total, status, payment_method, shipping_address, city, zip_code, phone) 
    VALUES (?, ?, ?, 'Processing', ?, ?, ?, ?, ?)
");
$stmt->execute([$user_id, $orderId, $total, $payment_method, $shipping_address, $city, $zip_code, $phone]);
        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT order_number FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order_number = $stmt->fetchColumn();
        
        // Insert into order_items and update stock
$stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
$updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

foreach ($cart as $item) {
    $size = $item['size'] ?? 'M';
    $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $size]);
    $updateStock->execute([$item['quantity'], $item['id'], $item['quantity']]);
}
        
        $pdo->commit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Order failed: ' . $e->getMessage();
    }
    $orderData = [
    'id' => $order_number, 
    'items' => $cart,
    'total' => $total,
    'date' => date('Y-m-d H:i:s'),
    'status' => 'Processing'
];

    $_SESSION['last_order'] = $orderData;
    if (!isset($_SESSION['orders'])) {
        $_SESSION['orders'] = [];
    }
    $_SESSION['orders'][] = $orderData;
    
    $_SESSION['cart'] = [];
    
    header('Location: order_confirmation.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Checkout</title>
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
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
    <?php if (isset($_SESSION['user'])): ?>
        <!-- PROFILE CARD -->
        <div class="dropdown-profile">
            <div class="dropdown-avatar">
                <?php echo strtoupper(substr($_SESSION['user']['name'], 0, 1)); ?>
            </div>
            <div class="dropdown-user-info">
                <span class="dropdown-name"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                <span class="dropdown-email"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></span>
            </div>
        </div>
        <div class="dropdown-divider"></div>
    <?php endif; ?>
    
    <ul>
        <?php if (!isset($_SESSION['user'])): ?>
            <li><a href="../login/login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
        <?php endif; ?>
        
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="view_orders.php"><i class="fas fa-box"></i> My Orders</a></li>
            <li><a href="#" onclick="showLogoutModal(event)" class="logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php endif; ?>
    </ul>
</div>
            </div>
        </div>
    </nav>


    <section class="checkout-page">
        <div class="container">
            <div class="checkout-header">
                <h1>CHECKOUT</h1>
                <p>Review your order and confirm</p>
            </div>
            
            <?php if (isset($orderSuccess) && $orderSuccess): ?>
                <div class="order-success">
                    <i class="fas fa-check-circle fa-4x"></i>
                    <h2>Order Placed Successfully!</h2>
                    <p>Thank you for your order. You will receive a confirmation email.</p>
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
            
            <form method="POST" action="checkout.php" class="checkout-form">
                <div class="checkout-grid">
                    
                
                    <div class="checkout-summary">
                        <h2>Order Summary</h2>
                        <?php foreach ($cart as $item): ?>
                            <div class="checkout-item">
                                <img src="../images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                <div class="checkout-item-details">
                                    <h4><?php echo $item['name']; ?></h4>
                                    <p>₱<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                                </div>
                                <span class="checkout-item-total">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="checkout-total">
                            <div class="checkout-total-row">
                                <span>Subtotal:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="checkout-total-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="checkout-total-row grand-total">
                                <span>Total:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                
                    <div class="checkout-details">
    <h2>Shipping Details</h2>
    <div class="form-group">
        <input type="text" name="address" placeholder="Shipping Address" required>
    </div>
    <div class="form-group">
        <input type="text" name="city" placeholder="City / Municipality" required>
    </div>
    <div class="form-group">
        <input type="text" name="zip" placeholder="ZIP Code" required>
    </div>
    <div class="form-group">
        <input type="text" name="phone" placeholder="Phone Number" required>
    </div>
    
    <!-- PAYMENT METHOD -->
    <div class="form-group">
        <label style="color:#aaa;font-size:13px;display:block;margin-bottom:8px;">Mode of Payment</label>
        <div style="width:100%;padding:14px 18px;border:1px solid rgba(255,193,7,0.3);border-radius:10px;background:rgba(255,193,7,0.08);color:#ffc107;font-size:15px;font-family:Arial,sans-serif;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-money-bill-wave" style="font-size:18px;"></i>
            <span>Cash on Delivery (COD)</span>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary checkout-submit">Place Order</button>
</div>
                    
                </div>
            </form>
            
            <?php endif; ?>
        </div>
    </section>


    <script src="../script.js"></script>
</body>
</html>