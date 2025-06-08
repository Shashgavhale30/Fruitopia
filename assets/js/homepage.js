(() => {
  'use strict';

  document.addEventListener("DOMContentLoaded", () => {
    console.log("🍎 FRUITOPIA Loading...");

    // Initialize all features
    initProfileDropdown();
    initSafeNavigation();
    initSeasonCards();
    initSearchFeatures();
    initSmoothScrolling();
    initFruitCardAnimations();
    initLogoInteractions();

    console.log("🍊 FRUITOPIA Ready! Welcome to the freshest fruit experience!");
  });

function initSafeNavigation() {
  document.body.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');
    if (!link) return;

    const href = link.getAttribute('href');
    if (href && href.includes('buyer_dashboard')) {
      e.preventDefault();
      console.log('Redirecting to dashboard:', href);
      window.location.href = href; // Force full page reload to dashboard
    }
  });
}

  function initProfileDropdown() {
    const profileBtn = document.getElementById("profileToggle") || document.querySelector('.profile-btn');
    const profileDropdown = document.getElementById("profileDropdown");

    if (profileBtn && profileDropdown) {
      const style = document.createElement('style');
      style.textContent = `
        .profile-dropdown.show,
        #profileDropdown.show {
          display: block !important;
          opacity: 1 !important;
          transform: translateY(0) !important;
          animation: dropdownSlide 0.3s ease-out;
        }
        
        @keyframes dropdownSlide {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      `;
      document.head.appendChild(style);

      profileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle("show");
      });

      document.addEventListener("click", (e) => {
        if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
          profileDropdown.classList.remove("show");
        }
      });

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          profileDropdown.classList.remove("show");
        }
      });
    }
  }

  function initSeasonCards() {
    document.querySelectorAll(".season-card").forEach(card => {
      if (!card.hasAttribute('data-url') && card.href) {
        card.setAttribute('data-url', card.href);
      }

      card.addEventListener("click", (e) => {
        const url = card.getAttribute("data-url") || card.getAttribute("href");
        if (url && !e.target.closest('a')) {
          card.classList.add('clicked-loading');
          setTimeout(() => {
            window.location.href = url;
          }, 150);
        }
      });

      card.setAttribute("tabindex", "0");
      card.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          const url = card.getAttribute("data-url") || card.getAttribute("href");
          if (url) window.location.href = url;
        }
      });
    });

    // Add CSS class for loading feedback
    const style = document.createElement('style');
    style.textContent = `
      .season-card.clicked-loading {
        transform: scale(0.95);
        transition: transform 0.15s ease;
      }
    `;
    document.head.appendChild(style);
  }

  function initSearchFeatures() {
    const searchInput = document.querySelector(".search-input");

    if (!searchInput) return;

    const placeholders = [
      "Search for fresh apples...",
      "Find juicy oranges...",
      "Discover sweet berries...",
      "Look for tropical fruits..."
    ];

    let currentPlaceholder = 0;
    const placeholderInterval = setInterval(() => {
      searchInput.placeholder = placeholders[currentPlaceholder];
      currentPlaceholder = (currentPlaceholder + 1) % placeholders.length;
    }, 3000);

    // Optional: clear interval on page unload to avoid memory leaks
    window.addEventListener('beforeunload', () => {
      clearInterval(placeholderInterval);
    });

    // Debounce function to improve input event performance
    function debounce(func, wait) {
      let timeout;
      return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    }

    const handleInput = debounce((e) => {
      const query = e.target.value.toLowerCase();
      if (query.length >= 2) {
        highlightSearchResults(query);
      } else {
        clearSearchHighlights();
      }
    }, 300);

    searchInput.addEventListener("input", handleInput);

    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        performSearch(e.target.value);
      }
    });
  }

  function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener("click", (e) => {
        const targetSelector = anchor.getAttribute("href");
        const target = document.querySelector(targetSelector);
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      });
    });
  }

  function initFruitCardAnimations() {
    const fruitCards = document.querySelectorAll(".fruit-card");

    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      fruitCards.forEach(card => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
        observer.observe(card);

        const orderBtn = card.querySelector(".order-btn");
        if (orderBtn) {
          orderBtn.addEventListener("click", (e) => {
            if (!orderBtn.classList.contains("loading")) {
              orderBtn.classList.add("loading");
              orderBtn.textContent = "Adding...";

              setTimeout(() => {
                orderBtn.classList.remove("loading");
                orderBtn.textContent = "Order Now";
              }, 2000);
            }
          });
        }
      });
    }
  }

  function initLogoInteractions() {
    const logo = document.querySelector(".logo");

    if (logo) {
      logo.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
      });
    }
  }

  // Placeholder functions (define these properly in your actual code)
  function highlightSearchResults(query) {
    // Implement search highlighting logic here
    console.log(`Highlighting results for: ${query}`);
  }

  function clearSearchHighlights() {
    // Implement logic to clear search highlights here
    console.log('Clearing search highlights');
  }

  function performSearch(query) {
    // Implement search execution logic here
    console.log(`Performing search for: ${query}`);
  }

})();
