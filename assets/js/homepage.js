// Animate logo on page load
window.addEventListener("DOMContentLoaded", () => {
    const logo = document.querySelector(".logo");
    logo.style.opacity = 0;
    logo.style.transform = "translateY(-30px)";
    setTimeout(() => {
        logo.style.transition = "all 0.8s ease";
        logo.style.opacity = 1;
        logo.style.transform = "translateY(0)";
    }, 300);
});

// Animate hero section
window.addEventListener("load", () => {
    const heroContent = document.querySelector(".hero-content");
    heroContent.style.opacity = 0;
    heroContent.style.transform = "translateY(50px)";
    setTimeout(() => {
        heroContent.style.transition = "all 1s ease";
        heroContent.style.opacity = 1;
        heroContent.style.transform = "translateY(0)";
    }, 600);
});

// Animate season cards when in view
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("show");
        }
    });
}, {
    threshold: 0.2
});

document.querySelectorAll(".season-card").forEach(card => {
    card.classList.add("hide"); // Initial hidden state
    observer.observe(card);
});
