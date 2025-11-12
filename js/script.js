// Scroll to Top Button
window.addEventListener('scroll', function() {
    const scrollTopBtn = document.getElementById('scrollTop');
    if (window.pageYOffset > 300) {
        scrollTopBtn.style.display = 'block';
    } else {
        scrollTopBtn.style.display = 'none';
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Mobile Menu Toggle (if needed in future)
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
});
