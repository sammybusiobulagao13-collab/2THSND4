<?php
session_start();

// ===== HANDLE LOGOUT =====
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header('Location: shop.php');
    exit();
}

// ===== GET SEARCH QUERY =====
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';  // ← IDUGANG NI!
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - About</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>

 <!-- ============================================
         HEADER / NAVIGATION
         ============================================ -->
<nav class="navbar">
    <div class="container">
        <!-- Logo -->
        <div class="nav-logo">
            <a href="index.php">
                <img src='../images/headerlogo.png' alt="2THSND4 Logo">
            </a>
        </div>
        
        <!-- Nav Links & Icons -->
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="about.php"class="active">ABOUT</a></li>
                <li><a href="contact.php">CONTACT</a></li>
            </ul>
            
            <div class="nav-icons">
                <!-- Search with Dropdown -->
                <div class="search-wrapper">
                    <a href="#" class="search-icon" id="searchToggle"><i class="fas fa-search"></i></a>
                    <div class="search-dropdown" id="searchDropdown">
                        <input type="text" placeholder="Search products..." id="searchInput">
                        <button type="button"><i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <!-- ===== DROPDOWN MENU ===== -->
<div class="dropdown-menu" id="dropdownMenu">
    <ul>
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        
        <?php if (isset($_SESSION['user'])): ?>
            <!-- NAAY NAKA-LOGIN -->
            <li><a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['user']['name']; ?></a></li>
            <li>
                <a href="#" onclick="showLogoutModal(event)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        <?php else: ?>
            <!-- WALA NAKA-LOGIN -->
            <li><a href="../login/login.php"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
        <?php endif; ?>
        
        <li><a href="#"><i class="fas fa-history"></i> History</a></li>
    </ul>
</div>
</nav>

    <!-- ============================================
         ABOUT PAGE
         ============================================ -->
    <section class="about-page">
        <div class="container">
            <div class="about-page-content">
                <h1>ABOUT US</h1>
                
                <div class="about-description">
                    <p>To elevate the 2THSND4 brand into a globally recognized 
streetwear staple, while continuously refining our design skills 
and pushing creative boundaries. 
We are committed to sharing our art with the world, 
delivering maximum comfort to everyday wearers, 
and empowering individuals to express their confidence and 
unique identity through bold apparel.</p>
                    <p>2THSND4 was born from the idea that everyone deserves to feel confident in what they wear. We combine premium quality fabrics with modern designs to create outfits that make you stand out.</p>
                </div>
                
                <div class="about-features">
                    <div class="about-feature">
                        <span class="about-icon">✅</span>
                        <h3>Premium Quality Materials</h3>
                        <p>We use only the finest fabrics for comfort and durability.</p>
                    </div>
                    <div class="about-feature">
                        <span class="about-icon">✅</span>
                        <h3>Sustainable & Ethical Fashion</h3>
                        <p>We care about the planet and our workers.</p>
                    </div>
                    <div class="about-feature">
                        <span class="about-icon">✅</span>
                        <h3>Free Shipping on Orders ₱2,000+</h3>
                        <p>Enjoy free delivery on all orders over ₱2,000.</p>
                    </div>
                    <div class="about-feature">
                        <span class="about-icon">✅</span>
                        <h3>30-Day Return Policy</h3>
                        <p>Not satisfied? Return within 30 days for a full refund.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ============================================
         FOOTER
         ============================================ -->
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

    <!-- ===== LOGOUT CONFIRMATION MODAL ===== -->
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
    
    <script src="../script.js"></script>
</body>
</html>