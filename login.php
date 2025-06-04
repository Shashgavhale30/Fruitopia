<?php
require_once 'config.php';
require_once 'auth.php';

if (is_logged_in()) {
    $redirect = ($_SESSION['role'] === 'seller') ? 'seller_dashboard.php' : 'buyer_dashboard.php';
    redirect($redirect);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                login_user($user['id'], $user['name'], $user['role']);
                $redirect = ($user['role'] === 'seller') ? 'seller_dashboard.php' : 'buyer_dashboard.php';
                redirect($redirect);
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "System error. Please try again later.";
        }
    } else {
        $error = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Fruitopia</title>
    <link rel="stylesheet" href="assets/css/login.css" />
</head>
<body>
    <nav class="portal-navbar">
        <a href="#" class="navbar-brand">
            <i class="fa fa-apple-whole"></i> Fruitopia
        </a>
    </nav>

    <div class="auth-container">
        <div class="auth-card">
            <div class="brand-wrapper">
                <div class="brand-logo">
                    <i class="fa fa-apple-whole" style="color:white; font-size: 2.5rem;"></i>
                </div>
            </div>

            <h2 style="text-align:center; margin-bottom: 1.5rem;">Login to Your Account</h2>

            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter your email"
                        required
                    />
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    />
                </div>

                <button type="submit" class="btn-auth">Login</button>
            </form>

            <p style="margin-top: 1rem; text-align: center;">
                Don't have an account?
                <a href="register.php" style="color: var(--accent-400); font-weight: 600;">Register here</a>
            </p>
        </div>
    </div>

    <div class="wave-bg"></div>
</body>
</html>
