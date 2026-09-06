<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php?redirect=home');
    exit();
}

if (!isset($_SESSION['activity_log'])) {
    $_SESSION['activity_log'] = [];
}

// Clear history
if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    $_SESSION['activity_log'] = [];
    header('Location: history.php');
    exit();
}

$activities = $_SESSION['activity_log'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Activity History</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <!--HEADER-->
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
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['user']['name']; ?></a></li>
                            <li><a href="history.php"><i class="fas fa-history"></i> History</a></li>
                            <li><a href="#" onclick="showLogoutModal(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        <?php else: ?>
                            <li><a href="../login/login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!--HISTORY PAGE -->
    <section class="history-page">
        <div class="container">
            <div class="history-header">
                <h1>ACTIVITY HISTORY</h1>
            </div>

            <?php if (count($activities) > 0): ?>
                <div class="history-table-wrapper">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_reverse($activities) as $activity): ?>
                                <tr>
                                    <td class="action-cell">
                                        <span class="action-icon">
                                            <?php
                                            switch ($activity['type']) {
                                                case 'login': echo ''; break;
                                                case 'logout': echo ''; break;
                                                case 'cart_add': echo ''; break;
                                                case 'cart_remove': echo ''; break;
                                                case 'checkout': echo ''; break;
                                                case 'register': echo ''; break;
                                                default: echo '';
                                            }
                                            ?>
                                        </span>
                                        <?php echo $activity['message']; ?>
                                    </td>
                                    <td class="date-cell">
                                        <?php echo date('M d, Y h:i A', strtotime($activity['timestamp'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Clear History Button -->
                <div class="clear-history">
                    <a href="history.php?clear=1" class="btn-clear" onclick="return confirm('Clear all history?')">Clear History</a>
                </div>
            <?php else: ?>
                <div class="empty-history">
                    <i class="fas fa-history fa-4x"></i>
                    <h2>No activities yet</h2>
                    <p>Start shopping and your activities will appear here.</p>
                   
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!--LOGOUT MODAL-->
    <div class="logout-modal-overlay" id="logoutModal" style="display: none;">
        <div class="logout-modal">
            <div class="logout-modal-content">
                <p>Are you sure you want to log out?</p>
                <div class="logout-modal-actions">
                    <button class="btn btn-secondary" onclick="closeLogoutModal()">Cancel</button>
                    <a href="index.php?logout=1" class="btn btn-primary">Yes</a>
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

        document.addEventListener('click', function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
    <script src="../script.js"></script>
</body>
</html>