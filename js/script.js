function scrollToConsultation(event) {
    if (event) event.preventDefault();
    const consultationSection = document.querySelector('.consultation');
    if (consultationSection) {
        // 현재 페이지에 상담 섹션이 있으면 스크롤
        consultationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        // 상담 섹션이 없으면 index.php로 이동 후 스크롤
        window.location.href = '/index.php#consultation';
    }
}

// Hero Slider
document.addEventListener('DOMContentLoaded', function() {
    // 페이지 로드 시 해시가 #consultation이면 스크롤
    if (window.location.hash === '#consultation') {
        setTimeout(function() {
            const consultationSection = document.querySelector('.consultation');
            if (consultationSection) {
                consultationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }

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
