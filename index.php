<?php
// DB connection (update with your actual credentials)
$host = 'localhost';
$db = 'frutopia'; 
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "DB Connection failed: " . $e->getMessage();
    exit;
}

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
                <div class="logo">FRUITOPIA</div>
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
                <div class="btn login-btn">
                    <a href="login.php" class="btn-login">
                        <span>Login</span>
                    </a>
                </div>

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
                            src="https://static.vecteezy.com/system/resources/previews/004/654/777/original/summer-season-typographic-poster-free-vector.jpg"
                            alt="Summer Season"
                        />
                    </div>
                    <p>Summer</p>
                </div>
                <div class="season-card">
                    <div class="season-img">
                        <img
                            src="https://thumbs.dreamstime.com/z/vector-cartoon-illustration-rainy-day-beautiful-background-vector-cartoon-illustration-rainy-day-126766509.jpg"
                            alt="Rainy Season"
                        />
                    </div>
                    <p>Rainy</p>
                </div>
                <div class="season-card">
                    <div class="season-img">
                        <img
                            src="https://img.freepik.com/free-vector/winter-landscape-background_23-2149155991.jpg"
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
        <!-- User logged in, go to order page with fruit name as parameter -->
        <a href="order.php?fruit=<?php echo urlencode($fruit['name']); ?>" class="order-btn">Order</a>
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
