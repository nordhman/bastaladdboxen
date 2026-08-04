// BästaLaddboxen.se - JavaScript

// Mobilmeny
function toggleMenu() {
  const menu = document.getElementById('mobileMenu');
  menu.classList.toggle('open');
}

// Stäng meny vid klick utanför
document.addEventListener('click', function(e) {
  const menu = document.getElementById('mobileMenu');
  const hamburger = document.querySelector('.hamburger');
  if (menu && !menu.contains(e.target) && !hamburger.contains(e.target)) {
    menu.classList.remove('open');
  }
});

// Smooth reveal på scroll
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.produkt-kort, .kategori-kort, .usp-kort').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  observer.observe(el);
});

// Affiliate-länk tracking (lägg till din analytics här)
document.querySelectorAll('[rel*="sponsored"]').forEach(link => {
  link.addEventListener('click', function() {
    const productName = this.closest('.produkt-kort, .produkt-page')?.querySelector('.produkt-namn, h1')?.textContent || 'Unknown';
    console.log('Affiliate click:', productName, this.href);
    // Google Analytics exempel:
    // gtag('event', 'affiliate_click', { product: productName, url: this.href });
  });
});
