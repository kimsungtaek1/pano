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
            <div class="container">
                <div class="hero-content">
                    <img src="/images/slide_logo.png" alt="PANO" class="hero-logo">
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="success-stories">
        <div class="container">
            <div class="success-layout">
                <div class="success-intro">
                    <p class="section-label-text">성공사례</p>
                    <h2>SUCCESS STORIES</h2>
                    <p class="section-desc">파노의 성공사례를 만나보세요.</p>
                    <div class="slider-controls">
                        <button class="slider-arrow prev" onclick="moveSuccessSlide(-1)">
                            <img src="/images/left.svg" alt="이전">
                        </button>
                        <button class="slider-arrow next" onclick="moveSuccessSlide(1)">
                            <img src="/images/right.svg" alt="다음">
                        </button>
                    </div>
                    <div class="btn-more-wrapper">
                        <a href="/news.php" class="btn-more-link">
                            <span>더 알아보기</span>
                            <button class="btn-circle-arrow">
                                <img src="/images/right_w.svg" alt="더 알아보기">
                            </button>
                        </a>
                    </div>
                </div>
                <div class="success-content">
                    <div class="success-cards">
                        <div class="success-card">
                            <div class="card-header">
                                <span class="card-tag">구속영장 기각</span>
                                <h3>현행범체포<br>구속영장청구 기각</h3>
                            </div>
                            <div class="card-body">
                                <p>피고인이 2023년 5월 조치 불출시기 방법 조직범죄 관한처분을 범죄 권리위반 신청원 고발원이 만약어 제재우 송수청원 권립라윈 것....</p>
                            </div>
                        </div>
                        <div class="success-card">
                            <div class="card-header">
                                <span class="card-tag">무죄 판결</span>
                                <h3>횡령 혐의<br>1심 무죄 판결</h3>
                            </div>
                            <div class="card-body">
                                <p>검찰이 제기한 횡령 혐의에 대해 철저한 증거 분석과 법리 검토를 통해 1심에서 무죄 판결을 이끌어냈습니다.</p>
                            </div>
                        </div>
                        <div class="success-card">
                            <div class="card-header">
                                <span class="card-tag">집행유예</span>
                                <h3>특수상해 사건<br>집행유예 선고</h3>
                            </div>
                            <div class="card-body">
                                <p>특수상해 혐의로 기소된 사건에서 피고인의 정상 참작 사유를 적극 주장하여 실형이 아닌 집행유예 판결을 받았습니다.</p>
                            </div>
                        </div>
                        <div class="success-card">
                            <div class="card-header">
                                <span class="card-tag">불기소 처분</span>
                                <h3>사기 혐의<br>불기소 처분</h3>
                            </div>
                            <div class="card-body">
                                <p>사기 혐의로 고소된 사건에서 명확한 법리 검토와 증거 제출로 검찰의 불기소 처분을 받았습니다.</p>
                            </div>
                        </div>
                        <div class="success-card">
                            <div class="card-header">
                                <span class="card-tag">승소 판결</span>
                                <h3>민사 손해배상<br>원고 승소</h3>
                            </div>
                            <div class="card-body">
                                <p>손해배상 청구 소송에서 피해 사실을 명확히 입증하여 법원으로부터 전액 배상 판결을 받았습니다.</p>
                            </div>
                        </div>
                        <div class="success-card">
                            <div class="card-header">
                                <span class="card-tag">합의 성공</span>
                                <h3>교통사고<br>원만한 합의</h3>
                            </div>
                            <div class="card-body">
                                <p>교통사고 피해 사건에서 보험사와의 협상을 통해 의뢰인에게 유리한 조건으로 합의했습니다.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Press Coverage Section -->
    <section class="press-coverage">
        <div class="container">
            <div class="press-layout">
                <div class="press-intro">
                    <p class="section-label-text">언론보도</p>
                    <h2>PRESS COVERAGE</h2>
                    <p class="section-desc">파노의 언론소식을 만나보세요.</p>
                    <div class="slider-controls">
                        <button class="slider-arrow prev" onclick="movePressSlide(-1)">←</button>
                        <button class="slider-arrow next" onclick="movePressSlide(1)">→</button>
                    </div>
                    <div class="btn-more-wrapper">
                        <a href="/news.php" class="btn-more-link">
                            <span>더 알아보기</span>
                            <button class="btn-circle-arrow">→</button>
                        </a>
                    </div>
                </div>
                <div class="press-content">
                    <div class="press-cards">
                        <div class="press-card">
                            <div class="card-image"></div>
                            <div class="card-header">
                                <span class="card-tag">언론보도</span>
                                <h3>현행범체포<br>구속영장청구 기각</h3>
                            </div>
                        </div>
                        <div class="press-card">
                            <div class="card-image"></div>
                            <div class="card-header">
                                <span class="card-tag">언론보도</span>
                                <h3>법무법인 파노<br>전문성 인정</h3>
                            </div>
                        </div>
                        <div class="press-card">
                            <div class="card-image"></div>
                            <div class="card-header">
                                <span class="card-tag">언론보도</span>
                                <h3>공익활동 참여<br>사회공헌 활동</h3>
                            </div>
                        </div>
                        <div class="press-card">
                            <div class="card-image"></div>
                            <div class="card-header">
                                <span class="card-tag">언론보도</span>
                                <h3>형사전문 변호사<br>업계 주목</h3>
                            </div>
                        </div>
                        <div class="press-card">
                            <div class="card-image"></div>
                            <div class="card-header">
                                <span class="card-tag">언론보도</span>
                                <h3>의료 분쟁 해결<br>전문성 강화</h3>
                            </div>
                        </div>
                        <div class="press-card">
                            <div class="card-image"></div>
                            <div class="card-header">
                                <span class="card-tag">언론보도</span>
                                <h3>기업법무 강화<br>법률 파트너</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Practice Areas Section -->
    <section class="practice-areas">
        <div class="container">
            <div class="practice-header">
                <p class="section-label-text">업무분야</p>
                <h2>PRACTICE AREAS</h2>
                <div class="btn-more-wrapper">
                    <a href="/field.php" class="btn-more-link">
                        <span>더 알아보기</span>
                        <button class="btn-circle-arrow">
                            <img src="/images/right_w.svg" alt="더 알아보기">
                        </button>
                    </a>
                </div>
            </div>
            <div class="practice-grid">
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/icon_criminal.png" alt="형사">
                    </div>
                    <h3>형사</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/icon_medical.png" alt="의료">
                    </div>
                    <h3>의료</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/icon_finance.png" alt="금융·경제">
                    </div>
                    <h3>금융<span class="dot">·</span>경제</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/icon_bankruptcy.png" alt="도산">
                    </div>
                    <h3>도산(회생·파산)</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/icon_admin.png" alt="행정">
                    </div>
                    <h3>행정</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Consultation Request Section -->
    <section class="consultation">
        <div class="container">
            <div class="consultation-wrapper">
                <div class="consultation-text">
                    <p class="consultation-label">상담신청</p>
                    <h2>CONSULTATION REQUEST</h2>
                    <p class="consultation-subtitle">법률 상담부터 해결까지 파노 법률사무소가 도와드립니다.</p>
                </div>
                
                <form id="consultationForm" class="consultation-form" method="POST" action="/api/submit_consultation.php">
                    <div class="form-row-horizontal">
                        <div class="form-field">
                            <label for="name">성함</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-field">
                            <label for="phone">연락처</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row-vertical">
                        <label for="content">내용</label>
                        <textarea id="content" name="content" rows="4" required placeholder="상담하실 내용을 자세히 입력해주세요."></textarea>
                    </div>
                    <div class="form-row-button">
                        <button type="submit" class="btn-submit">상담신청</button>
                    </div>
                </form>
            </div>
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
</main>

<script>
// Success Stories slider functionality
let currentSuccessSlide = 0;

function moveSuccessSlide(direction) {
    const cards = document.querySelectorAll('.success-card');
    const totalCards = cards.length;

    currentSuccessSlide += direction;

    if (currentSuccessSlide < 0) {
        currentSuccessSlide = 0;
    } else if (currentSuccessSlide > totalCards - 3) {
        currentSuccessSlide = totalCards - 3;
    }

    const container = document.querySelector('.success-cards');
    const cardWidth = cards[0].offsetWidth;
    const gap = 28;
    const offset = currentSuccessSlide * (cardWidth + gap);
    container.style.transform = `translateX(-${offset}px)`;
}

// Press slider functionality
let currentPressSlide = 0;

function movePressSlide(direction) {
    const cards = document.querySelectorAll('.press-card');
    const totalCards = cards.length;

    currentPressSlide += direction;

    if (currentPressSlide < 0) {
        currentPressSlide = 0;
    } else if (currentPressSlide > totalCards - 3) {
        currentPressSlide = totalCards - 3;
    }

    const container = document.querySelector('.press-cards');
    const cardWidth = cards[0].offsetWidth;
    const gap = 28;
    const offset = currentPressSlide * (cardWidth + gap);
    container.style.transform = `translateX(-${offset}px)`;
}

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
