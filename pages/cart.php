<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ===== ADD TO CART =====
if (isset($_GET['add']) && $_GET['add'] == 1) {
    $product = [
        'name' => isset($_GET['name']) ? $_GET['name'] : 'Product',
        'price' => isset($_GET['price']) ? (float)$_GET['price'] : 0,
        'image' => isset($_GET['image']) ? $_GET['image'] : 'default.jpg',
        'quantity' => isset($_GET['qty']) ? (int)$_GET['qty'] : 1,
        'stock' => isset($_GET['stock']) ? (int)$_GET['stock'] : 10
    ];
    
    // Check if product already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['name'] === $product['name']) {
            if ($item['quantity'] + $product['quantity'] <= $item['stock']) {
                $item['quantity'] += $product['quantity'];
            } else {
                header('Location: cart.php?error=stock');
                exit();
            }
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $product;
    }
    
    header('Location: cart.php');
    exit();
}

// ===== REMOVE =====
if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    header('Location: cart.php');
    exit();
}

// ===== CLEAR =====
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit();
}

// ===== UPDATE =====
if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $index => $qty) {
        if ($qty > 0 && isset($_SESSION['cart'][$index])) {
            $stock = isset($_SESSION['cart'][$index]['stock']) ? $_SESSION['cart'][$index]['stock'] : 10;
            if ($qty <= $stock) {
                $_SESSION['cart'][$index]['quantity'] = (int)$qty;
            }
        }
    }
    header('Location: cart.php');
    exit();
}

// ===== GET CART DATA =====
$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Cart</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    
    <!-- ===== HEADER ===== -->
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
                        <a href="#" class="search-icon" id="searchToggle"><i class="fas fa-search"></i></a>
                        <div class="search-dropdown" id="searchDropdown">
                            <input type="text" placeholder="Search products..." id="searchInput">
                            <button type="button"><i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge"><?php echo count($cart); ?></span>
                    </a>
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <ul>
                        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                        <li><a href="#"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
                        <li><a href="#"><i class="fas fa-history"></i> History</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== CART PAGE ===== -->
    <section class="cart-page">
        <div class="container">
            <div class="cart-header">
                <h1>🛒 YOUR CART</h1>
                <p>Review your items before checkout</p>
            </div>
            
            <?php if (count($cart) > 0): ?>
                <form method="POST" action="cart.php">
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Stock</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $index => $item): ?>
                                    <tr>
                                        <td class="cart-product">
                                            <img src="../images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                        </td>
                                        <td class="cart-price">₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="cart-quantity">
                                            <input type="number" name="quantity[<?php echo $index; ?>]" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   max="<?php echo isset($item['stock']) ? $item['stock'] : 10; ?>">
                                        </td>
                                        <td class="cart-stock">
                                            <?php 
                                            $stock = isset($item['stock']) ? $item['stock'] : 10;
                                            echo $stock; 
                                            ?>
                                            <?php if ($stock <= 3): ?>
                                                <span style="color: #ff4444; font-size: 12px; margin-left: 5px;">⚠️ Low Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="cart-total">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td class="cart-remove">
                                            <a href="cart.php?remove=<?php echo $index; ?>" class="remove-btn" onclick="return confirm('Remove this item?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="cart-actions">
                        <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
                        <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
                        <a href="cart.php?clear=1" class="btn btn-danger" onclick="return confirm('Clear all items?')">Clear Cart</a>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="cart-total-box">
                            <h3>Cart Total</h3>
                            <div class="cart-total-row">
                                <span>Subtotal:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="cart-total-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="cart-total-row grand-total">
                                <span>Total:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <a href="checkout.php" class="btn btn-primary checkout-btn">Proceed to Checkout</a>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="empty-cart-page">
                    <i class="fas fa-shopping-cart fa-4x"></i>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>QUICK LINKS</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>CUSTOMER SERVICE</h4>
                    <ul>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Shipping Information</a></li>
                        <li><a href="#">Return & Exchange</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>FOLLOW US</h4>
                    <ul>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">Tiktok</a></li>
                        <li><a href="#">Facebook</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 2THSND4. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="../script.js"></script>
</body>
</html>