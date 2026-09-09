<?php
session_start();
require_once '../database/config.php';
require_once '../pages/validation.php';

// If already logged in,redirect based on user type
if (isset($_SESSION['user'])) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';
    
    // Check if user is admin
    if (isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == 1) {
        header('Location: ../admin/index.php');
        exit();
    }
    
    if ($redirect === 'checkout') {
        header('Location: ../pages/checkout.php');
    } else {
        header('Location: ../pages/index.php');
    }
    exit();
}

$logoutMessage = '';
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logoutMessage = 'You have been logged out successfully.';
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check user in database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'username' => $user['username'],
            'is_admin' => $user['is_admin']  // ← Importante!
        ];
        
        // Redirect based on user type
        if ($user['is_admin'] == 1) {
            header('Location: ../admin/index.php');
        } else {
            if ($redirect === 'checkout') {
                header('Location: ../pages/checkout.php');
            } else {
                header('Location: ../pages/index.php');
            }
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
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email already exists!';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            //Check if this is the first user (become admin) =====
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            if ($userCount == 0) {
                $isAdmin = 1; // First user = Admin
            } else {
                $isAdmin = 0; // All other users
            }
        
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, username, password, is_admin) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $username, $hashedPassword, $isAdmin]);
            
            $_SESSION['user'] = [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'is_admin' => $isAdmin
            ];
            
            if ($isAdmin == 1) {
                header('Location: ../admin/index.php');
            } else {
                if ($redirect === 'checkout') {
                    header('Location: ../pages/checkout.php');
                } else {
                    header('Location: ../pages/index.php');
                }
            }
            exit();
        }
    }
    
    if (!empty($errors)) {
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
    <style>
        .logout-success-message {
            background: rgba(40,167,69,0.15);
            color: #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(40,167,69,0.2);
        }
        .logout-success-message i {
            margin-right: 10px;
        }
        .admin-notice {
            background: rgba(255,193,7,0.1);
            color: #ffc107;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
            border: 1px solid rgba(255,193,7,0.1);
        }
        .admin-notice i {
            margin-right: 8px;
        }
    </style>
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
                
                <?php if ($logoutMessage): ?>
                    <div class="logout-success-message">
                        <i class="fas fa-check-circle"></i> <?php echo $logoutMessage; ?>
                    </div>
                <?php endif; ?>
                
                <?php 
                $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                if ($userCount == 0): 
                ?>
                    <div class="admin-notice">
                        <i class="fas fa-info-circle"></i> 
                        No users yet. The first person to sign up will become the <strong>Admin</strong>!
                    </div>
                <?php endif; ?>
                
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