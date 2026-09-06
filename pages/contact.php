<?php
session_start();

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    session_unset();
    session_destroy();
    header('Location: shop.php');
    exit();
}


$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';  // ← IDUGANG NI!
?>

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
                <li><a href="contact.php"class="active">CONTACT</a></li>
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
        <li><a href="#"><i class="fas fa-user"></i> <?php echo $_SESSION['user']['name']; ?></a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        
        <?php if (isset($_SESSION['user'])): ?>
    
            
        <?php else: ?>
          
            <li><a href="../login/login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
        <?php endif; ?>
        
       <li><a href="history.php"><i class="fas fa-history"></i> History</a></li>
        <li>
                <a href="#" onclick="showLogoutModal(event)">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
    </ul>
</div>
</nav>
    
    

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
                
               <form class="contact-form" onsubmit="submitContactForm(event)">
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

    <div class="popup-overlay" id="successPopup" style="display: none;">
        <div class="popup-content">
            <div class="popup-icon">✅</div>
            <h2>Message Sent!</h2>
            <p>Your message has been sent successfully. We'll get back to you soon!</p>
            <button class="btn btn-primary" onclick="closePopup()">OK</button>
        </div>
    </div>


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
function submitContactForm(event) {
    event.preventDefault();
    
    try {
        const name = document.querySelector('.contact-form input[type="text"]');
        const email = document.querySelector('.contact-form input[type="email"]');
        const subject = document.querySelector('.contact-form input[placeholder="Subject"]');
        const message = document.querySelector('.contact-form textarea');
        
        if (!name.value || !email.value || !subject.value || !message.value) {
            alert('Please fill in all fields.');
            return;
        }
        
        document.getElementById('successPopup').style.display = 'flex';
        document.querySelector('.contact-form').reset();
    } catch (error) {
        console.log('Error:', error);
        alert('Something went wrong. Please try again.');
    }
}

function closePopup() {
    document.getElementById('successPopup').style.display = 'none';
}

console.log('Contact page script loaded!');
</script>
    <script src="../script.js"></script>
</body>
</html>