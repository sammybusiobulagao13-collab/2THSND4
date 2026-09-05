<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Contact</title>
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
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="contact.php"class="active">CONTACT</a></li>
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
            
            <!-- ===== DROPDOWN MENU (ILALOM SA MENU ICON) ===== -->
<div class="dropdown-menu" id="dropdownMenu">
    <ul>
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        <li><a href="#"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
        <li><a href="#"><i class="fas fa-history"></i> History</a></li>
    </ul>
</div>
</nav>
    
    
    <!-- ============================================
         CONTACT PAGE
         ============================================ -->
    <section class="contact-page">
        <div class="container">
            <div class="contact-page-content">
                <h1>CONTACT US</h1>
                <p class="contact-subtitle">We'd love to hear from you! Reach out to us with any questions or feedback.</p>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <span class="contact-icon">📧</span>
                        <h3>Email</h3>
                        <p>2THSND4@gmail.com</p>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📱</span>
                        <h3>Phone</h3>
                        <p>+63 936 313 6992</p>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <h3>Address</h3>
                        <p>Dumaguete City, Neg Or. Philippines</p>
                    </div>
                </div>
                
                <form class="contact-form">
                    <div class="form-group">
                        <input type="text" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <textarea placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
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
    
    <script src="../script.js"></script>
</body>
</html>