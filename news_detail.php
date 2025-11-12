<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

// 뉴스 ID 가져오기
$id = $_GET['id'] ?? 0;

if (!$id) {
    header('Location: news.php');
    exit;
}

// 뉴스 상세 조회
$sql = "SELECT * FROM news WHERE id = ? AND is_published = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$news = $stmt->fetch();

if (!$news) {
    header('Location: news.php');
    exit;
}

// 카테고리별 배지 색상
function getBadgeClass($category) {
    if ($category === '최근 업무사례') return 'badge-red';
    if ($category === '언론보도') return 'badge-blue';
    return 'badge-blue';
}

include 'includes/header.php';
?>

<main>
    <!-- 페이지 타이틀 -->
    <section class="page-title">
        <div class="container">
            <h1>파노소식</h1>
        </div>
    </section>

    <!-- 뉴스 상세 내용 -->
    <section class="news-detail">
        <div class="container">
            <div class="news-detail-content">
                <div class="news-detail-header">
                    <span class="badge <?php echo getBadgeClass($news['category']); ?>">
                        <?php echo htmlspecialchars($news['category']); ?>
                    </span>
                    <h2><?php echo htmlspecialchars($news['title']); ?></h2>
                    <span class="date"><?php echo date('Y.m.d', strtotime($news['news_date'])); ?></span>
                </div>

                <?php if ($news['image']): ?>
                    <div class="news-detail-image">
                        <img src="<?php echo htmlspecialchars($news['image']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                    </div>
                <?php endif; ?>

                <div class="news-detail-body">
                    <?php echo $news['content']; ?>
                </div>

                <div class="news-detail-actions">
                    <a href="news.php" class="btn-back">목록으로</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.news-detail {
    padding: 60px 0;
}

.news-detail-content {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.news-detail-header {
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.news-detail-header .badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 15px;
}

.badge-red {
    background: #dc3545;
    color: white;
}

.badge-blue {
    background: #0066cc;
    color: white;
}

.news-detail-header h2 {
    font-size: 28px;
    color: #333;
    margin: 0 0 15px 0;
    line-height: 1.4;
}

.news-detail-header .date {
    color: #999;
    font-size: 14px;
}

.news-detail-image {
    margin: 30px 0;
    text-align: center;
}

.news-detail-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.news-detail-body {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
    margin-bottom: 40px;
}

.news-detail-body p {
    margin-bottom: 20px;
}

.news-detail-body img {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
}

.news-detail-actions {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid #f0f0f0;
}

.btn-back {
    display: inline-block;
    padding: 12px 30px;
    background: #333;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 15px;
    transition: background 0.3s;
}

.btn-back:hover {
    background: #555;
}

@media (max-width: 768px) {
    .news-detail-content {
        padding: 30px 20px;
    }

    .news-detail-header h2 {
        font-size: 22px;
    }

    .news-detail-body {
        font-size: 15px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
