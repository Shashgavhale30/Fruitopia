<?php
session_start();

// Database connection settings
$host = 'localhost';
$dbname = 'frutopia';  // corrected dbname
$dbuser = 'root';
$dbpass = '';  // update if needed

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');

    // Validation
    if (!$name) $errors[] = "Name is required.";
    if (!$email) $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (!$password) $errors[] = "Password is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (!$role || !in_array($role, ['seller', 'buyer'])) $errors[] = "Please select a valid role.";
    if (!$address) $errors[] = "Address is required.";
    if ($role === 'seller' && !$bank_account) {
        $errors[] = "Bank Account is required for sellers.";
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if email exists in either sellers or buyers
            $stmt = $pdo->prepare("SELECT id FROM sellers WHERE email = ? UNION SELECT id FROM buyers WHERE email = ?");
            $stmt->execute([$email, $email]);
            if ($stmt->fetch()) {
                $errors[] = "Email is already registered.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                if ($role === 'seller') {
                    $stmt = $pdo->prepare("INSERT INTO sellers (name, email, password, address, bank_account) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $password_hash, $address, $bank_account]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO buyers (name, email, password, address) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $password_hash, $address]);
                }

                // Redirect to login page after successful registration
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register | Fruitopia</title>
    <link rel="stylesheet" href="assets/css/login.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
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

        <h2 style="text-align:center; margin-bottom: 1.5rem;">Create a New Account</h2>

        <?php if ($errors): ?>
            <div style="color: #b00020; margin-bottom: 1rem;">
                <ul style="padding-left: 1.2rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?=htmlspecialchars($error)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" autocomplete="off" novalidate>
            <div class="mb-3">
                <label for="role" class="form-label">Registering as:</label>
                <select name="role" id="role" class="form-control" required onchange="toggleSellerFields()">
                    <option value="">-- Select Role --</option>
                    <option value="buyer" <?= (($_POST['role'] ?? '') === 'buyer') ? 'selected' : '' ?>>Buyer</option>
                    <option value="seller" <?= (($_POST['role'] ?? '') === 'seller') ? 'selected' : '' ?>>Seller</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="Enter your full name"
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea
                    name="address"
                    id="address"
                    class="form-control"
                    placeholder="Enter your address"
                    required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>

            <div id="seller-extra-fields" style="display: none;">
                <div class="mb-3">
                    <label for="bank_account" class="form-label">Bank Account Number</label>
                    <input
                        type="text"
                        name="bank_account"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['bank_account'] ?? '') ?>"
                    />
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Create a password"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Confirm your password"
                    required
                />
            </div>

            <button type="submit" class="btn-auth">Register</button>
        </form>

        <p style="margin-top: 1rem; text-align: center;">
            Already have an account?
            <a href="login.php" style="color: var(--accent-400); font-weight: 600;">Login here</a>
        </p>
    </div>
</div>

<div class="wave-bg"></div>

<script>
function toggleSellerFields() {
    const role = document.getElementById('role').value;
    const sellerFields = document.getElementById('seller-extra-fields');
    sellerFields.style.display = (role === 'seller') ? 'block' : 'none';
}
// Initialize display on page load
document.addEventListener('DOMContentLoaded', () => toggleSellerFields());
</script>
</body>
</html>
