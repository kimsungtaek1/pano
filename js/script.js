function scrollToConsultation(event) {
    if (event) event.preventDefault();
    const consultationSection = document.querySelector('.consultation');
    if (consultationSection) {
        consultationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Hero Slider
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length > 0) {
        let currentSlide = 0;

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        // Change slide every 4 seconds
        setInterval(nextSlide, 4000);
    }

    // Intro Tab Switching
    const tabButtons = document.querySelectorAll('.intro-tab-btn');
    const tabContents = document.querySelectorAll('.intro-tab-content');

    if (tabButtons.length > 0 && tabContents.length > 0) {
        tabButtons.forEach((button, index) => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                tabContents[index].classList.add('active');
            });
        });
    }
});
