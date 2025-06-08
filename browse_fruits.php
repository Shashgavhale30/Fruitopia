<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Ensure PDO fetch mode to associative arrays
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Redirect if not logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

$buyer_id = $_SESSION['user_id'] ?? null;
$season = $_GET['season'] ?? 'summer';
$allowed_seasons = ['summer', 'rainy', 'winter'];

if (!in_array($season, $allowed_seasons)) {
    die('Invalid season.');
}

$season_table_map = [
    'summer' => 'summer_fruits',
    'rainy' => 'rainy_fruits',
    'winter' => 'winter_fruits',
];

$table = $season_table_map[$season];

// Handle Order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fruit_id'], $_POST['fruit_name'], $_POST['price'], $_POST['quantity'], $_POST['season'], $_POST['seller_id'])) {

    if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
        echo "<script>alert('Please log in as a buyer to place an order.'); window.location='login.php';</script>";
        exit;
    }

    $buyer_id = $_SESSION['user_id'];
    $fruit_id = (int)$_POST['fruit_id'];
    $fruit_name = $_POST['fruit_name'];
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $season_post = $_POST['season'];
    $seller_id = (int)$_POST['seller_id'];

    // Check availability and unit
    $check = $pdo->prepare("SELECT quantity, unit FROM $table WHERE id = ?");
    $check->execute([$fruit_id]);
    $fruit = $check->fetch();

    if (!$fruit) {
        echo "<script>alert('Fruit not found.'); window.location='browse_fruits.php?season=$season';</script>";
        exit;
    }

    $available_quantity = (int)$fruit['quantity'];
    $unit = htmlspecialchars($fruit['unit']);

    if ($quantity > $available_quantity) {
        echo "<script>
            alert('Not enough quantity available. Only $available_quantity $unit left. Please adjust your order.');
            window.location='browse_fruits.php?season=$season';
        </script>";
        exit;
    }

    // Insert order record
$stmt = $pdo->prepare("INSERT INTO orders (buyer_id, fruit_name, price, quantity, season, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$buyer_id, $fruit_name, $price, $quantity, $season_post, $seller_id]);

    // Update fruit quantity
    $new_quantity = $available_quantity - $quantity;
    $update_stmt = $pdo->prepare("UPDATE $table SET quantity = ? WHERE id = ?");
    $update_stmt->execute([$new_quantity, $fruit_id]);

    echo "<script>
        alert('Order placed successfully for {$quantity} {$unit} of {$fruit_name}.');
        window.location='my_orders.php';
    </script>";
    exit;
}

// Fetch fruits for the current season
$stmt = $pdo->prepare("SELECT * FROM $table ORDER BY id DESC");
$stmt->execute();
$fruits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo ucfirst(htmlspecialchars($season)); ?> Fruits - Fruitopia</title>
  <link rel="stylesheet" href="assets/css/browse.css" />
</head>
<body>
<header class="browse-header">
  <div class="header-content">
    <form action="buyer_dashboard.php" method="get">
      <button type="submit" name="backToDashboard" id="backToDashboardBtn">Back to Dashboard</button>
    </form>
    <h1><?php echo ucfirst(htmlspecialchars($season)); ?> Fruits</h1>
  </div>
</header>
<main>
  <?php if (count($fruits) === 0): ?>
    <p>No fruits available for this season.</p>
  <?php else: ?>
    <div class="fruit-list">
  <?php foreach ($fruits as $fruit): ?>
    <div class="fruit-item">
      <h2><?php echo htmlspecialchars($fruit['name']); ?></h2>
      <?php if (!empty($fruit['photo'])): ?>
        <img src="<?php echo htmlspecialchars($fruit['photo']); ?>" alt="<?php echo htmlspecialchars($fruit['name']); ?>">
      <?php endif; ?>
      <p><strong>Available:</strong> <?php echo (int)$fruit['quantity'] . ' ' . htmlspecialchars($fruit['unit']); ?></p>
      <p><strong>Price:</strong> ₹<?php echo number_format((float)$fruit['price'], 2); ?> per <?php echo htmlspecialchars($fruit['unit']); ?></p>

      <?php if ((int)$fruit['quantity'] > 0): ?>
        <form method="post">
          <input type="hidden" name="fruit_id" value="<?php echo (int)$fruit['id']; ?>">
          <input type="hidden" name="fruit_name" value="<?php echo htmlspecialchars($fruit['name']); ?>">
          <input type="hidden" name="price" value="<?php echo (float)$fruit['price']; ?>">
          <input type="hidden" name="season" value="<?php echo htmlspecialchars($season); ?>">
          <input type="hidden" name="seller_id" value="<?php echo (int)$fruit['seller_id']; ?>">

          <input type="number" name="quantity" min="1" max="<?php echo (int)$fruit['quantity']; ?>" required>
          <button type="submit">Order</button>
        </form>
      <?php else: ?>
        <p style="color:red;">Out of stock</p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
  <?php endif; ?>
</main>
</body>
</html>
