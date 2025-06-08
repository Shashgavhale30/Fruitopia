<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

function fetchFruitsBySeason($pdo, $seasonTable) {
    $allowedTables = ['summer_fruits', 'rainy_fruits', 'winter_fruits'];
    if (!in_array($seasonTable, $allowedTables)) {
        return [];
    }

    $sql = "SELECT name, photo, quantity, unit FROM $seasonTable ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

$seasons = [
    'summer_fruits' => 'Summer',
    'rainy_fruits' => 'Rainy',
    'winter_fruits' => 'Winter',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fruitopia - Organic Fruits Delivery</title>
    <link rel="stylesheet" href="assets/css/homepage.css" />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&display=swap"
    />
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo-container">
            <a href="index.php" class="logo">FRUITOPIA</a>
        </div>
            <div class="location">
                Location
            </div>
        </div>
        <nav class="nav-bar">
            <div class="search-container">
                <input
                    type="text"
                    placeholder="Search for product"
                    class="search-input"
                />
            </div>
            <div class="nav-actions">
                <!-- Login button -->
                    <a href="login.php" class="login-btn">
                        <span>Login</span>
                    </a>

                <!-- Shop button -->
                <div class="btn shop-btn">
                    Shop
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Fruitopia</h1>
                <p>We serve you organic fruits at your doorstep.</p>
            </div>
        </section>

        <section class="season-fruits">
            <h2>Seasonal Fruits</h2>
            <div class="season-cards">
                <div class="season-card">
                    <div class="season-img">
                        <img
                            src="uploads/summer.png"
                            alt="Summer Season"
                        />
                    </div>
                    <p>Summer</p>
                </div>
                <div class="season-card">
                    <div class="season-img">
                        <img
                            src="uploads/rainy.png"
                            alt="Rainy Season"
                        />
                    </div>
                    <p>Rainy</p>
                </div>
                <div class="season-card">
                    <div class="season-img">
                        <img
                            src="uploads/winter.png"
                            alt="Winter Season"
                        />
                    </div>
                    <p>Winter</p>
                </div>
            </div>
        </section>

        <!-- Added fruits section -->
        <section class="all-fruits">
            <h2>All Added Fruits</h2>

            <?php foreach ($seasons as $table => $seasonName): 
                $fruits = fetchFruitsBySeason($pdo, $table);
                if (count($fruits) === 0) {
                    echo "<p>No fruits found in {$seasonName} season.</p>";
                    continue; 
                }
            ?>
                <h3><?php echo htmlspecialchars($seasonName); ?> Fruits</h3>
                <div class="fruits-grid">
                    <?php foreach ($fruits as $fruit): ?>
                        <div class="fruit-card">
                            <img src="<?php echo htmlspecialchars($fruit['photo']); ?>" alt="<?php echo htmlspecialchars($fruit['name']); ?>" />
                            <h4><?php echo htmlspecialchars($fruit['name']); ?></h4>
                            <p>Quantity: <?php echo intval($fruit['quantity']) . ' ' . htmlspecialchars($fruit['unit']); ?></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <!-- User logged in, go to browse_fruits.php with season -->
                                <a href="browse_fruits.php?season=<?php echo strtolower($seasonName); ?>" class="order-btn">Order</a>
                            <?php else: ?>
                                <!-- Not logged in, redirect to login -->
                                <a href="login.php" class="order-btn">Order</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
        
    </main>
    <script src="assets/js/homepage.js"></script>
</body>
</html>
