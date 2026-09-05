<?php
session_start();

// ===== HANDLE LOGOUT =====
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Home</title>
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
                <li><a href="index.php" class="active">HOME</a></li>
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="about.php">ABOUT</a></li>
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
         HERO SECTION
         ============================================ -->
    <section class="hero">
        <div class="container hero-container">
            <!-- Left: Content -->
            <div class="hero-content">
                
                <h1 class="hero-title">
                    <span class="express-text">Express your</span>
                    <span class="highlight">CONFIDENCE.</span>
                    <span class="wear-brand">
                        <span class="wear-text">Wear</span>
                        <span class="brand">2THSND4</span>
                    </span>
                </h1>
                
                <p class="hero-description">
                    Explore the latest fashion trends with <br>
                    premium quality outfit designed just for you
                </p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">4.9</span>
                        <span class="stat-label">⭐ Ratings</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number">3,000+</span>
                        <span class="stat-label">Trusted Customers</span>
                    </div>
                </div>
                
                <a href="shop.php" class="btn btn-primary">SHOP NOW</a>
            </div>
            
            <div class="hero-image">
                <img src='../images/herosectionmodel.png'>
            </div>
        </div>
    </section>
    
    <!-- ============================================
         QUOTE SECTION
         ============================================ -->
    <section class="quote-section">
        <div class="quote-overlay">
            <div class="container">
                <div class="quote-content">
                    <p>
                        TOO MANY BASIC FITS. NOT ENOUGH ATTITUDE. <br>
                        BUILT FOR THOSE WHO REFUSE TO BLEND IN.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- ============================================
         FEATURED COLLECTION
         ============================================ -->
<section class="featured-collection">
    <div class="container">
        <div class="section-header">
            <h2>FEATURED COLLECTION</h2>
            <p>Discover our best-selling fashion pieces</p>
        </div>
        
        <div class="carousel-wrapper">
            <div class="products-carousel" id="productsCarousel">
                
                <!-- Product 1 -->
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">   <!-- ← SIGUROHA NGA shop.php -->
                            <img src="../images/tshirts.jpg.png" alt="White Shirt">
                        </a>
                    </div>
                    <h3>White Shirt</h3>
                    <p class="price">₱1,299.00</p>
                    <a href="#" class="btn btn-add">Add to Cart</a>
                </div>
                
                <!-- Product 2 -->
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">   <!-- ← SIGUROHA NGA shop.php -->
                            <img src="../images/jeans.png" alt="Denim Jeans">
                        </a>
                    </div>
                    <h3>Denim Jeans</h3>
                    <p class="price">₱1,899.00</p>
                    <a href="#" class="btn btn-add">Add to Cart</a>
                </div>
                
                <!-- Product 3 -->
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">   <!-- ← SIGUROHA NGA shop.php -->
                            <img src="../images/caps.png" alt="Black Cap">
                        </a>
                    </div>
                    <h3>Black Cap</h3>
                    <p class="price">₱999.00</p>
                    <a href="#" class="btn btn-add">Add to Cart</a>
                </div>
                
                <!-- Product 4 -->
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">   <!-- ← SIGUROHA NGA shop.php -->
                            <img src="../images/hoddies.png" alt="Oversized Hoody">
                        </a>
                    </div>
                    <h3>Oversized Hoody</h3>
                    <p class="price">₱1,599.00</p>
                    <a href="#" class="btn btn-add">Add to Cart</a>
                </div>
                
                <!-- Product 5 -->
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">   <!-- ← SIGUROHA NGA shop.php -->
                            <img src="../images/jorts.png" alt="Baggy White Jort">
                        </a>
                    </div>
                    <h3>Baggy White Jort</h3>
                    <p class="price">₱1,199.00</p>
                    <a href="#" class="btn btn-add">Add to Cart</a>
                </div>
                
                <!-- Product 6 -->
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">   <!-- ← SIGUROHA NGA shop.php -->
                            <img src="../images/muscletee.jpg" alt="Muscle Tee">
                        </a>
                    </div>
                    <h3>Muscle Tee</h3>
                    <p class="price">₱899.00</p>
                    <a href="#" class="btn btn-add">Add to Cart</a>
                </div>
                
            </div>
        </div>
    </div>
</section>
    
    <!-- ============================================
         BUY MORE, SAVE MORE SECTION
         ============================================ -->
    <section class="buy-more-section">
        <div class="container">
            <div class="buy-more-wrapper">
                
                <!-- LEFT: Image -->
                <div class="buy-more-image">
                    <img src="../images/bmmodel.png" alt="Buy More Save More">
                </div>
                
                <!-- RIGHT: Content -->
                <div class="buy-more-content">
                    
                    <!-- Features -->
                    <div class="features-list">
                        <div class="feature-item">
                            <span class="feature-icon">✔️</span>
                            <span class="feature-text">Secure Checkout</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✔️</span>
                            <span class="feature-text">Premium Quality Fabrics</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✔️</span>
                            <span class="feature-text">Limited Drops only</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✔️</span>
                            <span class="feature-text">Shipping Nationwide</span>
                        </div>
                    </div>
                    
                    <!-- Title & Button -->
                    <h2>BUY MORE, <br><span class="highlight">SAVE MORE</span></h2>
                    <a href="shop.php" class="btn btn-primary">GET YOURS NOW</a>
                    
                </div>
                
            </div>
        </div>
    </section>
    
    <!-- ============================================
         RATINGS SECTION
         ============================================ -->
    <section class="ratings-section">
        <div class="container">
            <div class="ratings-content">
                
                <!-- Rating Number + Stars -->
                <div class="rating-header">
                    <span class="rating-number">4.9</span>
                    <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                </div>
                
                <!-- Rating Text -->
                <p class="rating-text">
                    REAL REVIEWS FROM CUSTOMERS <br>
                    WHO WEAR 2THSND4 WITH CONFIDENCE.
                </p>
                
                <!-- Profile Images -->
                <div class="reviewers">
                    <div class="reviewer">
                        <img src="../images/r1.png" alt="Reviewer 1">
                        <span class="reviewer-name">Jhon D.</span>
                    </div>
                    <div class="reviewer">
                        <img src="../images/r2.png" alt="Reviewer 2">
                        <span class="reviewer-name">Anna R.</span>
                    </div>
                    <div class="reviewer">
                        <img src="../images/r3.png" alt="Reviewer 3">
                        <span class="reviewer-name">Michael V.</span>
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
                
                <!-- QUICK LINKS -->
                <div class="footer-col">
                    <h4>QUICK LINKS</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <!-- CUSTOMER SERVICE -->
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
                
                <!-- FOLLOW US -->
                <div class="footer-col">
                    <h4>FOLLOW US</h4>
                    <ul>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">Tiktok</a></li>
                        <li><a href="#">Facebook</a></li>
                    </ul>
                </div>
                
            </div>
            
            <!-- COPYRIGHT -->
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
    
    <!-- ============================================
         JAVASCRIPT
         ============================================ -->
    <script src="../script.js"></script>
    
</body>
</html>