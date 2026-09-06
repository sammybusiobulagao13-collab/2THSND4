<?php
session_start();


if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php');
    exit();
}


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
    
    try {
        $pdo = getConnection();
        $pdo->beginTransaction();
        
        foreach ($_SESSION['cart'] as $item) {
            if (isset($item['id'])) {
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $stmt->execute([$item['quantity'], $item['id'], $item['quantity']]);
            }
        }
        
        $pdo->commit();
    } catch (Exception $e) {
      
    }
    
    $_SESSION['cart'] = [];
    $orderSuccess = true;
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
            <div class="checkout-header">
                <h1>🛍️ CHECKOUT</h1>
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
                            <input type="text" placeholder="Full Name" value="<?php echo $_SESSION['user']['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="email" placeholder="Email Address" value="<?php echo $_SESSION['user']['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Phone Number" required>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Shipping Address" required>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="City / Municipality" required>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="ZIP Code" required>
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