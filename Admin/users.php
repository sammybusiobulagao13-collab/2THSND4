<?php
session_start();
require_once '../database/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header('Location: ../login/login.php');
    exit();
}

// Get ALL users (including admin)
$users = $pdo->query("
    SELECT * FROM users 
    ORDER BY created_at DESC
")->fetchAll();

// Get counts
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Admin (Users)</title>
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
        .admin-content .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .admin-content .header h1 {
            color: #fff;
            font-family: var(--font-primary);
            font-size: 28px;
        }
        .admin-content .header .stats {
            color: #888;
            font-size: 14px;
        }
        .admin-content .header .stats strong {
            color: #fff;
        }
        
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
        .stat-card.green .number { color: #28a745; }
        .stat-card.yellow .number { color: #ffc107; }
        
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
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }
        .user-avatar.admin { background: rgba(23,162,184,0.2); color: #17a2b8; }
        .user-avatar.user { background: rgba(255,255,255,0.05); color: #fff; }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-info .name {
            font-weight: 600;
        }
        .user-info .email {
            color: #888;
            font-size: 13px;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
        }
        .badge-admin { background: rgba(23,162,184,0.15); color: #17a2b8; }
        .badge-user { background: rgba(40,167,69,0.15); color: #28a745; }
        
        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
        }
        .no-data i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #444;
        }
        
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
            .user-info .email { display: none; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="logo">2THSND4</div>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                <li><a href="users.php" class="active"><i class="fas fa-users"></i> <span>Users</span></a></li>
               <li class="logout-link"><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>
        
        <!-- Content -->
        <div class="admin-content">
            <div class="header">
                <h1>👥 Users</h1>
                <div class="stats">
                    Total: <strong><?php echo $totalUsers; ?></strong> users
                </div>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="number"><?php echo $totalUsers; ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card green">
                    <div class="number"><?php echo $totalCustomers; ?></div>
                    <div class="label">Customers</div>
                </div>
                <div class="stat-card yellow">
                    <div class="number"><?php echo $totalAdmins; ?></div>
                    <div class="label">Admins</div>
                </div>
            </div>
            
            <div class="table-container">
                <?php if (count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar <?php echo $user['is_admin'] == 1 ? 'admin' : 'user'; ?>">
                                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="name"><?php echo htmlspecialchars($user['name']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['is_admin'] == 1): ?>
                                        <span class="badge badge-admin">👑 Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">👤 User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-users"></i>
                        <p>No users yet.</p>
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