<?php
session_start();
require_once 'includes/config.php'; // $pdo connection
require_once 'includes/auth.php';   // login_user() and redirect()

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        try {
            // Check sellers first
            $stmt = $pdo->prepare("SELECT id, name, password FROM sellers WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = 'seller';

            if (!$user) {
                // Check buyers if seller not found
                $stmt = $pdo->prepare("SELECT id, name, password FROM buyers WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $role = 'buyer';
            }

            if (!$user) {
                $error = "No user found with this email.";
            } else {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session and redirect
                    login_user($user['id'], $user['name'], $role);
                    $redirect_page = $role === 'seller' ? 'seller_dashboard.php' : 'buyer_dashboard.php';
                    redirect($redirect_page);
                } else {
                    $error = 'Incorrect password.';
                }
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
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
  <a href="index.php" class="navbar-brand">Fruitopia</a>
</nav>

<div class="auth-container">
  <div class="auth-card">
    <h2 style="text-align:center; margin-bottom: 1.5rem;">Login to Your Account</h2>

    <?php if ($error): ?>
      <p style="color:red; text-align:center;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <label for="email">Email address</label><br />
      <input type="email" id="email" name="email" placeholder="Enter your email" required
             value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" /><br /><br />

      <label for="password">Password</label><br />
      <input type="password" id="password" name="password" placeholder="Enter your password" required /><br /><br />

      <button type="submit" class="btn-auth">Login</button>
    </form>

    <p style="margin-top: 1rem; text-align: center;">
      Don't have an account? <a href="register.php">Register here</a>
    </p>
  </div>
</div>
</body>
</html>
