<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
    redirect('login.php');
}

$buyer_id = $_SESSION['user_id'];
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

// Order handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fruit_id'], $_POST['fruit_name'], $_POST['price'], $_POST['quantity'], $_POST['season'], $_POST['seller_id'])) {
    $fruit_id = (int)$_POST['fruit_id'];
    $fruit_name = $_POST['fruit_name'];
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $season = $_POST['season'];
    $seller_id = (int)$_POST['seller_id'];

    $insert = $pdo->prepare("INSERT INTO orders (buyer_id, seller_id, fruit_name, quantity, price, season) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$buyer_id, $seller_id, $fruit_name, $quantity, $price, $season]);

    echo "<script>alert('Order placed successfully!'); window.location='my_orders.php';</script>";
    exit;
}

// Fetch fruits
$stmt = $pdo->prepare("SELECT * FROM $table ORDER BY id DESC");
$stmt->execute();
$fruits = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo ucfirst($season); ?> Fruits - Fruitopia</title>
  <link rel="stylesheet" href="assets/css/homepage.css" />
</head>
<body>
<header>
  <a href="buyer_dashboard.php">← Back to Dashboard</a>
  <h1><?php echo ucfirst($season); ?> Fruits</h1>
</header>
<main>
  <?php if (count($fruits) === 0): ?>
    <p>No fruits available for this season.</p>
  <?php else: ?>
    <div class="fruit-list">
      <?php foreach ($fruits as $fruit): ?>
        <div class="fruit-item">
          <h3><?php echo htmlspecialchars($fruit['name']); ?></h3>
          <?php if (!empty($fruit['photo'])): ?>
            <img src="<?php echo htmlspecialchars($fruit['photo']); ?>" alt="<?php echo htmlspecialchars($fruit['name']); ?>" style="max-width:150px;" />
          <?php endif; ?>
          <p>Quantity: <?php echo htmlspecialchars($fruit['quantity']); ?></p>
          <p>Price: ₹<?php echo htmlspecialchars($fruit['price']); ?> per <?php echo htmlspecialchars($fruit['unit']); ?></p>

          <form method="POST" style="margin-top: 10px;">
            <input type="hidden" name="fruit_id" value="<?php echo $fruit['id']; ?>">
            <input type="hidden" name="fruit_name" value="<?php echo htmlspecialchars($fruit['name']); ?>">
            <input type="hidden" name="price" value="<?php echo $fruit['price']; ?>">
            <input type="hidden" name="season" value="<?php echo $season; ?>">
            <input type="hidden" name="seller_id" value="<?php echo $fruit['seller_id']; ?>">
            <input type="number" name="quantity" min="1" required placeholder="Quantity" style="width: 60px; margin-right: 5px;">
            <button type="submit">Order</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
</body>
</html>
