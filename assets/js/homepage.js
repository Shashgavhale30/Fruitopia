document.addEventListener("DOMContentLoaded", () => {
  createFruitRain();
  addBouncyAnimations();
  addColorfulEffects();
  addSimpleInteractions();
  initProfileDropdown(); // Ensures dropdown logic is attached

  // ➡️ Add navigation on season card click
  document.querySelectorAll('.clickable-card').forEach(card => {
    card.addEventListener('click', () => {
      const url = card.getAttribute('data-url');
      if (url) {
        window.location.href = url;
      }
    });
  });
});

// 🍎 FRUIT RAIN ANIMATION
function createFruitRain() {
  const fruits = ['🍎', '🍊', '🍋', '🍌', '🍇', '🍓', '🥝', '🍑', '🥭', '🍍'];
  
  function dropFruit() {
    const fruit = document.createElement('div');
    fruit.textContent = fruits[Math.floor(Math.random() * fruits.length)];
    fruit.className = 'fruit-drop';
    fruit.style.cssText = `
      position: fixed;
      top: -50px;
      left: ${Math.random() * 100}%;
      font-size: ${20 + Math.random() * 20}px;
      z-index: 1000;
      pointer-events: none;
      animation: fall ${3 + Math.random() * 2}s linear forwards;
    `;
    
    document.body.appendChild(fruit);
    setTimeout(() => fruit.remove(), 5000);
  }
  
  // Drop fruit every 2-4 seconds
  setInterval(dropFruit, 2000 + Math.random() * 2000);
  
  // Initial burst of fruits
  for (let i = 0; i < 5; i++) {
    setTimeout(dropFruit, i * 300);
  }
}

// 🔽 PROFILE DROPDOWN FUNCTION
function initProfileDropdown() {
  const profileBtn = document.getElementById("profileToggle");
  const profileDropdown = document.getElementById("profileDropdown");

  if (profileBtn && profileDropdown) {
    profileBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle("show");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (
        !profileDropdown.contains(e.target) &&
        !profileBtn.contains(e.target)
      ) {
        profileDropdown.classList.remove("show");
      }
    });
  }
}

// ✨ Bounce & Wiggle Animations
function addBouncyAnimations() {
  const logo = document.querySelector(".logo");
  if (logo) {
    logo.addEventListener("mouseenter", () => {
      logo.style.animation = "bounce 0.6s ease";
    });
    logo.addEventListener("animationend", () => {
      logo.style.animation = "rainbow 3s ease-in-out infinite";
    });
  }

  const orderBtn = document.querySelector(".order-request-btn, .order-link");
  if (orderBtn) {
    orderBtn.addEventListener("click", () => {
      orderBtn.style.animation = "wiggle 0.5s ease";
      setTimeout(() => {
        orderBtn.style.animation = "";
      }, 500);
    });
  }
}

// 🌈 Color Effects
function addColorfulEffects() {
  const heroTitle = document.querySelector(".hero h1");
  if (heroTitle) {
    heroTitle.style.animation = "rainbow 3s ease-in-out infinite";
  }

  const searchInput = document.querySelector(".search-input");
  if (searchInput) {
    searchInput.addEventListener("focus", () => {
      searchInput.style.boxShadow = "0 0 20px #ff69b4, 0 0 30px #00ff88, 0 0 40px #ffd700";
      searchInput.style.animation = "glow 1s ease-in-out infinite alternate";
    });
    searchInput.addEventListener("blur", () => {
      searchInput.style.boxShadow = "";
      searchInput.style.animation = "";
    });
  }
}

// 🧠 Interactions
function addSimpleInteractions() {
  document.addEventListener("click", (e) => createSparkle(e.clientX, e.clientY));

  const cards = document.querySelectorAll(".season-card");
  cards.forEach((card) => {
    card.addEventListener("click", () => {
      card.style.animation = "spin360 1s ease";
      setTimeout(() => {
        card.style.animation = "float 7s ease-in-out infinite";
      }, 1000);
    });
  });

  setInterval(createFloatingHeart, 5000);
}

// ✨ Sparkle Effect
function createSparkle(x, y) {
  const sparkle = document.createElement("div");
  sparkle.className = "sparkle";
  sparkle.textContent = "✨";
  sparkle.style.cssText = `
    position: fixed;
    left: ${x}px;
    top: ${y}px;
    font-size: 20px;
    pointer-events: none;
    z-index: 9999;
    animation: sparkleAnim 1s ease-out forwards;
  `;
  document.body.appendChild(sparkle);
  setTimeout(() => sparkle.remove(), 1000);
}

// 💖 Floating Hearts
function createFloatingHeart() {
  const heart = document.createElement("div");
  heart.textContent = "💖";
  heart.style.cssText = `
    position: fixed;
    bottom: -50px;
    left: ${Math.random() * 100}%;
    font-size: 30px;
    z-index: 1000;
    pointer-events: none;
    animation: floatUp 4s ease-out forwards;
  `;
  document.body.appendChild(heart);
  setTimeout(() => heart.remove(), 4000);
}

// 🎨 Inject Extra Animations
const style = document.createElement("style");
style.textContent = `
  @keyframes fall {
    0% { transform: translateY(-50px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
  }

  @keyframes sparkleAnim {
    0% { transform: scale(0) rotate(0deg); opacity: 1; }
    50% { transform: scale(1.5) rotate(180deg); opacity: 1; }
    100% { transform: scale(0) rotate(360deg); opacity: 0; }
  }

  @keyframes floatUp {
    0% { transform: translateY(0) rotate(0deg); opacity: 1; }
    100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
  }

  @keyframes wiggle {
    0% { transform: rotate(0); }
    25% { transform: rotate(5deg); }
    50% { transform: rotate(-5deg); }
    75% { transform: rotate(3deg); }
    100% { transform: rotate(0); }
  }

  @keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
    40%, 43% { transform: translate3d(0,-30px,0); }
    70% { transform: translate3d(0,-15px,0); }
    90% { transform: translate3d(0,-4px,0); }
  }

  @keyframes rainbow {
    0% { color: #ff0000; }
    16% { color: #ff8000; }
    33% { color: #ffff00; }
    50% { color: #00ff00; }
    66% { color: #0080ff; }
    83% { color: #8000ff; }
    100% { color: #ff0000; }
  }

  @keyframes glow {
    from { box-shadow: 0 0 20px #ff69b4, 0 0 30px #00ff88, 0 0 40px #ffd700; }
    to { box-shadow: 0 0 30px #ff69b4, 0 0 40px #00ff88, 0 0 50px #ffd700; }
  }

  @keyframes spin360 {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
  }
`;
document.head.appendChild(style);

console.log("🎉 Dashboard animations loaded! Welcome to Fruitopia! 🍎");

function placeOrder(button) {
  const form = button.closest('form');
  let quantityInput = form.querySelector('.quantity-input');

  let qty = prompt("Enter quantity to order:", "1");
  if (qty === null) return; // User cancelled prompt

  qty = qty.trim();
  if (!qty || isNaN(qty) || parseInt(qty) <= 0) {
      alert("Please enter a valid quantity.");
      return;
  }

  quantityInput.value = qty;
  form.submit();
}
