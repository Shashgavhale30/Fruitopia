<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isset($_SESSION['login_user'])) {
    header("Location: login.php");
    exit();
}

if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
    redirect('login.php');
}

$buyer_id = $_SESSION['user_id'];

// Fetch buyer's name
$stmt = $pdo->prepare("SELECT name FROM buyers WHERE id = ?");
$stmt->execute([$buyer_id]);
$buyer = $stmt->fetch();
$buyer_name = $buyer ? $buyer['name'] : 'Buyer';

function fetchFruitsBySeason($pdo, $table) {
    $stmt = $pdo->prepare("SELECT id, name, quantity, unit, price, photo, seller_id FROM $table");
    $stmt->execute();
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
    <title>Buyer Dashboard - Fruitopia</title>
    <link rel="stylesheet" href="assets/css/homepage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="top-bar">
        <div class="logo-container">
            <div class="logo"><a href="index.php">FRUITOPIA</a></div>
        </div>
        <div class="location">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Location
        </div>
    </div>

    <nav class="nav-bar">
        <div class="search-container">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" placeholder="Search for product" class="search-input" />
        </div>

        <div class="nav-actions">
            <!-- Profile Button with Dropdown -->
            <div class="profile-btn">
                <div class="btn profile-icon" onclick="toggleDropdown()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span><?php echo htmlspecialchars($buyer_name); ?></span>
                    <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="logout.php" class="dropdown-item logout-btn">Logout</a>
                </div>
            </div>

            <!-- Order Request Button -->
            <div class="btn order-request-btn">
                <a href="my_orders.php" class="order-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M9 11H5a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h4m6-6h4a2 2 0 0 1 2 2v3c0 1.1-.9 2-2 2h-4m-6 0V9a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z"></path>
                    </svg>
                    My Orders
                </a>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Your Dashboard</h1>
            <p>Browse fruits by season and check your orders.</p>
        </div>
    </section>

    <section class="season-fruits">
        <h2>Browse Fruits by Season</h2>
        <div class="season-cards">
            <div class="season-card clickable" onclick="window.location.href='browse_fruits.php?season=summer'">
                <div class="season-img">
                    <img src="https://static.vecteezy.com/system/resources/previews/004/654/777/original/summer-season-typographic-poster-free-vector.jpg"
                         alt="Summer Season" />
                </div>
                <p>Summer</p>
            </div>
            <div class="season-card clickable" onclick="window.location.href='browse_fruits.php?season=rainy'">
                <div class="season-img">
                    <img src="https://thumbs.dreamstime.com/z/vector-cartoon-illustration-rainy-day-beautiful-background-vector-cartoon-illustration-rainy-day-126766509.jpg"
                         alt="Rainy Season" />
                </div>
                <p>Rainy</p>
            </div>
            <div class="season-card clickable" onclick="window.location.href='browse_fruits.php?season=winter'">
                <div class="season-img">
                    <img src="https://img.freepik.com/free-vector/winter-landscape-background_23-2149155991.jpg"
                         alt="Winter Season" />
                </div>
                <p>Winter</p>
            </div>
        </div>
    </section>

    <!-- New Section: All Added Fruits -->
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
                        <form method="POST" action="place_order.php" class="order-form">
    <input type="hidden" name="fruit_name" value="<?php echo htmlspecialchars($fruit['name']); ?>">
    <input type="hidden" name="price" value="<?php echo htmlspecialchars($fruit['price']); ?>">
    <input type="hidden" name="season" value="<?php echo strtolower($seasonName); ?>">

    <input type="hidden" name="seller_id" value="<?php echo htmlspecialchars($fruit['seller_id']); ?>">
    <input type="hidden" name="quantity" class="quantity-input">
    <button class="order-btn" type="button" onclick="placeOrder(this)">Order</button>
</form>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
</main>
<script src="assets/js/homepage.js"></script>
</body>
</html>
