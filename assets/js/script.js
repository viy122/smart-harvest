// scripts.js
// Subtle scroll animations using Intersection Observer API

document.addEventListener('DOMContentLoaded', () => {
  const features = document.querySelectorAll('.feature-card');
  const aboutItems = document.querySelectorAll('#about ul.list-group-item');

  const options = {
    threshold: 0.1,
  };

  const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in-up');
        observer.unobserve(entry.target);
      }
    });
  }, options);

  features.forEach(feature => {
    feature.classList.add('opacity-0');
    observer.observe(feature);
  });

  aboutItems.forEach(item => {
    item.classList.add('opacity-0');
    observer.observe(item);
  });
});

  window.addEventListener("scroll", function () {
    const navbar = document.querySelector(".custom-navbar");
    navbar.classList.toggle("scrolled", window.scrollY > 50);
  });
