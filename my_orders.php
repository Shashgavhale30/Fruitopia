<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
    redirect('login.php');
}

$buyer_id = $_SESSION['user_id'];

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $order_id = (int)$_POST['cancel_order_id'];
    $stmt = $pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND buyer_id = ?");
    $stmt->execute([$order_id, $buyer_id]);
    header("Location: my_orders.php");
    exit;
}

// Get buyer name
$stmt = $pdo->prepare("SELECT name FROM buyers WHERE id = ?");
$stmt->execute([$buyer_id]);
$buyer = $stmt->fetch();
$buyer_name = $buyer ? $buyer['name'] : 'Buyer';

// Get orders for this buyer
$stmt = $pdo->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY order_date DESC");
$stmt->execute([$buyer_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Orders - Fruitopia</title>
<link rel="stylesheet" href="assets/css/order.css" />
</head>
<body>
<header>
    <div class="top-bar">
        <div class="logo-container">
            <a href="buyer_dashboard.php" class="logo">FRUITOPIA</a>
        </div>
        <nav class="nav-bar">
            <div class="profile-btn" style="position: relative; cursor: pointer;">
                <div class="profile-icon" onclick="toggleDropdown()" style="display: flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span><?php echo htmlspecialchars($buyer_name); ?></span>
                    <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
                <div class="profile-dropdown" id="profileDropdown" style="display: none; position: absolute; right: 0; background: white; border: 1px solid #ccc; padding: 8px; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); z-index: 100; min-width: 120px;">
                    <a href="logout.php" class="dropdown-item logout-btn" style="display: block; padding: 8px 12px; color: #333; text-decoration: none; border-radius: 4px; transition: background-color 0.2s;">Logout</a>
                </div>
            </div>
        </nav>
    </div>
</header>

<main style="padding: 20px;">
    <h1>My Orders</h1>

    <?php if (count($orders) === 0): ?>
        <p>You have not placed any orders yet.</p>
    <?php else: ?>
        <table class="orders-table" style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
            <thead>
    <tr>
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Fruit</th>
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Quantity</th>
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Price (₹)</th>
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Total Price (₹)</th> <!-- new column -->
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Season</th>
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Status</th>
        <th style="border-bottom: 2px solid #ddd; padding: 10px; text-align: center;">Ordered On</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td style="padding: 10px;"><?php echo htmlspecialchars($order['fruit_name']); ?></td>
        <td style="padding: 10px; text-align: center;"><?php echo (int)$order['quantity']; ?></td>
        <td style="padding: 10px; text-align: center;"><?php echo number_format($order['price'], 2); ?></td>
        <td style="padding: 10px; text-align: center;">
            <?php 
                $total_price = $order['quantity'] * $order['price'];
                echo number_format($total_price, 2);
            ?>
        </td>
        <td style="padding: 10px; text-align: center;"><?php echo htmlspecialchars(ucfirst($order['season'])); ?></td>
        <td style="padding: 10px; text-align: center;">
            <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
            <?php if (strtolower($order['status']) !== 'cancelled'): ?>
                <form method="POST" onsubmit="return confirm('Cancel this order?');" style="margin-top: 5px;">
                    <input type="hidden" name="cancel_order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" style="padding: 4px 10px; color: white; background-color: red; border: none; border-radius: 4px;">Cancel</button>
                </form>
            <?php endif; ?>
        </td>
        <td style="padding: 10px; text-align: center;"><?php echo htmlspecialchars($order['order_date']); ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
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
