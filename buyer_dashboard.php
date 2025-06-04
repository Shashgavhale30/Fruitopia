<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
    redirect('login.php');
}

$buyer_id = $_SESSION['user_id'];

// Fetch buyer's name from buyers table
$stmt = $pdo->prepare("SELECT name FROM buyers WHERE id = ?");
$stmt->execute([$buyer_id]);
$buyer = $stmt->fetch();
$buyer_name = $buyer ? $buyer['name'] : 'Buyer';

// For showing fruits by season, you can add links or sections to list fruits from those tables
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Buyer Dashboard - Fruitopia</title>
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
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Location
        </div>
    </div>
    <nav class="nav-bar">
        <div class="search-container">
            <svg
                class="search-icon"
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
            >
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input
                type="text"
                placeholder="Search for product"
                class="search-input"
            />
        </div>
        <div class="nav-actions">
            <!-- User profile dropdown -->
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

            <!-- My Orders button -->
            <div class="order-request-btn" style="margin-left: 20px;">
                <a href="my_orders.php" style="display: flex; align-items: center; gap: 6px; text-decoration: none; color: inherit; font-weight: 500;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
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
            <!-- Summer Season Card -->
            <div class="season-card" onclick="window.location.href='browse_fruits.php?season=summer'" style="cursor: pointer;">
                <div class="season-img">
                    <img
                        src="https://static.vecteezy.com/system/resources/previews/004/654/777/original/summer-season-typographic-poster-free-vector.jpg"
                        alt="Summer Season"
                    />
                </div>
                <p>Summer</p>
            </div>
            
            <!-- Rainy Season Card -->
            <div class="season-card" onclick="window.location.href='browse_fruits.php?season=rainy'" style="cursor: pointer;">
                <div class="season-img">
                    <img
                        src="https://thumbs.dreamstime.com/z/vector-cartoon-illustration-rainy-day-beautiful-background-vector-cartoon-illustration-rainy-day-126766509.jpg"
                        alt="Rainy Season"
                    />
                </div>
                <p>Rainy</p>
            </div>
            
            <!-- Winter Season Card -->
            <div class="season-card" onclick="window.location.href='browse_fruits.php?season=winter'" style="cursor: pointer;">
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
</main>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
        }
    }

    // Close dropdown if clicking outside
    window.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const profileBtn = document.querySelector('.profile-icon');
        if (!profileBtn.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Add hover effect for dropdown item
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownItem = document.querySelector('.dropdown-item');
        if (dropdownItem) {
            dropdownItem.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f5f5f5';
            });
            dropdownItem.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
            });
        }
    });
</script>
<script src="assets/js/homepage.js"></script>
</body>
</html>
