<?php
require_once 'includes/db.php';

// 페이지네이션 설정
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// 검색 및 필터
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// WHERE 조건 구성
$where = ["is_published = 1"];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $where[] = "category = ?";
    $params[] = $category;
}

$where_sql = "WHERE " . implode(" AND ", $where);

// 전체 개수 조회
$count_sql = "SELECT COUNT(*) FROM news $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// 뉴스 목록 조회
$sql = "SELECT * FROM news $where_sql ORDER BY news_date DESC, created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news_list = $stmt->fetchAll();

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

    <!-- 필터 영역 -->
    <section class="news-filter">
        <div class="container">
            <div class="filter-tabs">
                <a href="?category=" class="tab <?php echo empty($category) ? 'active' : ''; ?>">전체</a>
                <a href="?category=언론보도" class="tab <?php echo $category === '언론보도' ? 'active' : ''; ?>">언론보도</a>
                <a href="?category=최근 업무사례" class="tab <?php echo $category === '최근 업무사례' ? 'active' : ''; ?>">최근 업무사례</a>
            </div>
            <form method="GET" class="search-box">
                <?php if (!empty($category)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <?php endif; ?>
                <input type="text" name="search" placeholder="검색" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">🔍</button>
            </form>
        </div>
    </section>

    <!-- 뉴스 그리드 -->
    <section class="news-list">
        <div class="container">
            <div class="news-grid">
                <?php if (empty($news_list)): ?>
                    <p style="text-align: center; padding: 60px 0; color: #999;">등록된 뉴스가 없습니다.</p>
                <?php else: ?>
                    <?php foreach ($news_list as $news): ?>
                        <div class="news-card">
                            <span class="badge <?php echo getBadgeClass($news['category']); ?>">
                                <?php echo htmlspecialchars($news['category']); ?>
                            </span>
                            <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                            <p><?php echo htmlspecialchars($news['summary'] ?: mb_substr(strip_tags($news['content']), 0, 100) . '...'); ?></p>
                            <span class="date"><?php echo date('Y.m.d', strtotime($news['news_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- 페이지네이션 -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>"
                           class="page <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
