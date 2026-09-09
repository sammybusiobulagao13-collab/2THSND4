<?php
session_start();
require_once '../database/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: ../login/login.php');
    exit();
}

// Update order status
if (isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: orders.php');
    exit();
}

// Delete order
if (isset($_GET['delete'])) {
    $order_id = (int)$_GET['delete'];
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }
    
    header('Location: orders.php');
    exit();
}

// Get all orders with customer names and items
$orders = $pdo->query("
    SELECT o.*, u.name as customer_name, u.email as customer_email
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
")->fetchAll();

// Get order counts
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$processingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Processing'")->fetchColumn();
$approvedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Approved'")->fetchColumn();
$shippedOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Shipped'")->fetchColumn();
$deliveredOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Delivered'")->fetchColumn();
$cancelledOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Cancelled'")->fetchColumn();

// Get unread messages count for badge
$unreadMessages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Admin (Orders)</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #0a0a1a; }
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: #000; padding: 20px; border-right: 1px solid rgba(255,255,255,0.05); min-height: 100vh; position: fixed; height: 100%; overflow-y: auto; z-index: 99; }
        .admin-sidebar .logo { font-size: 24px; font-weight: 700; color: #fff; padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px; text-align: center; letter-spacing: 2px; }
        .admin-sidebar ul { list-style: none; padding: 0; }
        .admin-sidebar ul li { margin-bottom: 2px; }
        .admin-sidebar ul li a { display: block; padding: 12px 18px; color: #888; text-decoration: none; border-radius: 10px; transition: all 0.3s; font-size: 14px; }
        .admin-sidebar ul li a:hover, .admin-sidebar ul li a.active { background: rgba(255,255,255,0.05); color: #fff; }
        .admin-sidebar ul li a i { width: 22px; margin-right: 12px; text-align: center; }
        .admin-sidebar .logout-link { margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; }
        .admin-sidebar .logout-link a { color: #ff4444 !important; }
        .admin-sidebar .logout-link a:hover { background: rgba(255,68,68,0.1) !important; }
        .admin-content { margin-left: 260px; flex: 1; padding: 30px; min-height: 100vh; width: calc(100% - 260px); }
        .admin-content h1 { color: #fff; font-family: var(--font-primary); margin-bottom: 20px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat-card { background: rgba(255,255,255,0.03); padding: 15px 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); text-align: center; }
        .stat-card .number { font-size: 26px; font-weight: 700; color: #fff; }
        .stat-card .label { color: #888; font-size: 13px; margin-top: 5px; }
        .stat-card.blue .number { color: #17a2b8; }
        .stat-card.yellow .number { color: #ffc107; }
        .stat-card.purple .number { color: #9b59b6; }
        .stat-card.green .number { color: #28a745; }
        .stat-card.red .number { color: #dc3545; }
        
        .table-container { background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; overflow-x: auto; border: 1px solid rgba(255,255,255,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.05); }
        th { color: #aaa; font-weight: 400; font-size: 12px; letter-spacing: 1px; text-transform: uppercase; white-space: nowrap; }
        tr:hover td { background: rgba(255,255,255,0.02); }
        
        select, button { padding: 6px 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; cursor: pointer; font-size: 12px; }
        select:focus { outline: none; }
        
        .badge { padding: 4px 12px; border-radius: 50px; font-size: 11px; }
        .badge-processing { background: rgba(255,193,7,0.15); color: #ffc107; }
        .badge-approved { background: rgba(155,89,182,0.15); color: #9b59b6; }
        .badge-shipped { background: rgba(23,162,184,0.15); color: #17a2b8; }
        .badge-delivered { background: rgba(40,167,69,0.15); color: #28a745; }
        .badge-cancelled { background: rgba(220,53,69,0.15); color: #dc3545; }
        
        .no-data { text-align: center; color: #666; padding: 40px; }
        .no-data i { font-size: 50px; margin-bottom: 15px; color: #444; }
        
        .delete-btn { color: #ff4444; text-decoration: none; padding: 4px 10px; border-radius: 4px; border: 1px solid rgba(255,68,68,0.2); font-size: 12px; transition: all 0.3s; }
        .delete-btn:hover { background: rgba(255,68,68,0.1); border-color: #ff4444; }
        
        .action-cell { display: flex; gap: 5px; flex-wrap: wrap; align-items: center; }
        
        .btn-approve { padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(155,89,182,0.3); background: transparent; color: #9b59b6; cursor: pointer; font-size: 11px; transition: all 0.3s; }
        .btn-approve:hover { background: rgba(155,89,182,0.1); border-color: #9b59b6; }
        
        .btn-ship { padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(23,162,184,0.3); background: transparent; color: #17a2b8; cursor: pointer; font-size: 11px; transition: all 0.3s; }
        .btn-ship:hover { background: rgba(23,162,184,0.1); border-color: #17a2b8; }
        
        .btn-deliver { padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(40,167,69,0.3); background: transparent; color: #28a745; cursor: pointer; font-size: 11px; transition: all 0.3s; }
        .btn-deliver:hover { background: rgba(40,167,69,0.1); border-color: #28a745; }
        
        .btn-cancel { padding: 4px 12px; border-radius: 4px; border: 1px solid rgba(220,53,69,0.3); background: transparent; color: #dc3545; cursor: pointer; font-size: 11px; transition: all 0.3s; }
        .btn-cancel:hover { background: rgba(220,53,69,0.1); border-color: #dc3545; }
        
        .address-cell { font-size: 13px; color: #ccc; max-width: 200px; }
        .items-cell { font-size: 13px; color: #fff; max-width: 250px; }
        .items-cell .product-item { display: inline-block; background: rgba(255,255,255,0.05); padding: 2px 8px; border-radius: 4px; margin: 2px; font-size: 12px; }
        .items-cell .product-item .qty { color: #ffc107; }
        
        @media (max-width: 768px) { .admin-sidebar { width: 200px; } .admin-content { margin-left: 200px; padding: 15px; width: calc(100% - 200px); } .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .admin-sidebar { width: 60px; } .admin-sidebar ul li a span { display: none; } .admin-sidebar ul li a i { margin-right: 0; font-size: 18px; } .admin-content { margin-left: 60px; padding: 10px; width: calc(100% - 60px); } .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="logo">2THSND4</div>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                <li><a href="messages.php"><i class="fas fa-envelope"></i> <span>Messages</span>
                    <?php if ($unreadMessages > 0): ?>
                        <span style="background:#ff4444;color:#fff;border-radius:50%;padding:2px 8px;font-size:11px;margin-left:5px;"><?php echo $unreadMessages; ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
                <li class="logout-link"><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <h1>Orders</h1>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="number"><?php echo $totalOrders; ?></div>
                    <div class="label">Total Orders</div>
                </div>
                <div class="stat-card yellow">
                    <div class="number"><?php echo $processingOrders; ?></div>
                    <div class="label">Processing</div>
                </div>
                <div class="stat-card purple">
                    <div class="number"><?php echo $approvedOrders; ?></div>
                    <div class="label">Approved</div>
                </div>
                <div class="stat-card blue">
                    <div class="number"><?php echo $shippedOrders; ?></div>
                    <div class="label">Shipped</div>
                </div>
                <div class="stat-card green">
                    <div class="number"><?php echo $deliveredOrders; ?></div>
                    <div class="label">Delivered</div>
                </div>
                <div class="stat-card red">
                    <div class="number"><?php echo $cancelledOrders; ?></div>
                    <div class="label">Cancelled</div>
                </div>
            </div>
            
            <div class="table-container">
                <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php 
                            // Get items for this order with product names
                            $stmt = $pdo->prepare("
                                SELECT oi.*, p.name as product_name 
                                FROM order_items oi 
                                JOIN products p ON oi.product_id = p.id 
                                WHERE oi.order_id = ?
                            ");
                            $stmt->execute([$order['id']]);
                            $items = $stmt->fetchAll();
                            ?>
                            <tr>
                                <td style="font-size:13px;font-weight:600;"><?php echo $order['order_number']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo $order['phone'] ?? 'N/A'; ?></td>
                                <td class="address-cell"><?php echo $order['shipping_address'] ?? 'N/A'; ?></td>
                                <td><?php echo $order['city'] ?? 'N/A'; ?></td>
                                <td class="items-cell">
                                    <?php foreach ($items as $item): ?>
                                        <span class="product-item">
                                            <?php echo htmlspecialchars($item['product_name']); ?> 
                                            <span class="qty">x<?php echo $item['quantity']; ?></span>
                                        </span>
                                    <?php endforeach; ?>
                                </td>
                                <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($order['status']); ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td style="font-size:12px;color:#888;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="action-cell">
                                        <?php if ($order['status'] == 'Processing'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="status" value="Approved">
                                                <button type="submit" name="update_status" class="btn-approve" onclick="return confirm('Approve this order?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                        <?php elseif ($order['status'] == 'Approved'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="status" value="Shipped">
                                                <button type="submit" name="update_status" class="btn-ship" onclick="return confirm('Mark as shipped?')">
                                                    <i class="fas fa-truck"></i> Ship
                                                </button>
                                            </form>
                                        <?php elseif ($order['status'] == 'Shipped'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <input type="hidden" name="status" value="Delivered">
                                                <button type="submit" name="update_status" class="btn-deliver" onclick="return confirm('Mark as delivered?')">
                                                    <i class="fas fa-check-circle"></i> Deliver
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- CANCEL BUTTON-->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="status" value="Cancelled">
                                            <button type="submit" name="update_status" class="btn-cancel" onclick="return confirm('Cancel this order?')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                        
                                        <form method="POST" style="display:flex;gap:5px;flex-wrap:wrap;">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status">
                                                <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="Approved" <?php echo $order['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                                <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="Delivered" <?php echo $order['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status">Update</button>
                                        </form>
                                        <a href="orders.php?delete=<?php echo $order['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-shopping-cart"></i>
                        <p>No orders yet.</p>
                    </div>
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