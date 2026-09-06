<?php
session_start();

if (isset($_SESSION['user'])) {
    
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';
    if ($redirect === 'checkout') {
        header('Location: ../pages/checkout.php');
    } else {
        header('Location: ../pages/index.php');
    }
    exit();
}


$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';


$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    
    if ($email === 'test@email.com' && $password === 'password') {
        $_SESSION['user'] = [
            'name' => 'Test User',
            'email' => $email
        ];
        if ($redirect === 'checkout') {
            header('Location: ../pages/checkout.php');
        } else {
            header('Location: ../pages/index.php');
        }
        exit();
    } else {
        $loginError = 'Invalid email or password!';
    }
}


$registerError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirmPassword) {
        $registerError = 'Passwords do not match!';
    } elseif (empty($name) || empty($email) || empty($password)) {
        $registerError = 'Please fill in all fields!';
    } else {
        $_SESSION['user'] = [
            'name' => $name,
            'email' => $email
        ];
        if ($redirect === 'checkout') {
            header('Location: ../pages/checkout.php');
        } else {
            header('Location: ../pages/index.php');
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2THSND4 - Login / Sign Up</title>
    <link rel="icon" type="image/png" href="../images/forwbe.png">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    
   
    <nav class="navbar">
        <div class="container">
            <div class="nav-logo">
                <a href="../pages/index.php">
                    <img src='../images/headerlogo.png' alt="2THSND4 Logo">
                </a>
            </div>
            <div class="nav-right">
                <ul class="nav-links">
                    <li><a href="../pages/index.php">HOME</a></li>
                    <li><a href="../pages/shop.php">SHOP</a></li>
                    <li><a href="../pages/about.php">ABOUT</a></li>
                    <li><a href="../pages/contact.php">CONTACT</a></li>
                </ul>
                <div class="nav-icons">
                    <a href="../pages/cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <ul>
                        <li><a href="../pages/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                        <li><a href="login.php?redirect=home"><i class="fas fa-user"></i> Log In / Sign Up</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

  
    <section class="login-page">
        <div class="container">
            <div class="login-wrapper">
                
                <!-- Login Form -->
                <div class="login-form-container">
                    <h2>LOGIN</h2>
                    <?php if ($loginError): ?>
                        <div class="error-message"><?php echo $loginError; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="login.php?redirect=<?php echo $redirect; ?>">
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                    </form>
                    <p class="form-switch">Don't have an account? <a href="#" onclick="toggleForms()">Sign Up</a></p>
                </div>
                
                <!-- Register Form -->
                <div class="register-form-container" style="display: none;">
                    <h2>SIGN UP</h2>
                    <?php if ($registerError): ?>
                        <div class="error-message"><?php echo $registerError; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="login.php?redirect=<?php echo $redirect; ?>">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary">Sign Up</button>
                    </form>
                    <p class="form-switch">Already have an account? <a href="#" onclick="toggleForms()">Login</a></p>
                </div>
                
                <!-- Back to Cart -->
                <div class="form-back">
                    <a href="../pages/cart.php">← Back to Cart</a>
                </div>
                
            </div>
        </div>
    </section>

   
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>QUICK LINKS</h4>
                    <ul>
                        <li><a href="../pages/index.php">Home</a></li>
                        <li><a href="../pages/shop.php">Shop</a></li>
                        <li><a href="../pages/about.php">About</a></li>
                        <li><a href="../pages/contact.php">Contact</a></li>
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

    <script>
        function toggleForms() {
            const loginForm = document.querySelector('.login-form-container');
            const registerForm = document.querySelector('.register-form-container');
            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
        }
    </script>
    <script src="../script.js"></script>
</body>
</html>