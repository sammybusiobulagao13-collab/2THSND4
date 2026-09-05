<?php
// ===== GET SEARCH QUERY =====
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Shop</title>
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
            <div class="nav-logo">
                <a href="index.php">
                    <img src='../images/headerlogo.png' alt="2THSND4 Logo">
                </a>
            </div>
            
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="shop.php" class="active">SHOP</a></li>
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
                        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                        <li><a href="#"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
                        <li><a href="#"><i class="fas fa-history"></i> History</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- ===== SHOP PAGE ===== -->
    <section class="shop-page">
        <div class="container">
            <div class="shop-header">
                <h1>OUR COLLECTION</h1>
                <?php if ($searchQuery): ?>
                    <p>Showing results for: <strong>"<?php echo htmlspecialchars($searchQuery); ?>"</strong></p>
                <?php else: ?>
                    <p>Discover all our fashion pieces</p>
                <?php endif; ?>
            </div>
            
            <div class="shop-grid">
                <?php
                // ===== PRODUCTS =====
                $products = [
                    ['name' => 'White Shirt', 'price' => '₱1,299.00', 'image' => 'tshirts.jpg.png', 'stock' => 5],
                    ['name' => 'Jeans', 'price' => '₱1,899.00', 'image' => 'jeans.png', 'stock' => 5],
                    ['name' => 'Black Cap', 'price' => '₱999.00', 'image' => 'caps.png', 'stock' => 5],
                    ['name' => 'Oversized Hoody', 'price' => '₱1,599.00', 'image' => 'hoddies.png', 'stock' => 5],
                    ['name' => 'Baggy White Jorts', 'price' => '₱1,199.00', 'image' => 'jorts.png', 'stock' => 5],
                    ['name' => 'Muscle tee', 'price' => '₱899.00', 'image' => 'muscletee.jpg', 'stock' => 5],
                ];
                
                // ===== FILTER PRODUCTS =====
                $filteredProducts = $products;
                if ($searchQuery) {
                    $filteredProducts = [];
                    foreach ($products as $product) {
                        if (stripos($product['name'], $searchQuery) !== false) {
                            $filteredProducts[] = $product;
                        }
                    }
                }
                
                // ===== DISPLAY =====
                if (count($filteredProducts) > 0) {
                    foreach ($filteredProducts as $product) {
                        // Clean price (remove ₱ and commas)
                        $cleanPrice = str_replace('₱', '', str_replace(',', '', $product['price']));
                        
                        echo '
                        <div class="product-card">
                            <div class="product-image">
                                <img src="../images/' . $product['image'] . '" alt="' . $product['name'] . '">
                            </div>
                            <h3>' . $product['name'] . '</h3>
                            <p class="price">' . $product['price'] . '</p>
                            <a href="#" class="btn btn-add" 
                               data-name="' . $product['name'] . '"
                               data-price="' . $cleanPrice . '"
                               data-image="' . $product['image'] . '"
                               data-stock="' . $product['stock'] . '">
                               Add to Cart
                            </a>
                        </div>
                        ';
                    }
                } else {
                    echo '
                    <div class="no-results">
                        <p>😕 No products found for <strong>"' . htmlspecialchars($searchQuery) . '"</strong></p>
                        <p>Try searching for: <span class="suggestions">T-shirts, Jeans, Caps, Hoodies, Jorts</span></p>
                        <a href="shop.php" class="btn btn-primary">View All Products</a>
                    </div>
                    ';
                }
                ?>
            </div>
        </div>
    </section>
    
    <!-- ===== FOOTER ===== -->
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