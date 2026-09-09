<?php
session_start();
require_once '../database/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: ../login/login.php');
    exit();
}

// Get all messages
$messages = $pdo->query("
    SELECT * FROM messages 
    ORDER BY created_at DESC
")->fetchAll();

// Get counts
$totalMessages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unreadMessages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'unread'")->fetchColumn();

// Mark as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit();
}

// Delete message
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: messages.php');
    exit();
}

// Mark all as read
if (isset($_GET['mark_all_read'])) {
    $pdo->query("UPDATE messages SET status = 'read' WHERE status = 'unread'");
    header('Location: messages.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Admin (Messages)</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #0a0a1a; }
        
        .admin-container { display: flex; min-height: 100vh; }
        
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
        .admin-content h1 { color: #fff; font-family: var(--font-primary); margin-bottom: 20px; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: rgba(255,255,255,0.03);
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.05);
            text-align: center;
        }
        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
        }
        .stat-card .label {
            color: #888;
            font-size: 13px;
            margin-top: 5px;
        }
        .stat-card.blue .number { color: #17a2b8; }
        .stat-card.yellow .number { color: #ffc107; }
        .stat-card.green .number { color: #28a745; }
        
        .table-container {
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 20px;
            overflow-x: auto;
            border: 1px solid rgba(255,255,255,0.05);
        }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px 15px;
            text-align: left;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        th {
            color: #aaa;
            font-weight: 400;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        tr:hover td { background: rgba(255,255,255,0.02); }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
        }
        .status-unread { background: rgba(255,193,7,0.15); color: #ffc107; }
        .status-read { background: rgba(40,167,69,0.15); color: #28a745; }
        
        .message-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .action-btn {
            color: #17a2b8;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-btn.delete {
            color: #ff4444;
        }
        .action-btn:hover { text-decoration: underline; }
        
        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
        }
        .no-data i { font-size: 50px; margin-bottom: 15px; color: #444; }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-mark-all {
            padding: 10px 25px;
            border: 2px solid #17a2b8;
            background: transparent;
            color: #17a2b8;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            text-decoration: none;
        }
        .btn-mark-all:hover {
            background: rgba(23,162,184,0.15);
        }
        
        .message-detail {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            display: none;
        }
        .message-detail.active { display: block; }
        .message-detail .msg-label {
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .message-detail .msg-value {
            color: #fff;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .message-detail .msg-value.full-message {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar { width: 200px; }
            .admin-content { margin-left: 200px; padding: 15px; width: calc(100% - 200px); }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .admin-sidebar { width: 60px; }
            .admin-sidebar ul li a span { display: none; }
            .admin-sidebar ul li a i { margin-right: 0; font-size: 18px; }
            .admin-content { margin-left: 60px; padding: 10px; width: calc(100% - 60px); }
            .stats-grid { grid-template-columns: 1fr; }
        }

body {
    background-image: url('../images/texture.jpg') !important;
    background-size: cover !important;
    background-position: center !important;
    background-attachment: fixed !important;
    background-repeat: no-repeat !important;
}

/* Dark overlay */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    z-index: -1;
    pointer-events: none;
}

.admin-container {
    position: relative;
    z-index: 1;
}

.admin-sidebar {
    background: rgba(0, 0, 0, 0.85) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.stat-card {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.table-container {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.table-container table tbody tr {
    background: rgba(255, 255, 255, 0.02);
}

.table-container table tbody tr:hover {
    background: rgba(255, 255, 255, 0.08);
}

.message-detail {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.message-detail .msg-value.full-message {
    background: rgba(0, 0, 0, 0.4) !important;
}

.admin-content h1 {
    color: #fff;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

.no-data {
    color: #aaa;
}



    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="logo">
    <a href="index.php">
        <img src="../images/forwbe.png" alt="2THSND4 Logo" style="max-width:150px;height:auto;display:block;margin:0 auto;">
    </a>
</div>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                <li><a href="messages.php" class="active"><i class="fas fa-envelope"></i> <span>Messages</span>
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
            <h1>📩 Messages</h1>
            
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="number"><?php echo $totalMessages; ?></div>
                    <div class="label">Total Messages</div>
                </div>
                <div class="stat-card yellow">
                    <div class="number"><?php echo $unreadMessages; ?></div>
                    <div class="label">Unread</div>
                </div>
                <div class="stat-card green">
                    <div class="number"><?php echo $totalMessages - $unreadMessages; ?></div>
                    <div class="label">Read</div>
                </div>
            </div>
            
            <div class="header-actions">
                <div></div>
                <?php if ($unreadMessages > 0): ?>
                    <a href="messages.php?mark_all_read=1" class="btn-mark-all" onclick="return confirm('Mark all messages as read?')">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="table-container">
                <?php if (count($messages) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?php echo $msg['id']; ?></td>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td class="message-cell"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : ''); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $msg['status']; ?>">
                                        <?php echo ucfirst($msg['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?></td>
                                <td>
                                    <a href="#" class="action-btn" onclick="showMessage(<?php echo $msg['id']; ?>, '<?php echo addslashes($msg['name']); ?>', '<?php echo addslashes($msg['email']); ?>', '<?php echo addslashes($msg['subject']); ?>', '<?php echo addslashes($msg['message']); ?>', '<?php echo $msg['created_at']; ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if ($msg['status'] == 'unread'): ?>
                                        <a href="messages.php?read=<?php echo $msg['id']; ?>" class="action-btn">
                                            <i class="fas fa-check"></i> Read
                                        </a>
                                    <?php endif; ?>
                                    <a href="messages.php?delete=<?php echo $msg['id']; ?>" class="action-btn delete" onclick="return confirm('Delete this message?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-envelope"></i>
                        <p>No messages yet.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Message Detail -->
            <div class="message-detail" id="messageDetail">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
                    <h3 style="color:#fff;">Message Details</h3>
                    <button onclick="closeMessageDetail()" style="background:none;border:none;color:#888;font-size:20px;cursor:pointer;">&times;</button>
                </div>
                <div class="msg-label">From</div>
                <div class="msg-value"><strong id="detailName"></strong> (<span id="detailEmail"></span>)</div>
                <div class="msg-label">Subject</div>
                <div class="msg-value" id="detailSubject"></div>
                <div class="msg-label">Date</div>
                <div class="msg-value" id="detailDate"></div>
                <div class="msg-label">Message</div>
                <div class="msg-value full-message" id="detailMessage"></div>
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
function showMessage(id, name, email, subject, message, date) {
    document.getElementById('detailName').textContent = name;
    document.getElementById('detailEmail').textContent = email;
    document.getElementById('detailSubject').textContent = subject;
    document.getElementById('detailMessage').textContent = message;
    document.getElementById('detailDate').textContent = date;
    document.getElementById('messageDetail').classList.add('active');
    document.getElementById('messageDetail').scrollIntoView({ behavior: 'smooth' });
}

function closeMessageDetail() {
    document.getElementById('messageDetail').classList.remove('active');
}

function showLogoutModal(event) {
    event.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

document.getElementById('logoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
        closeMessageDetail();
    }
});
</script>

</body>
</html>