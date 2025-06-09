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

// Fetch buyer's details including address
$stmt = $pdo->prepare("SELECT name, address FROM buyers WHERE id = ?");
$stmt->execute([$buyer_id]);
$buyer = $stmt->fetch();
$buyer_name = $buyer ? $buyer['name'] : 'Buyer';
$buyer_address = $buyer ? $buyer['address'] : 'Address not set';

// Get pending order count for notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ('pending', 'processing')");
$stmt->execute([$buyer_id]);
$pending_count = $stmt->fetchColumn();

function fetchFruitsBySeason($pdo, $table) {
    $stmt = $pdo->prepare("SELECT id, name, quantity, unit, price, photo, seller_id FROM $table");
    $stmt->execute();
    return $stmt->fetchAll();
}

function searchFruits($pdo, $query) {
    $tables = ['summer_fruits', 'rainy_fruits', 'winter_fruits'];
    $results = [];
    
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT id, name, quantity, unit, price, photo, seller_id, 
                              CASE 
                                  WHEN ? = 'summer_fruits' THEN 'Summer'
                                  WHEN ? = 'rainy_fruits' THEN 'Rainy'
                                  WHEN ? = 'winter_fruits' THEN 'Winter'
                              END as season,
                              ? as table_name
                              FROM $table 
                              WHERE name LIKE ?");
        $stmt->execute([$table, $table, $table, $table, "%$query%"]);
        $results = array_merge($results, $stmt->fetchAll());
    }
    
    return $results;
}

$seasons = [
    'summer_fruits' => 'Summer',
    'rainy_fruits' => 'Rainy',
    'winter_fruits' => 'Winter',
];

// Check if search query exists
$search_results = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $search_results = searchFruits($pdo, $search_query);
}
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
        
        /* Search form styles - preserving original classes */
        .search-container {
            flex-grow: 1;
            position: relative;
        }
        
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
        }
        
        .search-input {
            width: 100%;
            padding: 8px 15px 8px 35px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            background-color: #f8f8f8;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            background-color: white;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        /* Search results specific styles */
        .search-results-title {
            margin-top: 30px;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .search-form {
            display: block;
            width: 100%;
        }
        
        .back-to-dashboard {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .back-to-dashboard:hover {
            background-color: #45a049;
        }
        
        .location {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
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
            <?php echo htmlspecialchars($buyer_address); ?>
        </div>
    </div>

    <nav class="nav-bar">
        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="search" placeholder="Search for fruits..." class="search-input" 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
            </form>
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
    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
        <section class="search-results">
            <h2 class="search-results-title">Search Results for "<?php echo htmlspecialchars($_GET['search']); ?>"</h2>
            
            <?php if (count($search_results) > 0): ?>
                <div class="fruits-grid">
                    <?php foreach ($search_results as $fruit): ?>
                        <div class="fruit-card">
                            <img src="<?php echo htmlspecialchars($fruit['photo']); ?>" alt="<?php echo htmlspecialchars($fruit['name']); ?>" />
                            <h4><?php echo htmlspecialchars($fruit['name']); ?></h4>
                            <p>Season: <?php echo htmlspecialchars($fruit['season']); ?></p>
                            <p>Quantity: <?php echo intval($fruit['quantity']) . ' ' . htmlspecialchars($fruit['unit']); ?></p>
                            <p>Price: ₹<?php echo htmlspecialchars($fruit['price']); ?></p>
                            <form method="POST" action="place_order.php" class="order-form">
                                <input type="hidden" name="fruit_name" value="<?php echo htmlspecialchars($fruit['name']); ?>">
                                <input type="hidden" name="price" value="<?php echo htmlspecialchars($fruit['price']); ?>">
                                <input type="hidden" name="season" value="<?php echo strtolower($fruit['season']); ?>">
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
            <?php else: ?>
                <p>No fruits found matching your search.</p>
            <?php endif; ?>
            
            <div>
                <a href="index.php" class="back-to-dashboard">Back to Dashboard</a>
            </div>
        </section>
    <?php else: ?>
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
    <?php endif; ?>
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
    
    // Toggle profile dropdown
    function toggleDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
    
    // Close dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon') && !event.target.closest('.profile-icon')) {
            const dropdowns = document.getElementsByClassName('profile-dropdown');
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.style.display === 'block') {
                    openDropdown.style.display = 'none';
                }
            }
        }
    }
</script>

<script src="assets/js/homepage.js"></script>
</body>
</html>