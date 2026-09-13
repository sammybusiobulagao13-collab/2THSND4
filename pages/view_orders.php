<?php

session_start();
require_once '../database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php?redirect=view_orders');
    exit();
}

$user_id = $_SESSION['user']['id'];

// Get orders from DATABASE
$stmt = $pdo->prepare("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Get order items for each order
foreach ($orders as $key => $order) {
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order['id']]);
    $orders[$key]['items'] = $stmt->fetchAll();
}
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

<section class="history-page">
    <div class="container">
        <div class="history-header">
            <h1>MY ORDERS</h1>
            <p>View all your orders and track their status</p>
        </div>

        <?php if (count($orders) > 0): ?>
            <div class="orders-container" style="max-width:900px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:20px 25px;transition:all 0.3s ease;">
                        <div class="order-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.05);">
                            <div class="order-info">
                                <span class="order-number" style="font-size:16px;font-weight:700;color:#fff;letter-spacing:1px;">Order #<?php echo $order['order_number']; ?></span>
                                <span class="order-date" style="font-size:13px;color:#888;">
                                    <i class="far fa-calendar-alt"></i> 
                                    <?php echo date('F d, Y - h:i A', strtotime($order['created_at'])); ?>
                                </span>
                            </div>
                            <div class="order-status">
                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>" style="padding:4px 16px;border-radius:50px;font-size:13px;font-weight:600;">
                                    <?php echo $order['status']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-items" style="padding:15px 0;">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="order-item" style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:15px;color:#fff;border-bottom:1px solid rgba(255,255,255,0.03);">
                                    <span class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    <span class="item-qty" style="color:#888;margin:0 15px;font-size:13px;">x<?php echo $item['quantity']; ?></span>
                                    <span class="item-price" style="font-weight:600;color:#ffc107;">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="order-footer" style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid rgba(255,255,255,0.05);">
    <div class="order-total" style="font-size:16px;color:#888;">
        Total: <strong style="font-size:18px;color:#ffc107;margin-left:10px;">₱<?php echo number_format($order['total'], 2); ?></strong>
    </div>
    
    <!-- VIEW RECEIPT BUTTON -->
    <button class="btn-sm" onclick="showReceipt(
    '<?php echo $order['order_number']; ?>',
    '<?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>',
    '<?php echo $order['status']; ?>',
    '<?php echo htmlspecialchars($order['shipping_address']); ?>',
    '<?php echo htmlspecialchars($order['city']); ?>',
    '<?php echo htmlspecialchars($order['phone']); ?>',
    <?php echo $order['total']; ?>,
    [
        <?php foreach ($order['items'] as $item): ?>
            {
                name: '<?php echo addslashes($item['product_name']); ?>',
                size: '<?php echo addslashes($item['size'] ?? 'N/A'); ?>',
                qty: <?php echo $item['quantity']; ?>,
                price: <?php echo $item['price']; ?>
            },
        <?php endforeach; ?>
    ]
)">
        <i class="fas fa-receipt"></i> View Receipt
    </button>
</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-history" style="text-align:center;padding:150px 20px;">
                <i class="fas fa-box fa-4x" style="color:#888;margin-bottom:20px;"></i>
                
                <p style="color:#898989;font-size:15px;margin-bottom:15px;">You haven't placed any orders yet. Start shopping now!</p>
                <a href="shop.php" class="btn btn-primary" style="display:inline-block;padding:16px 50px;font-family:var(--font-primary);font-weight:700;font-size:14px;letter-spacing:2px;text-decoration:none;border-radius:50px;transition:all 0.3s ease;cursor:pointer;border:2px solid #ffffff;background-color:transparent;color:#ffffff;">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- RECEIPT MODAL -->
<div class="receipt-modal-overlay" id="receiptModal">
    <div class="receipt-modal">
        <div class="receipt-modal-content">
            <div class="receipt-header">
                <h2>RECEIPT</h2>
                <button class="receipt-close" onclick="closeReceipt()">&times;</button>
            </div>
            
            <div class="receipt-info">
                <div class="receipt-row">
                    <span>Order #:</span>
                    <strong id="receiptOrderNumber"></strong>
                </div>
                <div class="receipt-row">
                    <span>Date:</span>
                    <strong id="receiptDate"></strong>
                </div>
                <div class="receipt-row">
                    <span>Status:</span>
                    <strong id="receiptStatus"></strong>
                </div>
                <div class="receipt-row">
                    <span>Address:</span>
                    <strong id="receiptAddress"></strong>
                </div>
                <div class="receipt-row">
                    <span>Phone:</span>
                    <strong id="receiptPhone"></strong>
                </div>
            </div>
            
            <div class="receipt-items" id="receiptItems">
                
            </div>
            
            <div class="receipt-total">
                <span>TOTAL:</span>
                <strong id="receiptTotal"></strong>
            </div>
            
            <button class="btn-primary" onclick="closeReceipt()">Close</button>
        </div>
    </div>
</div>


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

<script>
function showLogoutModal(event) {
    event.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

function showReceipt(orderNumber, date, status, address, city, phone, total, items) {
    document.getElementById('receiptOrderNumber').textContent = orderNumber;
    document.getElementById('receiptDate').textContent = date;
    document.getElementById('receiptStatus').textContent = status;
    document.getElementById('receiptAddress').textContent = address + ', ' + city;
    document.getElementById('receiptPhone').textContent = phone;
    document.getElementById('receiptTotal').textContent = '₱' + total.toFixed(2);
    
    let itemsHTML = '';
    items.forEach(function(item) {
        itemsHTML += `
            <div class="receipt-item">
                <span>${item.name} x${item.qty}</span>
                <span>₱${(item.price * item.qty).toFixed(2)}</span>
            </div>
        `;
    });
    document.getElementById('receiptItems').innerHTML = itemsHTML;
    
    document.getElementById('receiptModal').style.display = 'flex';
}

function closeReceipt() {
    document.getElementById('receiptModal').style.display = 'none';
}

document.getElementById('receiptModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceipt();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReceipt();
    }
});

</script>

<script src="../script.js"></script>
</body>
</html>