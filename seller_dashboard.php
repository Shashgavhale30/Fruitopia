<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruitopia - Organic Fruits Delivery</title>
    <link rel="stylesheet" href="assets/css/homepage.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@0;1&display=swap">
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="logo-container">
                <div class="logo">FRUITOPIA</div>
            </div>
            <div class="location">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                Location
            </div>
        </div>
        <nav class="nav-bar">
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Search for product" class="search-input">
            </div>
            <div class="nav-actions">
                <div class="profile-btn">
                    <div class="profile-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Profile</span>
                        <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                
                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown">
                        <a href="#" class="dropdown-item">
                            <i class="icon orders"></i> Your Orders
                        </a>
                        <a href="http://localhost/php-practice/login.html" class="dropdown-item">
                          <i class="icon edit-profile"></i> Register
                        </a>                        
                        <a href="#" class="dropdown-item logout-btn">
                            <i class="icon logout"></i> Logout
                        </a>
                    </div>
                </div>
                <div class="shop-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
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
                        <img src="https://static.vecteezy.com/system/resources/previews/004/654/777/original/summer-season-typographic-poster-free-vector.jpg" alt="Summer Season">
                    </div>
                    <p>Summer</p>
                </div>
                <div class="season-card">
                    <div class="season-img">
                        <img src="https://thumbs.dreamstime.com/z/vector-cartoon-illustration-rainy-day-beautiful-background-vector-cartoon-illustration-rainy-day-126766509.jpg" alt="Rainy Season">
                    </div>
                    <p>Rainy</p>
                </div>
                <div class="season-card">
                    <div class="season-img">
                        <img src="https://img.freepik.com/free-vector/winter-landscape-background_23-2149155991.jpg" alt="Winter Season">
                    </div>
                    <p>Winter</p>
                </div>
            </div>
        </section>
    </main>
    <script src="assets/js/homepage.js"></script>
</body>
</html>