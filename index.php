<?php
include 'includes/header.php';
include 'includes/db.php';

// 최신 뉴스 3개 가져오기
/*
$stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$news_list = $stmt->fetchAll();
*/
?>

<main>
    <!-- 메인 비주얼 -->
    <section class="hero">
        <div class="hero-slide active">
            <img src="/images/1.jpg" alt="배경 1">
        </div>
        <div class="hero-slide">
            <img src="/images/2.jpg" alt="배경 2">
        </div>
        <div class="hero-slide">
            <img src="/images/3.jpg" alt="배경 3">
        </div>
        <div class="hero-slide">
            <img src="/images/4.jpg" alt="배경 4">
        </div>
        <div class="hero-overlay">
            <div class="hero-content">
                <img src="/images/logo.png" alt="PANO" class="hero-logo">
                <p class="subtitle">Song Dong Min</p>
                <p class="description">의뢰인의 믿음과 신뢰를 받을 수 있도록 최선을 다하겠습니다</p>
            </div>
        </div>
    </section>

    <!-- 업무분야 -->
    <section class="services">
        <div class="container">
            <div class="services-intro">
                <p>의뢰인의 믿음과</p>
                <h2>맞춤나 신속한 절차법<br>법률서비스를<br>제공합니다.</h2>
                <a href="/field.php" class="btn-outline">업무분야안내</a>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">⚖️</div>
                    <h3>민사재판</h3>
                    <p>민사 분쟁 및 소송 전문 법률 서비스를 제공합니다</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🏛️</div>
                    <h3>형사재판</h3>
                    <p>형사 사건 변호 및 법률 상담을 제공합니다</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">🤝</div>
                    <h3>조정중재</h3>
                    <p>분쟁 조정 및 중재를 통한 해결을 지원합니다</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">💼</div>
                    <h3>기업상담</h3>
                    <p>기업 법무 자문 및 컨설팅을 제공합니다</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 뉴스 섹션 -->
    <!--
    <section class="news">
        <div class="container">
            <div class="section-header">
                <h2>소식</h2>
                <a href="/news.php" class="more">더보기 ›</a>
            </div>
            <div class="news-grid">
                <?php foreach ($news_list as $news): ?>
                <a href="news_detail.php?id=<?php echo $news['id']; ?>" class="news-item">
                    <span class="badge <?php echo $news['category'] === '중요' ? 'badge-red' : 'badge-blue'; ?>">
                        <?php echo htmlspecialchars($news['category']); ?>
                    </span>
                    <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                    <p><?php echo htmlspecialchars(mb_substr($news['content'], 0, 100)); ?>...</p>
                    <span class="date"><?php echo date('Y-m-d', strtotime($news['created_at'])); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    -->

    <!-- 상담문의 섹션 -->
    <section class="consultation">
        <div class="container">
            <div class="consultation-header">
                <p class="label">문의하기</p>
                <h2>고객님께 전담변호사를 배정하여<br>법률 상담부터 해결까지 도와드립니다.</h2>
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
<!-- Floating Action Buttons -->
<div class="floating-buttons">
    <a href="https://pf.kakao.com/_Exaaxib/chat" target="_blank" class="floating-btn kakao" title="카카오톡 상담">
        <img src="/images/kakao.png" alt="카카오톡">
    </a>
    <a href="tel:010-5633-1803" class="floating-btn phone" title="전화 상담">
        <img src="/images/phone.png" alt="전화">
    </a>
    <a href="#" class="floating-btn consultation" title="상담 신청" onclick="scrollToConsultation(event)">
        📝
    </a>
</div>

</main>

<script>
document.getElementById('consultationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('.btn-submit');

    // 버튼 비활성화
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
</script>

<script>
// 상담신청 섹션으로 스크롤
function scrollToConsultation(event) {
    event.preventDefault();
    const consultationSection = document.querySelector('.consultation');
    if (consultationSection) {
        consultationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<?php include 'includes/footer.php'; ?>
