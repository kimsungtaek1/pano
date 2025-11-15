<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'includes/db.php';

// 현재 탭 확인
$tab = $_GET['tab'] ?? 'cases';

// 성공사례 탭 데이터
$cases_page = isset($_GET['cases_page']) ? (int)$_GET['cases_page'] : 1;
$cases_per_page = 9;
$cases_offset = ($cases_page - 1) * $cases_per_page;

$cases_where_sql = "WHERE is_published = 1 AND category = '최근 업무사례'";

$cases_count_sql = "SELECT COUNT(*) FROM news $cases_where_sql";
$cases_total = $pdo->query($cases_count_sql)->fetchColumn();
$cases_total_pages = ceil($cases_total / $cases_per_page);

$cases_sql = "SELECT * FROM news $cases_where_sql ORDER BY news_date DESC, created_at DESC LIMIT $cases_per_page OFFSET $cases_offset";
$cases_list = $pdo->query($cases_sql)->fetchAll();

// 언론보도 탭 데이터
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';

$where = ["is_published = 1", "category = '언론보도'"];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_sql = "WHERE " . implode(" AND ", $where);

$count_sql = "SELECT COUNT(*) FROM news $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

$sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news_list = $stmt->fetchAll();

include 'includes/header.php';
?>

<main>
    <!-- Top Image Section -->
    <section class="intro-hero">
        <img src="/images/news.png" alt="소식" style="width: 100%; display: block;">
        <div class="intro-hero-text-container">
            <div class="container">
                <div class="intro-hero-text">
                    <p class="hero-subtitle">LAW FIRM PANO</p>
                    <h1 class="hero-title">소식</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Tab Buttons Section -->
    <section class="intro-tabs-section">
        <div class="container">
            <div class="intro-tab-buttons">
                <button class="intro-tab-btn <?php echo $tab === 'cases' ? 'active' : ''; ?>" data-tab="cases">파노 성공사례</button>
                <button class="intro-tab-btn <?php echo $tab === 'press' ? 'active' : ''; ?>" data-tab="press">언론보도</button>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="intro-content-section">
        <div class="container">
            <!-- 성공사례 탭 컨텐츠 -->
            <div class="intro-tab-content <?php echo $tab === 'cases' ? 'active' : ''; ?>" id="tab-cases">
                <div class="cases-grid">
                    <?php if (empty($cases_list)): ?>
                        <p style="text-align: center; padding: 60px 0; color: #999;">등록된 파노 성공사례가 없습니다.</p>
                    <?php else: ?>
                        <?php foreach ($cases_list as $case): ?>
                            <a href="news_detail.php?id=<?php echo $case['id']; ?>" class="case-card">
                                <div class="thumbnail"></div>
                                <div class="content">
                                    <span class="badge badge-red">구속영장 기각</span>
                                    <h3><?php echo htmlspecialchars($case['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($case['summary'] ?: mb_substr(strip_tags($case['content']), 0, 100) . '...'); ?></p>
                                    <span class="date"><?php echo date('Y.m.d', strtotime($case['news_date'])); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 페이지네이션 -->
                <?php if ($cases_total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $cases_total_pages; $i++): ?>
                            <a href="?tab=cases&cases_page=<?php echo $i; ?>"
                               class="page <?php echo $i === $cases_page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 언론보도 탭 컨텐츠 -->
            <div class="intro-tab-content <?php echo $tab === 'press' ? 'active' : ''; ?>" id="tab-press">
                <!-- 검색 영역 -->
                <section class="news-filter">
                    <form method="GET" class="search-box">
                        <input type="hidden" name="tab" value="press">
                        <input type="text" name="search" placeholder="검색" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="search-btn">🔍</button>
                    </form>
                </section>

                <!-- 뉴스 목록 -->
                <div class="news-grid">
                    <?php if (empty($news_list)): ?>
                        <p style="text-align: center; padding: 60px 0; color: #999;">등록된 뉴스가 없습니다.</p>
                    <?php else: ?>
                        <?php foreach ($news_list as $news): ?>
                            <a href="news_detail.php?id=<?php echo $news['id']; ?>" class="news-card">
                                <div class="thumbnail"></div>
                                <div class="content">
                                    <span class="badge badge-blue">언론보도</span>
                                    <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($news['summary'] ?: mb_substr(strip_tags($news['content']), 0, 100) . '...'); ?></p>
                                    <span class="date"><?php echo date('Y.m.d', strtotime($news['news_date'])); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 페이지네이션 -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?tab=press&page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>"
                               class="page <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<style>
/* 검색 영역 */
.news-filter {
    margin: 40px 0;
}

.search-box {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
}

.search-box input {
    flex: 1;
    padding: 12px 20px;
    border: 1px solid #e0e0e0;
    border-radius: 25px 0 0 25px;
    font-size: 14px;
}

.search-btn {
    padding: 12px 30px;
    background: #0066cc;
    color: white;
    border: none;
    border-radius: 0 25px 25px 0;
    cursor: pointer;
    font-size: 16px;
}

/* 뉴스 리스트 */
.news-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 40px 0;
}

.news-card {
    background: #fff;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    padding: 15px 0;
    text-decoration: none;
    display: flex;
    flex-direction: row;
    gap: 15px;
}

.news-card:hover {
    background: #fafafa;
}

.news-card .thumbnail {
    width: 120px;
    height: 180px;
    background: #e8e8e8;
    flex-shrink: 0;
}

.news-card .content {
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 8px;
}

.news-card .badge {
    align-self: flex-start;
}

.news-card h3 {
    font-size: 15px;
    color: #000;
    line-height: 1.5;
    font-weight: 500;
}

.news-card p {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
}

.news-card .date {
    font-size: 12px;
    color: #999;
}

.badge-blue {
    background: #fff;
    color: #333;
    border: 1px solid #333;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 400;
}

.badge-red {
    background: #2196F3;
    color: #fff;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 400;
}

/* 페이지네이션 */
.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 40px 0;
}

.pagination .page {
    padding: 8px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s;
}

.pagination .page:hover {
    background: #f5f5f5;
}

.pagination .page.active {
    background: #0066cc;
    color: white;
    border-color: #0066cc;
}

/* 성공사례 리스트 */
.cases-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin: 40px 0;
}

.case-card {
    background: #fff;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    padding: 15px 0;
    text-decoration: none;
    display: flex;
    flex-direction: row;
    gap: 15px;
}

.case-card:hover {
    background: #fafafa;
}

.case-card .thumbnail {
    width: 120px;
    height: 180px;
    background: #e8e8e8;
    flex-shrink: 0;
}

.case-card .content {
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 8px;
}

.case-card .badge {
    align-self: flex-start;
}

.case-card h3 {
    font-size: 15px;
    color: #000;
    line-height: 1.5;
    font-weight: 500;
}

.case-card p {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
}

.case-card .date {
    font-size: 12px;
    color: #999;
}

@media (max-width: 768px) {
    .news-card,
    .case-card {
        flex-direction: column;
        gap: 10px;
        padding: 15px;
    }

    .news-card .thumbnail,
    .case-card .thumbnail {
        width: 100%;
        height: 200px;
    }
}
</style>

<script>
// 탭 전환 기능
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.intro-tab-btn');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = button.getAttribute('data-tab');
            window.location.href = '?tab=' + tab;
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
