<?php
session_start();
require_once '../database/config.php';  // ← IDUGANG NI

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ===== IDUGANG NI: UPDATE STOCK FROM DATABASE =====
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $cartItem) {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$cartItem['id']]);
        $product = $stmt->fetch();
        
        if ($product) {
            $currentStock = $product['stock'];
            // Update the stock in session cart item
            $_SESSION['cart'][$key]['stock'] = $currentStock;
            
            // If stock is 0 or less, remove from cart
            if ($currentStock <= 0) {
                unset($_SESSION['cart'][$key]);
            }
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}
// ===== END OF ADDED CODE =====

if (isset($_GET['add']) && $_GET['add'] == 1) {
    $product = [
        'id' => isset($_GET['id']) ? (int)$_GET['id'] : 0,
        'name' => isset($_GET['name']) ? $_GET['name'] : 'Product',
        'price' => isset($_GET['price']) ? (float)$_GET['price'] : 0,
        'image' => isset($_GET['image']) ? $_GET['image'] : 'default.jpg',
        'quantity' => isset($_GET['qty']) ? (int)$_GET['qty'] : 1,
        'stock' => isset($_GET['stock']) ? (int)$_GET['stock'] : 10
    ];
    

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $product['id']) {
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
    
    //ADD ACTIVITY LOG
    if (!isset($_SESSION['activity_log'])) {
        $_SESSION['activity_log'] = [];
    }
    $_SESSION['activity_log'][] = [
        'type' => 'cart_add',
        'message' => '🛒 Added ' . $product['name'] . ' to cart',
        'timestamp' => date('Y-m-d H:i:s')
    ];
}
    
    header('Location: cart.php');
    exit();
}


if (isset($_GET['remove'])) {
    $index = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    header('Location: cart.php');
    exit();
}


if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit();
}


if (isset($_POST['update_quantity'])) {
    $index = (int)$_POST['index'];
    $quantity = (int)$_POST['quantity'];
    
    if (isset($_SESSION['cart'][$index])) {
        $stock = isset($_SESSION['cart'][$index]['stock']) ? $_SESSION['cart'][$index]['stock'] : 10;
        if ($quantity > 0 && $quantity <= $stock) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
        }
    }
    

    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    echo json_encode(['success' => true, 'total' => $total]);
    exit();
}

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
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['user']['name']; ?></a></li>
        <?php else: ?>
            <li><a href="../login/login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
        <?php endif; ?>
        
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="view_orders.php"><i class="fas fa-box"></i> My Orders</a></li>
           
            <li><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php endif; ?>
    </ul>
</div>
    </nav>


    <section class="cart-page">
        <div class="container">
            <div class="cart-header">
                <h1>🛒 YOUR CART</h1>
                <p>Review your items before checkout</p>
            </div>
            
            <?php if (count($cart) > 0): ?>
                <form method="POST" action="cart.php" id="cartForm">
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
                                    <tr data-index="<?php echo $index; ?>">
                                        <td class="cart-product">
                                            <img src="../images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                        </td>
                                        <td class="cart-price">₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="cart-quantity">
                                            <?php 
                                            // ===== IDUGANG NI: Get updated stock from database =====
                                            $maxStock = 0;
                                            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                                            $stmt->execute([$item['id']]);
                                            $product = $stmt->fetch();
                                            if ($product) {
                                                $maxStock = $product['stock'];
                                            }
                                            ?>
                                            <input type="number" class="qty-input" 
                                                   data-index="<?php echo $index; ?>"
                                                   data-product-id="<?php echo $item['id']; ?>"
                                                   value="<?php echo min($item['quantity'], $maxStock); ?>" 
                                                   min="1" 
                                                   max="<?php echo $maxStock; ?>">
                                        </td>
                                        <td class="cart-stock">
                                            <?php 
                                            // ===== IDUGANG NI: Get updated stock from database =====
                                            $stock = 0;
                                            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                                            $stmt->execute([$item['id']]);
                                            $product = $stmt->fetch();
                                            if ($product) {
                                                $stock = $product['stock'];
                                            }
                                            echo $stock; 
                                            ?>
                                            <?php if ($stock <= 3 && $stock > 0): ?>
                                                <span style="color: #ffc107; font-size: 12px; margin-left: 5px;">⚠️ Low Stock</span>
                                            <?php elseif ($stock <= 0): ?>
                                                <span style="color: #ff4444; font-size: 12px; margin-left: 5px;">❌ Out of Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="cart-total item-total">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
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
                        <a href="cart.php?clear=1" class="btn btn-danger" onclick="return confirm('Clear all items?')">Clear Cart</a>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="cart-total-box">
                            <h3>Cart Total</h3>
                            <div class="cart-total-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="cart-total-row">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <div class="cart-total-row grand-total">
                                <span>Total:</span>
                                <span id="grandTotal">₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <a href="../login/login.php?redirect=checkout" class="btn btn-primary checkout-btn">Proceed to Checkout</a>
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

   
    <script>
 
    document.querySelectorAll('.qty-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const index = this.dataset.index;
            const quantity = parseInt(this.value);
            const maxStock = parseInt(this.max);
            
            if (quantity < 1) {
                this.value = 1;
                alert('Quantity must be at least 1');
                return;
            }
            
            if (quantity > maxStock) {
                this.value = maxStock;
                alert('Not enough stock! Maximum is ' + maxStock);
                return;
            }
            
       
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'update_quantity=1&index=' + index + '&quantity=' + quantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update total per item
                    const row = this.closest('tr');
                    const price = parseFloat(row.querySelector('.cart-price').textContent.replace(/[₱,]/g, ''));
                    const itemTotal = row.querySelector('.item-total');
                    itemTotal.textContent = '₱' + (price * quantity).toFixed(2);
                    
               
                    document.getElementById('subtotal').textContent = '₱' + data.total.toFixed(2);
                    document.getElementById('grandTotal').textContent = '₱' + data.total.toFixed(2);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    </script>
    
    <script src="../script.js"></script>
</body>
</html>