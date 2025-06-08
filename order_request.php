<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'seller') {
    redirect('login.php');
}

$seller_id = $_SESSION['user_id'];

// Get seller name
$stmt = $pdo->prepare("SELECT name FROM sellers WHERE id = ?");
$stmt->execute([$seller_id]);
$seller = $stmt->fetch();
$seller_name = $seller ? $seller['name'] : 'Seller';

// Handle accept/reject POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'] === 'accept' ? 'accepted' : 'rejected';

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND seller_id = ?");
    $stmt->execute([$action, $order_id, $seller_id]);
    header("Location: order_request.php");
    exit;
}

// Fetch all pending orders for this seller
$stmt = $pdo->prepare("SELECT * FROM orders WHERE seller_id = ? AND status = 'pending' ORDER BY order_date DESC");
$stmt->execute([$seller_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order Requests - Seller Panel</title>
    <link rel="stylesheet" href="assets/css/order.css" />
</head>
<body>
<header>
    <div class="top-bar">
        <div class="logo-container">
            <a href="seller_dashboard.php" class="logo">FRUITOPIA</a>
        </div>

        <div class="profile-section" style="margin-top: 8px; position: relative; cursor: pointer; width: max-content;">
            <div class="profile-icon" onclick="toggleDropdown()" style="display: flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span><?php echo htmlspecialchars($seller_name); ?></span>
                <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
            <div class="profile-dropdown" id="profileDropdown" style="display: none; position: absolute; right: 0; background: white; border: 1px solid #ccc; padding: 8px; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 100; min-width: 120px;">
                <a href="logout.php" class="dropdown-item logout-btn" style="display: block; padding: 8px 12px; color: #333; text-decoration: none; border-radius: 4px; transition: background-color 0.2s;">Logout</a>
            </div>
        </div>
    </div>
</header>

<h1>Pending Order Requests</h1>
<main style="padding: 20px;">
    <?php if (count($orders) === 0): ?>
        <p>No pending order requests.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
                <tr>
                    <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: left;">Fruit</th>
                    <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: right;">Quantity</th>
                    <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: right;">Price (₹)</th>
                    <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: left;">Season</th>
                    <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: left;">Order Date</th>
                    <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['fruit_name']); ?></td>
                        <td style="padding: 10px; text-align: right;"><?php echo (int)$order['quantity']; ?></td>
                        <td style="padding: 10px; text-align: right;"><?php echo number_format($order['price'], 2); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars(ucfirst($order['season'])); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td style="padding: 10px; text-align: center;">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="action" value="accept" style="margin-right: 5px;">Accept</button>
                                <button type="submit" name="action" value="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
}
window.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    const profileBtn = document.querySelector('.profile-icon');
    if (!profileBtn.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

</body>
</html>
