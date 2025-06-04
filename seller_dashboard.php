<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!is_logged_in() || $_SESSION['role'] !== 'seller') {
    redirect('login.php');
}

$seller_id = $_SESSION['user_id'];

// Fetch seller's name from sellers table
$stmt = $pdo->prepare("SELECT name FROM sellers WHERE id = ?");
$stmt->execute([$seller_id]);
$seller = $stmt->fetch();
$seller_name = $seller ? $seller['name'] : 'Seller';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Seller Dashboard - Fruitopia</title>
    <link rel="stylesheet" href="assets/css/homepage.css" />
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700&family=Playfair+Display:ital@0;1&display=swap" rel="stylesheet" />
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo-container">
                <div class="logo">FRUITOPIA</div>
            </div>
            <div class="location">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                Location
            </div>
        </div>

        <nav class="nav-bar">
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"
                    viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Search for product" class="search-input" />
            </div>

            <div class="nav-actions">
  <!-- Profile Button with Dropdown -->
  <div class="profile-btn" style="position: relative;"> <!-- 🔧 Added style -->
    <div class="profile-icon" id="profileToggle">
      <!-- Profile SVG Icon -->
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" 
          stroke="currentColor" stroke-width="2" stroke-linecap="round" 
          stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      <span><?php echo htmlspecialchars($seller_name); ?></span>
      <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="18" height="18" 
          fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
          stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
    </div>

    <!-- 🔽 Dropdown Menu -->
    <div class="profile-dropdown" id="profileDropdown">
      <a href="logout.php" class="dropdown-item logout-btn">Logout</a>
    </div>
  </div>
</div>


                <!-- Order Request Button -->
                <div class="order-request-btn">
                    <a href="order_request.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                            stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M9 11H5a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h4m6-6h4a2 2 0 0 1 2 2v3c0 1.1-.9 2-2 2h-4m-6 0V9a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z"></path>
                        </svg>
                        Order Requests
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Your Dashboard</h1>
                <p>Manage your seasonal fruits and orders efficiently.</p>
            </div>
        </section>

        <section class="season-fruits">
            <h2>Add Seasonal Fruits</h2>
            <div class="season-cards">
                <div class="season-card clickable-card" data-url="add_fruit.php?season=summer">
                    <div class="season-img">
                        <img src="https://static.vecteezy.com/system/resources/previews/004/654/777/original/summer-season-typographic-poster-free-vector.jpg"
                             alt="Summer Season" />
                    </div>
                    <p>Summer</p>
                </div>

                <div class="season-card clickable-card" data-url="add_fruit.php?season=rainy">
                    <div class="season-img">
                        <img src="https://thumbs.dreamstime.com/z/vector-cartoon-illustration-rainy-day-beautiful-background-vector-cartoon-illustration-rainy-day-126766509.jpg"
                             alt="Rainy Season" />
                    </div>
                    <p>Rainy</p>
                </div>

                <div class="season-card clickable-card" data-url="add_fruit.php?season=winter">
                    <div class="season-img">
                        <img src="https://img.freepik.com/free-vector/winter-landscape-background_23-2149155991.jpg"
                             alt="Winter Season" />
                    </div>
                    <p>Winter</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Load our enhanced JavaScript file -->
    <script src="assets/js/homepage.js"></script>
</body>
</html>