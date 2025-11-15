<?php
include 'includes/header.php';
include 'includes/db.php';
?>

<main>
    <!-- 메인 비주얼 -->
    <section class="hero">
        <div class="hero-slide active">
            <img src="/images/1.png" alt="배경 1">
        </div>
        <div class="hero-slide">
            <img src="/images/2.png" alt="배경 2">
        </div>
        <div class="hero-slide">
            <img src="/images/3.png" alt="배경 3">
        </div>
        <div class="hero-overlay">
            <div class="hero-content">
                <img src="/images/slide_logo.png" alt="PANO" class="hero-logo">
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="success-stories">
        <div class="container">
            <h2>SUCCESS STORIES</h2>
            <div class="slider-container">
                <div class="slider" id="successSlider">
                    <div class="slide">
                        <h3>사례 1</h3>
                        <p>성공적인 법률 서비스 제공 사례입니다.</p>
                        <span class="year">2024.01</span>
                    </div>
                    <div class="slide">
                        <h3>사례 2</h3>
                        <p>의뢰인의 권리를 보호한 사례입니다.</p>
                        <span class="year">2024.02</span>
                    </div>
                    <div class="slide">
                        <h3>사례 3</h3>
                        <p>복잡한 법률 문제를 해결한 사례입니다.</p>
                        <span class="year">2024.03</span>
                    </div>
                </div>
                <button class="slider-btn prev" onclick="moveSlide('success', -1)">‹</button>
                <button class="slider-btn next" onclick="moveSlide('success', 1)">›</button>
            </div>
        </div>
    </section>

    <!-- Press Coverage Section -->
    <section class="press-coverage">
        <div class="container">
            <h2>PRESS COVERAGE</h2>
            <div class="slider-container">
                <div class="slider" id="pressSlider">
                    <div class="slide">
                        <h3>언론 보도 1</h3>
                        <p>법무법인 파노의 주요 활동이 언론에 보도되었습니다.</p>
                        <span class="year">2024.01</span>
                    </div>
                    <div class="slide">
                        <h3>언론 보도 2</h3>
                        <p>전문성을 인정받은 법률 서비스 제공 사례입니다.</p>
                        <span class="year">2024.02</span>
                    </div>
                    <div class="slide">
                        <h3>언론 보도 3</h3>
                        <p>사회 공헌 활동이 주목받았습니다.</p>
                        <span class="year">2024.03</span>
                    </div>
                </div>
                <button class="slider-btn prev" onclick="moveSlide('press', -1)">‹</button>
                <button class="slider-btn next" onclick="moveSlide('press', 1)">›</button>
            </div>
        </div>
    </section>

    <!-- Practice Areas Section -->
    <section class="services">
        <div class="container">
            <div class="services-intro">
                <h2>PRACTICE AREAS</h2>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">⚖️</div>
                    <h3>민사</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">🏛️</div>
                    <h3>형사</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">🤝</div>
                    <h3>조정중재</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">🌳</div>
                    <h3>환경법규</h3>
                </div>
                <div class="service-card">
                    <div class="service-icon">📋</div>
                    <h3>행정</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Consultation Request Section -->
    <section class="consultation">
        <div class="container">
            <div class="consultation-header">
                <p class="label">문의하기</p>
                <h2>CONSULTATION REQUEST</h2>
            </div>

            <form id="consultationForm" class="consultation-form" method="POST" action="/api/submit_consultation.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">이름 <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">연락처 <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" required placeholder="010-1234-5678">
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">상담내용 <span class="required">*</span></label>
                    <textarea id="content" name="content" rows="8" required placeholder="상담하실 내용을 자세히 입력해주세요."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">무료상담 신청</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Fixed Bottom Consultation Bar -->
    <div class="fixed-consultation-bar">
        <form id="fixedConsultationForm" class="fixed-consultation-form" method="POST" action="/api/submit_consultation.php">
            <input type="text" name="name" placeholder="이름" required>
            <input type="tel" name="phone" placeholder="연락처 (010-1234-5678)" required>
            <textarea name="content" placeholder="상담내용을 간단히 입력해주세요" required></textarea>
            <button type="submit" class="btn-submit-bar">상담신청</button>
        </form>
    </div>

    <!-- Floating Action Buttons -->
    <div class="floating-buttons">
        <a href="https://pf.kakao.com/_Exaaxib/chat" target="_blank" class="floating-btn kakao" title="카카오톡 상담">
            <img src="/images/kakao.png" alt="카카오톡">
        </a>
        <a href="tel:02-1551-8385" class="floating-btn phone" title="전화 상담">
            <img src="/images/phone.png" alt="전화">
        </a>
        <a href="#" class="floating-btn consultation" title="상담 신청" onclick="scrollToConsultation(event)">
            📝
        </a>
        <a href="#" class="floating-btn scroll-top" title="맨 위로" onclick="scrollToTop(event)">
            ↑
        </a>
    </div>
</main>

<script>
// Slider functionality
let currentSlide = {
    success: 0,
    press: 0
};

function moveSlide(type, direction) {
    const slider = document.getElementById(type + 'Slider');
    const slides = slider.children;
    const totalSlides = slides.length;

    currentSlide[type] += direction;

    if (currentSlide[type] < 0) {
        currentSlide[type] = totalSlides - 1;
    } else if (currentSlide[type] >= totalSlides) {
        currentSlide[type] = 0;
    }

    slider.style.transform = `translateX(-${currentSlide[type] * 100}%)`;
}

// Auto slide
setInterval(() => {
    moveSlide('success', 1);
}, 5000);

setInterval(() => {
    moveSlide('press', 1);
}, 5500);

// Consultation form submit
document.getElementById('consultationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit');

    submitBtn.disabled = true;
    submitBtn.textContent = '전송 중...';

    fetch('/api/submit_consultation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('상담신청이 완료되었습니다.\n빠른 시일 내에 연락드리겠습니다.');
            this.reset();
        } else {
            alert('오류가 발생했습니다: ' + (data.message || '다시 시도해주세요.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('오류가 발생했습니다. 다시 시도해주세요.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = '무료상담 신청';
    });
});

// Scroll functions
function scrollToConsultation(event) {
    event.preventDefault();
    const consultationSection = document.querySelector('.consultation');
    if (consultationSection) {
        consultationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function scrollToTop(event) {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Fixed consultation form
document.getElementById('fixedConsultationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit-bar');

    submitBtn.disabled = true;
    submitBtn.textContent = '전송 중...';

    fetch('/api/submit_consultation.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('상담신청이 완료되었습니다.\n빠른 시일 내에 연락드리겠습니다.');
            this.reset();
        } else {
            alert('오류가 발생했습니다: ' + (data.message || '다시 시도해주세요.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('오류가 발생했습니다. 다시 시도해주세요.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = '상담신청';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
