<?php
include 'includes/header.php';
include 'includes/db.php';

// 최신 뉴스 3개 가져오기
$stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$news_list = $stmt->fetchAll();
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
                <h1>PANO</h1>
                <p class="subtitle">Song Dong Min</p>
                <p class="description">의뢰인의 믿음과 신뢰를 받을 수 있도록 최선을 다하겠습니다</p>
            </div>
        </div>
    </section>

    <!-- 40년 전통의 법률사무소 -->
    <section class="tradition">
        <div class="container">
            <h2>40년 전통의 법률사무소</h2>
            <p>전문변호 임무와 30년 이상, 귀하의 재산분쟁<br>고객에게 최선의 결과를 제공할 수 있게 최선을 다합니다</p>
            <a href="/intro.php" class="btn-primary">법률사무소소개</a>
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
                <div class="service-item">
                    <img src="/images/service-civil.jpg" alt="민사재판">
                    <h3>민사재판</h3>
                </div>
                <div class="service-item">
                    <img src="/images/service-criminal.jpg" alt="형사재판">
                    <h3>형사재판</h3>
                </div>
                <div class="service-item">
                    <img src="/images/service-family.jpg" alt="조정중재">
                    <h3>조정중재</h3>
                </div>
                <div class="service-item">
                    <img src="/images/service-admin.jpg" alt="기업상담">
                    <h3>기업상담</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- 뉴스 섹션 -->
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

    <!-- 상담문의 섹션 -->
    <section class="consultation">
        <div class="container">
            <div class="consultation-grid">
                <div class="consultation-item">
                    <p class="label">문의하기</p>
                    <h3>고객님께 전담변호사를 배정하여<br>법률 상담부터 해결까지 도와드립니다.</h3>
                    <a href="/contact.php" class="btn-outline-white">무료상담 신청</a>
                </div>
                <div class="consultation-item">
                    <p class="label">상담안내</p>
                    <h3>파노 법률사무소의 법률 및 법무 문의<br>확인 문의를 기다리고 계십니다.</h3>
                    <a href="/guide.php" class="btn-outline-white">예약 상담안내</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
