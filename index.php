<?php
include 'includes/db.php';

// 성공사례 (파노 성공사례) - 최신 6개
$success_sql = "SELECT * FROM news WHERE is_published = 1 AND category = '파노 성공사례' ORDER BY news_date DESC, created_at DESC LIMIT 6";
$success_list = $pdo->query($success_sql)->fetchAll();

// 언론보도 - 최신 6개
$press_sql = "SELECT * FROM news WHERE is_published = 1 AND category = '언론보도' ORDER BY news_date DESC, created_at DESC LIMIT 6";
$press_list = $pdo->query($press_sql)->fetchAll();

include 'includes/header.php';
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
                    <div class="controls-wrapper">
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
                </div>
                <div class="success-content">
                    <div class="success-cards">
                        <?php if (empty($success_list)): ?>
                            <div class="success-card">
                                <div class="card-header">
                                    <span class="card-tag">안내</span>
                                    <h3>등록된<br>성공사례가 없습니다</h3>
                                </div>
                                <div class="card-body">
                                    <p>곧 파노의 성공사례를 만나보실 수 있습니다.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($success_list as $success): ?>
                            <div class="success-card">
                                <div class="card-header">
                                    <?php if (!empty($success['case_type'])): ?>
                                    <span class="card-tag"><?php echo htmlspecialchars($success['case_type']); ?></span>
                                    <?php endif; ?>
                                    <h3><?php echo nl2br(htmlspecialchars($success['title'])); ?></h3>
                                </div>
                                <div class="card-body">
                                    <p><?php echo htmlspecialchars($success['summary'] ?: mb_substr(strip_tags($success['content']), 0, 80) . '...'); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                    <div class="controls-wrapper">
                        <div class="slider-controls">
                            <button class="slider-arrow prev" onclick="movePressSlide(-1)">
                                <img src="/images/left.svg" alt="이전">
                            </button>
                            <button class="slider-arrow next" onclick="movePressSlide(1)">
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
                </div>
                <div class="press-content">
                    <div class="press-cards">
                        <?php if (empty($press_list)): ?>
                            <div class="press-card">
                                <div class="card-image"></div>
                                <div class="card-header">
                                    <span class="card-tag">안내</span>
                                    <h3>등록된<br>언론보도가 없습니다</h3>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($press_list as $press):
                                $press_images = !empty($press['image_urls']) ? json_decode($press['image_urls'], true) : [];
                                $press_thumb = !empty($press_images[0]) ? $press_images[0] : '';
                            ?>
                            <div class="press-card">
                                <div class="card-image"<?php if ($press_thumb): ?> style="background-image: url('<?php echo htmlspecialchars($press_thumb); ?>'); background-size: cover; background-position: center;"<?php endif; ?>></div>
                                <div class="card-header">
                                    <span class="card-tag">언론보도</span>
                                    <h3><?php echo nl2br(htmlspecialchars($press['title'])); ?></h3>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                        <img src="/images/practice1.png" alt="형사">
                    </div>
                    <h3>형사</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/practice2.png" alt="의료">
                    </div>
                    <h3>의료</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/practice3.png" alt="금융·경제">
                    </div>
                    <h3>금융<span class="dot">·</span>경제</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/practice4.png" alt="도산">
                    </div>
                    <h3>도산(회생·파산)</h3>
                </div>
                <div class="practice-card">
                    <div class="practice-icon">
                        <img src="/images/practice5.png" alt="행정">
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
                    <p class="consultation-subtitle">법률 상담부터 해결까지<br class="mobile-br"> 파노 법률사무소가 도와드립니다.</p>
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
                        <textarea id="content" name="content" rows="4" required placeholder="문의 내용을 입력해주세요."></textarea>
                    </div>
                    <div class="form-row-button">
                        <button type="submit" class="btn-submit">상담신청</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>

<script>
// Success Stories slider functionality
let currentSuccessSlide = 0;
let successSliderInterval;

function moveSuccessSlide(direction) {
    const cards = document.querySelectorAll('.success-card');
    const totalCards = cards.length;
    const isMobile = window.innerWidth <= 768;
    const cardsPerView = isMobile ? 1 : 3;

    currentSuccessSlide += direction;

    if (currentSuccessSlide < 0) {
        currentSuccessSlide = 0;
    } else if (currentSuccessSlide > totalCards - cardsPerView) {
        currentSuccessSlide = totalCards - cardsPerView;
    }

    const container = document.querySelector('.success-cards');
    const cardWidth = cards[0].offsetWidth;
    const gap = isMobile ? 20 : 28;
    const offset = currentSuccessSlide * (cardWidth + gap);
    container.style.transform = `translateX(-${offset}px)`;
}

function autoMoveSuccessSlide() {
    const cards = document.querySelectorAll('.success-card');
    const totalCards = cards.length;
    const isMobile = window.innerWidth <= 768;
    const cardsPerView = isMobile ? 1 : 3;

    currentSuccessSlide++;

    if (currentSuccessSlide > totalCards - cardsPerView) {
        currentSuccessSlide = 0;
    }

    const container = document.querySelector('.success-cards');
    const cardWidth = cards[0].offsetWidth;
    const gap = isMobile ? 20 : 28;
    const offset = currentSuccessSlide * (cardWidth + gap);
    container.style.transform = `translateX(-${offset}px)`;
}

// 성공사례 자동 슬라이드 시작
successSliderInterval = setInterval(autoMoveSuccessSlide, 3000);

// 마우스 오버 시 자동 슬라이드 중지, 마우스 아웃 시 재시작
document.querySelector('.success-stories')?.addEventListener('mouseenter', function() {
    clearInterval(successSliderInterval);
});

document.querySelector('.success-stories')?.addEventListener('mouseleave', function() {
    successSliderInterval = setInterval(autoMoveSuccessSlide, 3000);
});

// Press slider functionality
let currentPressSlide = 0;
let pressSliderInterval;

function movePressSlide(direction) {
    const cards = document.querySelectorAll('.press-card');
    const totalCards = cards.length;
    const isMobile = window.innerWidth <= 768;
    const cardsPerView = isMobile ? 1 : 3;

    currentPressSlide += direction;

    if (currentPressSlide < 0) {
        currentPressSlide = 0;
    } else if (currentPressSlide > totalCards - cardsPerView) {
        currentPressSlide = totalCards - cardsPerView;
    }

    const container = document.querySelector('.press-cards');
    const cardWidth = cards[0].offsetWidth;
    const gap = isMobile ? 20 : 28;
    const offset = currentPressSlide * (cardWidth + gap);
    container.style.transform = `translateX(-${offset}px)`;
}

function autoMovePressSlide() {
    const cards = document.querySelectorAll('.press-card');
    const totalCards = cards.length;
    const isMobile = window.innerWidth <= 768;
    const cardsPerView = isMobile ? 1 : 3;

    currentPressSlide++;

    if (currentPressSlide > totalCards - cardsPerView) {
        currentPressSlide = 0;
    }

    const container = document.querySelector('.press-cards');
    const cardWidth = cards[0].offsetWidth;
    const gap = isMobile ? 20 : 28;
    const offset = currentPressSlide * (cardWidth + gap);
    container.style.transform = `translateX(-${offset}px)`;
}

// 언론보도 자동 슬라이드 시작
pressSliderInterval = setInterval(autoMovePressSlide, 3000);

// 마우스 오버 시 자동 슬라이드 중지, 마우스 아웃 시 재시작
document.querySelector('.press-coverage')?.addEventListener('mouseenter', function() {
    clearInterval(pressSliderInterval);
});

document.querySelector('.press-coverage')?.addEventListener('mouseleave', function() {
    pressSliderInterval = setInterval(autoMovePressSlide, 3000);
});

// 화면 크기 변경 시 슬라이드 위치 재조정
window.addEventListener('resize', function() {
    moveSuccessSlide(0);
    movePressSlide(0);
});

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
const fixedForm = document.getElementById('fixedConsultationForm');
if (fixedForm) {
    fixedForm.addEventListener('submit', function(e) {
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
}
</script>

<?php include 'includes/footer.php'; ?>
