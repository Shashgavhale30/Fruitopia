<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'seller') {
    redirect('login.php');
}

$seller_id = $_SESSION['user_id'];

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
<html>
<head>
    <meta charset="UTF-8" />
    <title>Order Requests - Seller Panel</title>
    <link rel="stylesheet" href="assets/css/homepage.css" />
</head>
<body>
<header>
    <a href="seller_dashboard.php">&larr; Back to Dashboard</a>
    <h1>Pending Order Requests</h1>
</header>

<main>
    <?php if (count($orders) === 0): ?>
        <p>No pending order requests.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Fruit</th>
                    <th>Quantity</th>
                    <th>Price (₹)</th>
                    <th>Season</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['fruit_name']); ?></td>
                        <td><?php echo (int)$order['quantity']; ?></td>
                        <td><?php echo number_format($order['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($order['season'])); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="action" value="accept">Accept</button>
                                <button type="submit" name="action" value="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
