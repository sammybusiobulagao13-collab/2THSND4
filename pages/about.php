<?php
session_start();

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
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
                <li><a href="about.php" class="active">ABOUT</a></li>
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

<!--ABOUT PAGE -->
<section class="about-page">
    <div class="container">
        <div class="about-page-content">
    <div class="about-hero">
    <img src="../images/about.png" alt="2THSND4 Team" class="about-hero-image">
    <div class="about-hero-overlay"></div>
    <div class="about-hero-text">
        <h1>ABOUT US</h1>
        <div class="about-description">
            <p>To elevate the 2THSND4 brand into a globally recognized streetwear staple, while continuously refining our design skills and pushing creative boundaries. We are committed to sharing our art with the world, delivering maximum comfort to everyday wearers, and empowering individuals to express their confidence and unique identity through bold apparel.</p>
            <p>2THSND4 was born from the idea that everyone deserves to feel confident in what they wear. We combine premium quality fabrics with modern designs to create outfits that make you stand out.</p>
        </div>
    </div>
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
            
            <!--CUSTOMER SERVICE -->
            <div class="about-customer-service">
                <h2>CUSTOMER SERVICE</h2>
                
                <!-- FAQS  -->
                <div class="service-section" id="faqs">
                    <div class="service-header">
                        <span class="service-icon">❓</span>
                        <h3>FAQs</h3>
                    </div>
                    <div class="service-content">
                        <div class="faq-item">
                            <h4>What payment methods do you accept?</h4>
                            <p>We accept credit/debit cards, GCash, and bank transfers.</p>
                        </div>
                        <div class="faq-item">
                            <h4>How long does shipping take?</h4>
                            <p>Shipping takes 3-7 days within Philippines.</p>
                        </div>
                        <div class="faq-item">
                            <h4>Do you offer international shipping?</h4>
                            <p>Currently, we only ship within the Philippines.</p>
                        </div>
                        <div class="faq-item">
                            <h4>Can I cancel my order?</h4>
                            <p>Orders can be cancelled within 24 hours of placement.</p>
                        </div>
                    </div>
                </div>
                
                <!--  SHIPPING INFORMATION  -->
                <div class="service-section" id="shipping">
                    <div class="service-header">
                        <span class="service-icon">📦</span>
                        <h3>Shipping Information</h3>
                    </div>
                    <div class="service-content">
                        <p><strong>Delivery Time:</strong> 3-7 days (Philippines).</p>
                        <p><strong>Shipping Fee:</strong> <strong>FREE</strong> Shipping</p>
                        <p><strong>Tracking:</strong> You will receive a tracking number via email once your order is shipped.</p>
                        <p><strong>Delivery Partners:</strong> We partner with LBC, J&T Express, and GrabExpress.</p>
                    </div>
                </div>
                
                <!--  RETURN & EXCHANGE  -->
                <div class="service-section" id="returns">
                    <div class="service-header">
                        <span class="service-icon">🔄</span>
                        <h3>Return & Exchange</h3>
                    </div>
                    <div class="service-content">
                        <p><strong>30-Day Return Policy:</strong> You may return items within 30 days of delivery.</p>
                        <p><strong>Conditions:</strong> Items must be unworn, unwashed, and with original tags.</p>
                        <p><strong>Exchange:</strong> Free exchange for size or color within 30 days.</p>
                        <p><strong>Refund:</strong> Refunds are processed within 5-7 business days after we receive the returned item.</p>
                    </div>
                </div>
                
                <!--  PRIVACY POLICY  -->
                <div class="service-section" id="privacy">
                    <div class="service-header">
                        <span class="service-icon">🔒</span>
                        <h3>Privacy Policy</h3>
                    </div>
                    <div class="service-content">
                        <p>Your privacy is important to us. We collect personal information only to process your orders and improve your shopping experience.</p>
                        <p>We do not share your information with third parties without your consent.</p>
                        <p>We use secure encryption to protect your payment information.</p>
                    </div>
                </div>
                
                <!-- TERMS & CONDITIONS  -->
                <div class="service-section" id="terms">
                    <div class="service-header">
                        <span class="service-icon">📜</span>
                        <h3>Terms & Conditions</h3>
                    </div>
                    <div class="service-content">
                        <p>By using our website, you agree to these terms and conditions.</p>
                        <p>All content, images, and products are the property of 2THSND4.</p>
                        <p>Prices are subject to change without prior notice.</p>
                        <p>We reserve the right to cancel orders due to stock unavailability.</p>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</section>

<!--  LOGOUT CONFIRMATION MODAL  -->
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

<script src="../script.js"></script>
</body>
</html>