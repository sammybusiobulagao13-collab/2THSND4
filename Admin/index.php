<?php
session_start();
require_once '../database/config.php';

// Check if user is logged in AND is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: ../login/login.php');
    exit();
}

// Get dashboard stats (only from existing tables)
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();

// Get low stock products
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 3 AND stock > 0")->fetchColumn();
$outOfStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 0")->fetchColumn();

// Get unread messages count for badge
$unreadMessages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();

// Get recent orders
$recentOrders = $pdo->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Admin (Dashboard)</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #0a0a1a; }
        
        /* Admin Container */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: 260px;
            background: #000;
            padding: 20px;
            border-right: 1px solid rgba(255,255,255,0.05);
            min-height: 100vh;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            z-index: 99;
        }
        .admin-sidebar .logo {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 2px;
            
        }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li { margin-bottom: 2px; }
        .admin-sidebar ul li a {
            display: block;
            padding: 12px 18px;
            color: #888;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
            font-size: 14px;
        }
        .admin-sidebar ul li a:hover,
        .admin-sidebar ul li a.active {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        .admin-sidebar ul li a i {
            width: 22px;
            margin-right: 12px;
            text-align: center;
        }
        .admin-sidebar .logout-link {
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 15px;
        }
        .admin-sidebar .logout-link a { color: #ff4444 !important; }
        .admin-sidebar .logout-link a:hover { background: rgba(255,68,68,0.1) !important; }
        
        /* Content */
        .admin-content {
            margin-left: 260px;
            flex: 1;
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 260px);
        }
        .admin-content .welcome {
            margin-bottom: 25px;
        }
        .admin-content .welcome h1 {
            color: #fff;
            font-size: 28px;
            font-family: var(--font-primary);
            letter-spacing: 1px;
        }
        .admin-content .welcome p {
            color: #888;
            font-size: 15px;
            margin-top: 5px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255,255,255,0.03);
            padding: 20px 25px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.3s;
        }
        .stat-card:hover {
            border-color: rgba(255,255,255,0.15);
            transform: translateY(-3px);
        }
        .stat-card .stat-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .stat-card .stat-number {
            font-size: 30px;
            font-weight: 700;
            color: #fff;
        }
        .stat-card .stat-label {
            color: #888;
            font-size: 13px;
            margin-top: 5px;
        }
        .stat-card .stat-sub {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .stat-card .stat-sub .warning { color: #ffc107; }
        .stat-card .stat-sub .danger { color: #ff4444; }
        
        /* Recent Orders */
        .recent-section {
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .recent-section .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .recent-section .section-header h2 {
            color: #fff;
            font-size: 18px;
            font-family: var(--font-primary);
        }
        .recent-section .section-header a {
            color: #17a2b8;
            text-decoration: none;
            font-size: 14px;
        }
        .recent-section .section-header a:hover { text-decoration: underline; }
        .recent-section table { width: 100%; border-collapse: collapse; }
        .recent-section th,
        .recent-section td {
            padding: 10px 12px;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-align: left;
            font-size: 14px;
        }
        .recent-section th {
            color: #888;
            font-weight: 400;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .recent-section tr:hover td { background: rgba(255,255,255,0.02); }
        .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            display: inline-block;
        }
        .status-processing { background: rgba(255,193,7,0.15); color: #ffc107; }
        .status-shipped { background: rgba(23,162,184,0.15); color: #17a2b8; }
        .status-delivered { background: rgba(40,167,69,0.15); color: #28a745; }
        .status-cancelled { background: rgba(220,53,69,0.15); color: #dc3545; }
        
        .no-data {
            text-align: center;
            color: #666;
            padding: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar { width: 200px; }
            .admin-content { margin-left: 200px; padding: 15px; width: calc(100% - 200px); }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .admin-sidebar { width: 60px; }
            .admin-sidebar .logo { font-size: 14px; }
            .admin-sidebar ul li a span { display: none; }
            .admin-sidebar ul li a i { margin-right: 0; font-size: 18px; }
            .admin-content { margin-left: 60px; padding: 10px; width: calc(100% - 60px); }
            .stats-grid { grid-template-columns: 1fr; }
            .admin-content .welcome h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="logo">2THSND4</div>
            <ul>
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> <span>Messages</span>
                    <?php if ($unreadMessages > 0): ?>
                        <span style="background:#ff4444;color:#fff;border-radius:50%;padding:2px 8px;font-size:11px;margin-left:5px;"><?php echo $unreadMessages; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
                <li class="logout-link"><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- Content -->
        <div class="admin-content">
            <div class="welcome">
                <h1>👋 Welcome back, Admin!</h1>
                <p>Here's what's happening with your store today.</p>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $totalProducts; ?></div>
                    <div class="stat-label">Total Products</div>
                    <div class="stat-sub">
                        <?php if ($lowStock > 0): ?>
                            <span class="warning">⚠️ <?php echo $lowStock; ?> low stock</span>
                        <?php endif; ?>
                        <?php if ($outOfStock > 0): ?>
                            <span class="danger">❌ <?php echo $outOfStock; ?> out of stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $totalOrders; ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-number"><?php echo $totalUsers; ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📩</div>
                    <div class="stat-number"><?php echo $unreadMessages; ?></div>
                    <div class="stat-label">Unread Messages</div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="recent-section">
                <div class="section-header">
                    <h2>Recent Orders</h2>
                    <a href="orders.php">View All →</a>
                </div>
                <?php if (count($recentOrders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_number']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                <td><span class="status-badge status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="no-data">No orders yet. 🛒</div>
                <?php endif; ?>
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
                <a href="../login/logout.php" class="btn btn-primary">Yes, Logout</a>
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

// Close modal on background click
document.getElementById('logoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
    }
});
</script>

</body>
</html>