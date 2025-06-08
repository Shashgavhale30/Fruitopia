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

// Fetch buyer's name
$stmt = $pdo->prepare("SELECT name FROM buyers WHERE id = ?");
$stmt->execute([$buyer_id]);
$buyer = $stmt->fetch();
$buyer_name = $buyer ? $buyer['name'] : 'Buyer';

// Get pending order count for notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ('pending', 'processing')");
$stmt->execute([$buyer_id]);
$pending_count = $stmt->fetchColumn();

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
    <style>
        /* Notification badge styles */
        .notification-badge {
            background-color: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: relative;
            top: -8px;
            margin-left: 4px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .alert-notice {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 15px;
            display: inline-block;
        }
    </style>
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
            <div class="profile-btn" style="position: relative;">
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

            <!-- Order Request Button with Notification Badge -->
            <div class="btn order-request-btn">
                <a href="my_orders.php" class="order-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M9 11H5a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h4m6-6h4a2 2 0 0 1 2 2v3c0 1.1-.9 2-2 2h-4m-6 0V9a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z"></path>
                    </svg>
                    My Orders
                    <?php if ($pending_count > 0): ?>
                        <span class="notification-badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
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
            <?php if ($pending_count > 0): ?>
                <div class="alert-notice">
                    You have <?php echo $pending_count; ?> order<?php echo $pending_count > 1 ? 's' : ''; ?> pending confirmation.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="season-fruits">
        <h2>Browse Fruits by Season</h2>
        <div class="season-cards">
            <a href="browse_fruits.php?season=summer" class="season-card">
                <div class="season-img">
                    <img src="uploads/summer.png" alt="Summer Season" />
                </div>
                <p>Summer</p>
            </a>
            <a href="browse_fruits.php?season=rainy" class="season-card">
                <div class="season-img">
                    <img src="uploads/rainy.png" alt="Rainy Season" />
                </div>
                <p>Rainy</p>
            </a>
            <a href="browse_fruits.php?season=winter" class="season-card">
                <div class="season-img">
                    <img src="uploads/winter.png" alt="Winter Season" />
                </div>
                <p>Winter</p>
            </a>
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
                        <p>Price: ₹<?php echo htmlspecialchars($fruit['price']); ?></p>
                        <form method="POST" action="place_order.php" class="order-form">
                            <input type="hidden" name="fruit_name" value="<?php echo htmlspecialchars($fruit['name']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($fruit['price']); ?>">
                            <input type="hidden" name="season" value="<?php echo strtolower($seasonName); ?>">
                            <input type="hidden" name="seller_id" value="<?php echo htmlspecialchars($fruit['seller_id']); ?>">
                            <div class="quantity-selector">
                                <label for="quantity">Qty:</label>
                                <input type="number" name="quantity" min="1" max="<?php echo intval($fruit['quantity']); ?>" value="1" class="quantity-input">
                            </div>
                            <button class="order-btn" type="submit">Order Now</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
</main>

<script>
    // Function to update notification badge
    function updateOrderNotifications() {
        fetch('includes/get_buyer_orders.php')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (data.count > 0) {
                    if (!badge) {
                        // Create badge if it doesn't exist
                        const newBadge = document.createElement('span');
                        newBadge.className = 'notification-badge';
                        newBadge.textContent = data.count;
                        document.querySelector('.order-request-btn .order-link').appendChild(newBadge);
                        
                        // Add alert notice if it doesn't exist
                        if (!document.querySelector('.alert-notice')) {
                            const alert = document.createElement('div');
                            alert.className = 'alert-notice';
                            alert.textContent = `You have ${data.count} order${data.count > 1 ? 's' : ''} pending confirmation.`;
                            document.querySelector('.hero-content').appendChild(alert);
                        }
                    } else {
                        // Update existing badge
                        badge.textContent = data.count;
                        const alert = document.querySelector('.alert-notice');
                        if (alert) {
                            alert.textContent = `You have ${data.count} order${data.count > 1 ? 's' : ''} pending confirmation.`;
                        }
                    }
                } else if (badge) {
                    // Remove badge if no pending orders
                    badge.remove();
                    const alert = document.querySelector('.alert-notice');
                    if (alert) {
                        alert.remove();
                    }
                }
            });
    }

    // Check for updates every 30 seconds
    setInterval(updateOrderNotifications, 30000);
    
    // Initial check when page loads
    document.addEventListener('DOMContentLoaded', updateOrderNotifications);
</script>

<script src="assets/js/homepage.js"></script>
</body>
</html>