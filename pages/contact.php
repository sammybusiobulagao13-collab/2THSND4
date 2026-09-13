<?php
session_start();
require_once '../database/config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']);

$errorMessage = '';
$showPopup = false;

//ONLY PROCESS IF LOGGED IN AND FORM SUBMITTED
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    //KUNG WALA KA-LOGIN, DILI PEDE MO-SEND
    if (!$isLoggedIn) {
        $errorMessage = 'Please login or sign up first to send a message.';
    } else {
        //KUNG NAKA-LOGIN, PROCESS ANG MESSAGE
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Name is required.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required.';
        }
        if (empty($subject)) {
            $errors['subject'] = 'Subject is required.';
        }
        if (empty($message)) {
            $errors['message'] = 'Message is required.';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO messages (name, email, subject, message, status, created_at) 
                    VALUES (?, ?, ?, ?, 'unread', NOW())
                ");
                $stmt->execute([$name, $email, $subject, $message]);
                
                $_SESSION['contact_success'] = true;
                header('Location: contact.php');
                exit();
                
            } catch (PDOException $e) {
                $errorMessage = 'Something went wrong. Please try again.';
            }
        }
    }
}

// Check for session messages
if (isset($_SESSION['contact_success'])) {
    $showPopup = true;
    unset($_SESSION['contact_success']);
}

if (isset($_SESSION['contact_error'])) {
    $errorMessage = $_SESSION['contact_error'];
    unset($_SESSION['contact_error']);
}
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
                <li><a href="contact.php" class="active">CONTACT</a></li>
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
    <?php if (isset($_SESSION['user'])): ?>
        <!-- PROFILE CARD -->
        <div class="dropdown-profile">
            <div class="dropdown-avatar">
                <?php echo strtoupper(substr($_SESSION['user']['name'], 0, 1)); ?>
            </div>
            <div class="dropdown-user-info">
                <span class="dropdown-name"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                <span class="dropdown-email"><?php echo htmlspecialchars($_SESSION['user']['email']); ?></span>
            </div>
        </div>
        <div class="dropdown-divider"></div>
    <?php endif; ?>
    
    <ul>
        <?php if (!isset($_SESSION['user'])): ?>
            <li><a href="../login/login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
        <?php endif; ?>
        
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="view_orders.php"><i class="fas fa-box"></i> My Orders</a></li>
            <li><a href="#" onclick="showLogoutModal(event)" class="logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php endif; ?>
    </ul>
</div>
        </div>
    </div>
</nav>

<section class="contact-page">
    <div class="container">
        <div class="contact-page-content">
            <h1>CONTACT US</h1>
            <p class="contact-subtitle">We'd love to hear from you! Reach out to us with any questions or feedback.</p>
            
            <!-- LOGIN NOTICE - IF NOT LOGGED IN -->
            <?php if (!$isLoggedIn): ?>
                <div class="login-notice">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Please <a href="../login/login.php?redirect=contact">Login</a> or 
                    <a href="../login/login.php?redirect=contact">Sign Up</a> to send us a message.
                </div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-info">
                <div class="contact-item">
                    <span class="contact-icon"></span>
                    <h3>Email</h3>
                    <p>2THSND4@gmail.com</p>
                </div>
                <div class="contact-item">
                    <span class="contact-icon"></span>
                    <h3>Phone</h3>
                    <p>+63 936 313 6992</p>
                </div>
                <div class="contact-item">
                    <span class="contact-icon"></span>
                    <h3>Address</h3>
                    <p>Dumaguete City, Neg Or. Philippines</p>
                </div>
            </div>
            
            <!--SHOW FORM ONLY IF LOGGED IN -->
            <?php if ($isLoggedIn): ?>
                <form class="contact-form" method="POST" action="" id="contactForm">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" 
                               value="<?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : ''; ?>" 
                               required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" 
                               value="<?php echo isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : ''; ?>" 
                               required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
                </form>
            <?php else: ?>
                <!-- ⛔ SHOW LOGIN REQUIRED MESSAGE -->
                <div class="login-required">
                    <i class="fas fa-lock"></i>
                    <h3>Login Required</h3>
                    <p>Please login or sign up to send us a message.</p>
                    <a href="../login/login.php?redirect=contact" class="btn btn-primary">Login / Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SUCCESS POPUP -->
<div class="popup-overlay" id="successPopup" style="<?php echo $showPopup ? 'display: flex;' : 'display: none;'; ?>">
    <div class="popup-content">
        <div class="popup-icon">✅</div>
        <h2>Message Sent!</h2>
        <p>Your message has been sent successfully. We'll get back to you soon!</p>
        <button class="btn btn-primary" onclick="closeSuccessPopup()">OK</button>
    </div>
</div>

<!-- ERROR POPUP -->
<div class="popup-overlay" id="errorPopup" style="display: none;">
    <div class="popup-content">
        <div class="popup-icon">❌</div>
        <h2>Error!</h2>
        <p id="errorMessageText">Something went wrong. Please try again.</p>
        <button class="btn btn-primary" onclick="closeErrorPopup()">OK</button>
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
function closePopup(popupId) {
    if (popupId) {
        const popup = document.getElementById(popupId);
        if (popup) {
            popup.style.display = 'none';
        }
    } else {
        const successPopup = document.getElementById('successPopup');
        const errorPopup = document.getElementById('errorPopup');
        if (successPopup) successPopup.style.display = 'none';
        if (errorPopup) errorPopup.style.display = 'none';
    }
}

function closeErrorPopup() {
    closePopup('errorPopup');
}

function closeSuccessPopup() {
    closePopup('successPopup');
}

function showErrorPopup(message) {
    const popup = document.getElementById('errorPopup');
    const errorText = document.getElementById('errorMessageText');
    if (popup && errorText) {
        errorText.textContent = message || 'Something went wrong. Please try again.';
        popup.style.display = 'flex';
    } else {
        alert(message || 'Something went wrong. Please try again.');
    }
}

function showLogoutModal(event) {
    event.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

document.getElementById('successPopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});

document.getElementById('errorPopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeErrorPopup();
    }
});

document.getElementById('logoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup();
        closeErrorPopup();
        closeLogoutModal();
    }
});

console.log('Contact page script loaded!');
</script>
<script src="../script.js"></script>
</body>
</html>