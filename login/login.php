<?php
session_start();
require_once '../pages/validation.php';

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
            'email' => $email,
            'username' => 'testuser'
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

$registerErrors = [];
$registerData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
   
    $nameError = validateRequired($name, 'Full Name');
    if ($nameError) {
        $errors['name'] = $nameError;
    }
    
    
    $emailError = validateEmailFormat($email);
    if ($emailError) {
        $errors['email'] = $emailError;
    }
    
    
    $usernameError = validateUsername($username);
    if ($usernameError) {
        $errors['username'] = $usernameError;
    }
    
    
    $passwordError = validatePassword($password);
    if ($passwordError) {
        $errors['password'] = $passwordError;
    }
    
   
    $confirmError = validateConfirmPassword($password, $confirmPassword);
    if ($confirmError) {
        $errors['confirm_password'] = $confirmError;
    }
    
   
    if (empty($errors)) {
        $_SESSION['user'] = [
            'name' => $name,
            'email' => $email,
            'username' => $username
        ];
        
        if ($redirect === 'checkout') {
            header('Location: ../pages/checkout.php');
        } else {
            header('Location: ../pages/index.php');
        }
        exit();
    } else {
        $registerErrors = $errors;
        $registerData = [
            'name' => $name,
            'email' => $email,
            'username' => $username
        ];
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
                
             
                <div class="register-form-container" style="display: none;">
                    <h2>SIGN UP</h2>
                    
                    <?php if (!empty($registerErrors)): ?>
                        <?php foreach ($registerErrors as $error): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php?redirect=<?php echo $redirect; ?>">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($registerData['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($registerData['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username (min 6 characters)" value="<?php echo htmlspecialchars($registerData['username'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password (min 6 chars, 1 uppercase, 1 lowercase, 1 number)" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary">Sign Up</button>
                    </form>
                    <p class="form-switch">Already have an account? <a href="#" onclick="toggleForms()">Login</a></p>
                </div>
                
            </div>
        </div>
    </section>

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