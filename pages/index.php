<?php
session_start();
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
  
<nav class="navbar">
    <div class="container">
      
        <div class="nav-logo">
            <a href="index.php">
                <img src='../images/headerlogo.png' alt="2THSND4 Logo">
            </a>
        </div>
        
   
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="index.php" class="active">HOME</a></li>
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="about.php">ABOUT</a></li>
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
    
  
    <section class="hero">
        <div class="container hero-container">
         
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
    
<section class="featured-collection">
    <div class="container">
        <div class="section-header">
            <h2>FEATURED COLLECTION</h2>
            <p>Discover our best-selling fashion pieces</p>
        </div>
        
        <div class="carousel-wrapper">
            <div class="products-carousel" id="productsCarousel">
                
                <?php
                //FEATURED PRODUCTS
                $featuredProducts = [
                    ['id' => 1, 'name' => 'White Shirt', 'price' => '₱1,299.00', 'image' => 'tshirts.jpg.png', 'stock' => 5],
                    ['id' => 2, 'name' => 'Denim Jeans', 'price' => '₱1,899.00', 'image' => 'jeans.png', 'stock' => 5],
                    ['id' => 3, 'name' => 'Black Cap', 'price' => '₱999.00', 'image' => 'caps.png', 'stock' => 5],
                    ['id' => 4, 'name' => 'Oversized Hoody', 'price' => '₱1,599.00', 'image' => 'hoddies.png', 'stock' => 5],
                    ['id' => 5, 'name' => 'Baggy White Jort', 'price' => '₱1,199.00', 'image' => 'jorts.png', 'stock' => 5],
                    ['id' => 6, 'name' => 'Muscle Tee', 'price' => '₱899.00', 'image' => 'muscletee.jpg', 'stock' => 5],
                ];
                
                foreach ($featuredProducts as $product):
                    $cleanPrice = str_replace('₱', '', str_replace(',', '', $product['price']));
                ?>
                
                <div class="product-card">
                    <div class="product-image">
                        <a href="shop.php">
                            <img src="../images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </a>
                    </div>
                    <h3><?php echo $product['name']; ?></h3>
                    <p class="price"><?php echo $product['price']; ?></p>
                    <a href="#" class="btn btn-add" 
                       data-id="<?php echo $product['id']; ?>"
                       data-name="<?php echo $product['name']; ?>"
                       data-price="<?php echo $cleanPrice; ?>"
                       data-image="<?php echo $product['image']; ?>"
                       data-stock="<?php echo $product['stock']; ?>">
                       Add to Cart
                    </a>
                </div>
                
                <?php endforeach; ?>
                
            </div>
        </div>
    </div>
</section>   


    <section class="buy-more-section">
        <div class="container">
            <div class="buy-more-wrapper">
                
             
                <div class="buy-more-image">
                    <img src="../images/bmmodel.png" alt="Buy More Save More">
                </div>
                
               
                <div class="buy-more-content">
                    
               
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
                    
                
                    <h2>BUY MORE, <br><span class="highlight">SAVE MORE</span></h2>
                    <a href="shop.php" class="btn btn-primary">GET YOURS NOW</a>
                    
                </div>
                
            </div>
        </div>
    </section>
    
  
    <section class="ratings-section">
        <div class="container">
            <div class="ratings-content">
                
           
                <div class="rating-header">
                    <span class="rating-number">4.9</span>
                    <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                </div>
                
         
                <p class="rating-text">
                    REAL REVIEWS FROM CUSTOMERS <br>
                    WHO WEAR 2THSND4 WITH CONFIDENCE.
                </p>
                
           
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
        <li><a href="about.php#faqs">FAQs</a></li>
        <li><a href="about.php#shipping">Shipping Information</a></li>
        <li><a href="about.php#returns">Return & Exchange</a></li>
        <li><a href="about.php#privacy">Privacy Policy</a></li>
        <li><a href="about.php#terms">Terms & Conditions</a></li>
    </ul>
</div>
            
            
            <div class="footer-col">
                <h4>FOLLOW US</h4>
                <ul>
                    <li><a href="https://www.instagram.com/" target="_blank">Instagram</a></li>
                    <li><a href="https://www.tiktok.com/" target="_blank">Tiktok</a></li>
                    <li><a href="https://www.facebook.com/" target="_blank">Facebook</a></li>
                </ul>
            </div>
            
        </div>
        
        
        <div class="footer-bottom">
            <p>&copy; 2026 2THSND4. All Rights Reserved.</p>
        </div>
    </div>
</footer>

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