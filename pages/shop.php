<?php
session_start();

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
    
  
    <section class="shop-page">
        <div class="container">
            <div class="shop-header">
                <h1>OUR COLLECTION</h1>
                <?php if ($searchQuery): ?>
                    <p>Showing results for: <strong>"<?php echo htmlspecialchars($searchQuery); ?>"</strong></p>
                <?php else: ?>
                    <div class="shop-header-image">
            <img src="../images/fashion.png" alt="Discover our fashion pieces">
        </div>
                <?php endif; ?>
            </div>
            
            <div class="shop-grid">
                <?php
            
                $products = [
    ['id' => 1, 'name' => 'White Shirt', 'price' => '₱1,299.00', 'image' => 'tshirts.jpg.png', 'stock' => 5],
    ['id' => 2, 'name' => 'Jeans', 'price' => '₱1,899.00', 'image' => 'jeans.png', 'stock' => 5],
    ['id' => 3, 'name' => 'Black Cap', 'price' => '₱999.00', 'image' => 'caps.png', 'stock' => 5],
    ['id' => 4, 'name' => 'Oversized Hoody', 'price' => '₱1,599.00', 'image' => 'hoddies.png', 'stock' => 5],
    ['id' => 5, 'name' => 'Baggy White Jorts', 'price' => '₱1,199.00', 'image' => 'jorts.png', 'stock' => 5],
    ['id' => 6, 'name' => 'Muscle tee', 'price' => '₱899.00', 'image' => 'muscletee.jpg', 'stock' => 5],
];
                
              
                $filteredProducts = $products;
                if ($searchQuery) {
                    $filteredProducts = [];
                    foreach ($products as $product) {
                        if (stripos($product['name'], $searchQuery) !== false) {
                            $filteredProducts[] = $product;
                        }
                    }
                }
                
              
                if (count($filteredProducts) > 0) {
                    foreach ($filteredProducts as $product) {
                     
                        $cleanPrice = str_replace('₱', '', str_replace(',', '', $product['price']));
                        
                        echo '
                        <div class="product-card">
                            <div class="product-image">
                                <img src="../images/' . $product['image'] . '" alt="' . $product['name'] . '">
                            </div>
                            <h3>' . $product['name'] . '</h3>
                            <p class="price">' . $product['price'] . '</p>
                            <a href="#" class="btn btn-add" 
   data-id="' . $product['id'] . '"
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
                        <p>No products found for <strong>"' . htmlspecialchars($searchQuery) . '"</strong></p>
                        <p>Try searching for: <span class="suggestions">shirt, Jeans, Caps, Hoody, Jorts</span></p>
                        <a href="shop.php" class="btn btn-primary">View All Products</a>
                    </div>
                    ';
                }
                ?>
            </div>
            <!-- COMING SOON TEXT -->
<div class="coming-soon-container">
    <p class="coming-soon-text">More products coming soon...</p>
</div>
        </div>
    </section>
    

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